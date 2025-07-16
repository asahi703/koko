<?php
/*!
@file community_management.php
@brief コミュニティ管理画面
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("../main/common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $admin;
	public $db;
	public $communities;
	public $users;
	public $error;
	public $success;
	
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
		$this->admin = null;
		$this->db = null;
		$this->communities = array();
		$this->users = array();
		$this->error = '';
		$this->success = '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("community_name",'');
		cutil::post_default("community_description",'');
		cutil::post_default("community_owner",'');
		cutil::post_default("delete_community_id",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// 管理者セッション確認
		if(!is_admin_logged_in()){
			cutil::redirect_exit('index.php');
		}
		$this->admin = get_admin_user();
		
		// DB接続
		require_once(__DIR__ . '/../main/common/dbmanager.php');
		$this->db = new cdb();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  本体実行（表示前処理）
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo 'ページが無効です';
			exit();
		}
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'add_community':
					$this->add_community();
				break;
				case 'delete_community':
					$this->delete_community();
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		
		// コミュニティ一覧取得
		$this->get_communities();
		// ユーザー一覧取得
		$this->get_users();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ追加処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function add_community(){
		$name = trim($_POST['community_name']);
		$description = trim($_POST['community_description']);
		$owner = intval($_POST['community_owner']);
		
		if(empty($name)){
			$this->error = 'コミュニティ名を入力してください。';
			return;
		}
		
		if($owner <= 0){
			$this->error = 'オーナーのユーザーを選択してください。';
			return;
		}
		
		try {
			$stmt = $this->db->prepare('INSERT INTO communities (community_name, community_description, community_owner) VALUES (?, ?, ?)');
			$result = $stmt->execute([$name, $description, $owner]);
			if($result){
				$this->success = 'コミュニティを追加しました。';
				// フォームリセット
				$_POST['community_name'] = '';
				$_POST['community_description'] = '';
				$_POST['community_owner'] = '';
			} else {
				$this->error = 'コミュニティの追加に失敗しました。';
			}
		} catch (Exception $e) {
			$this->error = 'データベースエラーが発生しました: ' . $e->getMessage();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ削除処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function delete_community(){
		$delete_id = intval($_POST['delete_community_id']);
		
		if($delete_id <= 0){
			$this->error = '無効なコミュニティIDです。';
			return;
		}
		
		try {
			$stmt = $this->db->prepare('DELETE FROM communities WHERE community_id = ?');
			$result = $stmt->execute([$delete_id]);
			if($result){
				$this->success = 'コミュニティを削除しました。';
			} else {
				$this->error = 'コミュニティの削除に失敗しました。';
			}
		} catch (Exception $e) {
			$this->error = 'データベースエラーが発生しました: ' . $e->getMessage();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_communities(){
		try {
			$stmt = $this->db->prepare('
				SELECT c.community_id, c.community_name, c.community_description, u.user_name AS owner_name
				FROM communities c
				LEFT JOIN users u ON c.community_owner = u.user_id
				ORDER BY c.community_id DESC
			');
			$stmt->execute();
			$this->communities = $stmt->fetchAll();
		} catch (Exception $e) {
			$this->error = 'コミュニティ一覧の取得に失敗しました。';
			$this->communities = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_users(){
		try {
			$stmt = $this->db->prepare('SELECT user_id, user_name FROM users ORDER BY user_name');
			$stmt->execute();
			$this->users = $stmt->fetchAll();
		} catch (Exception $e) {
			$this->users = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー表示の取得
	@return	エラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_error_display(){
		if(!empty($this->error)){
			return '<div class="alert alert-danger mt-3">' . display($this->error) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	成功メッセージ表示の取得
	@return	成功メッセージ表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_success_display(){
		if(!empty($this->success)){
			return '<div class="alert alert-success mt-3">' . display($this->success) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ一覧テーブルの取得
	@return	コミュニティ一覧テーブル文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_communities_table(){
		$table_str = '';
		foreach($this->communities as $community){
			$community_id = $community['community_id'];
			$community_name = display($community['community_name']);
			$community_description = display($community['community_description']);
			$owner_name = display($community['owner_name'] ?? '');
			
			$table_str .= <<<END_BLOCK
<tr>
    <td>{$community_name}</td>
    <td>{$community_description}</td>
    <td>{$owner_name}</td>
    <td>
        <button type="button" class="btn btn-danger btn-sm" onClick="delete_community({$community_id})">削除</button>
    </td>
</tr>
END_BLOCK;
		}
		return $table_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー選択オプションの取得
	@return	ユーザー選択オプション文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_user_options(){
		$options_str = '<option value="">選択してください</option>';
		foreach($this->users as $user){
			$user_id = $user['user_id'];
			$user_name = display($user['user_name']);
			$selected = ($_POST['community_owner'] == $user_id) ? 'selected' : '';
			$options_str .= '<option value="' . $user_id . '" ' . $selected . '>' . $user_name . '</option>';
		}
		return $options_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロック終了
?>
<!-- コンテンツ　-->
<head>
    <title>コミュニティ管理</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content-wrapper">
        <div class="container bg-white rounded shadow p-4">
            <?= $this->get_error_display(); ?>
            <?= $this->get_success_display(); ?>
            
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommunityModal">
                    コミュニティを追加
                </button>
            </div>
            <h2>コミュニティ一覧</h2>
            <table class="table table-striped table-hover mt-3">
                <thead class="table-light section-header">
                    <tr>
                        <th>コミュニティ名</th>
                        <th>説明</th>
                        <th>オーナーのユーザー</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?= $this->get_communities_table(); ?>
                </tbody>
            </table>
        </div>

        <!-- コミュニティ追加モーダル -->
        <div class="modal fade" id="addCommunityModal" tabindex="-1" aria-labelledby="addCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCommunityModalLabel">コミュニティを追加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="community_name" class="form-label">コミュニティ名</label>
                            <input type="text" class="form-control" id="community_name" name="community_name" value="<?= display($_POST['community_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="community_description" class="form-label">説明</label>
                            <textarea class="form-control" id="community_description" name="community_description"><?= display($_POST['community_description']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="community_owner" class="form-label">オーナーのユーザー</label>
                            <select class="form-select" id="community_owner" name="community_owner" required>
                                <?= $this->get_user_options(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="button" class="btn btn-primary" onClick="set_func_form('add_community','')">追加</button>
                    </div>
                    <input type="hidden" name="func" value="" />
                    <input type="hidden" name="param" value="" />
                </form>
            </div>
        </div>
        
        <!-- 削除確認用フォーム -->
        <form name="form2" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" style="display:none;">
            <input type="hidden" name="delete_community_id" id="delete_community_id" value="">
            <input type="hidden" name="func" value="delete_community" />
            <input type="hidden" name="param" value="" />
        </form>
    </main>
    
    <script>
    function set_func_form(func, param) {
        document.form1.func.value = func;
        document.form1.param.value = param;
        document.form1.submit();
    }
    
    function delete_community(community_id) {
        if(confirm('本当に削除しますか？')) {
            document.getElementById('delete_community_id').value = community_id;
            document.form2.submit();
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
//PHPブロック再開
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//ページを作成
$page_obj = new cnode();
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処理
$page_obj->create();
//POST変数のデフォルト値をセット
$main_obj->post_default();
//本体実行（表示前処理）
$main_obj->execute();
//ページ全体を表示
$page_obj->display();

?>