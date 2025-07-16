<?php
/*!
@file admin_management.php
@brief 管理者管理画面
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
	public $login_admin;
	public $db;
	public $admins;
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
		$this->login_admin = null;
		$this->db = null;
		$this->admins = array();
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
		cutil::post_default("administer_name",'');
		cutil::post_default("administer_mailaddress",'');
		cutil::post_default("administer_password",'');
		cutil::post_default("delete_admin_id",'');
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
		$this->login_admin = get_login_admin();
		
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
				case 'add_admin':
					$this->add_admin();
				break;
				case 'delete_admin':
					$this->delete_admin();
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		
		// 管理者一覧取得
		$this->get_admins();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	管理者追加処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function add_admin(){
		$name = trim($_POST['administer_name']);
		$mail = trim($_POST['administer_mailaddress']);
		$pass = $_POST['administer_password'];
		
		if(empty($name)){
			$this->error = '管理者名を入力してください。';
			return;
		}
		
		if(empty($mail)){
			$this->error = 'メールアドレスを入力してください。';
			return;
		}
		
		if(empty($pass)){
			$this->error = 'パスワードを入力してください。';
			return;
		}
		
		try {
			$stmt = $this->db->prepare('INSERT INTO administers (administer_name, administer_mailaddress, administer_password) VALUES (?, ?, ?)');
			$result = $stmt->execute([$name, $mail, sha1($pass)]);
			if($result){
				$this->success = '管理者を追加しました。';
				// フォームリセット
				$_POST['administer_name'] = '';
				$_POST['administer_mailaddress'] = '';
				$_POST['administer_password'] = '';
			} else {
				$this->error = '管理者の追加に失敗しました。';
			}
		} catch (Exception $e) {
			$this->error = 'データベースエラーが発生しました: ' . $e->getMessage();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	管理者削除処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function delete_admin(){
		$delete_id = intval($_POST['delete_admin_id']);
		
		if($delete_id <= 0){
			$this->error = '無効な管理者IDです。';
			return;
		}
		
		// 自分自身は削除できない
		if($this->login_admin && $this->login_admin['auid'] == $delete_id){
			$this->error = '自分自身は削除できません。';
			return;
		}
		
		try {
			$stmt = $this->db->prepare('DELETE FROM administers WHERE administer_id = ?');
			$result = $stmt->execute([$delete_id]);
			if($result){
				$this->success = '管理者を削除しました。';
			} else {
				$this->error = '管理者の削除に失敗しました。';
			}
		} catch (Exception $e) {
			$this->error = 'データベースエラーが発生しました: ' . $e->getMessage();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	管理者一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_admins(){
		try {
			$stmt = $this->db->prepare('SELECT administer_id, administer_name, administer_mailaddress FROM administers ORDER BY administer_id DESC');
			$stmt->execute();
			$this->admins = $stmt->fetchAll();
		} catch (Exception $e) {
			$this->error = '管理者一覧の取得に失敗しました。';
			$this->admins = array();
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
	@brief	管理者一覧テーブルの取得
	@return	管理者一覧テーブル文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_admins_table(){
		$table_str = '';
		foreach($this->admins as $admin){
			$admin_id = $admin['administer_id'];
			$admin_name = display($admin['administer_name']);
			$admin_mail = display($admin['administer_mailaddress']);
			
			$delete_button = '';
			if($this->login_admin && $this->login_admin['auid'] != $admin_id){
				$delete_button = '<button type="button" class="btn btn-danger btn-sm" onClick="delete_admin(' . $admin_id . ')">削除</button>';
			} else {
				$delete_button = '<span class="text-muted">自分自身は削除できません</span>';
			}
			
			$table_str .= <<<END_BLOCK
<tr>
    <td>{$admin_name}</td>
    <td>{$admin_mail}</td>
    <td>{$delete_button}</td>
</tr>
END_BLOCK;
		}
		return $table_str;
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
    <title>管理者管理</title>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    管理者を追加
                </button>
            </div>
            <h2>管理者一覧</h2>
            <table class="table table-striped table-hover mt-3">
                <thead class="table-light section-header">
                    <tr>
                        <th>管理者名</th>
                        <th>メールアドレス</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?= $this->get_admins_table(); ?>
                </tbody>
            </table>
        </div>

        <!-- 管理者追加モーダル -->
        <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdminModalLabel">管理者を追加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="administer_name" class="form-label">管理者名</label>
                            <input type="text" class="form-control" id="administer_name" name="administer_name" value="<?= display($_POST['administer_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="administer_mailaddress" class="form-label">メールアドレス</label>
                            <input type="email" class="form-control" id="administer_mailaddress" name="administer_mailaddress" value="<?= display($_POST['administer_mailaddress']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="administer_password" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="administer_password" name="administer_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="button" class="btn btn-primary" onClick="set_func_form('add_admin','')">追加</button>
                    </div>
                    <input type="hidden" name="func" value="" />
                    <input type="hidden" name="param" value="" />
                </form>
            </div>
        </div>
        
        <!-- 削除確認用フォーム -->
        <form name="form2" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" style="display:none;">
            <input type="hidden" name="delete_admin_id" id="delete_admin_id" value="">
            <input type="hidden" name="func" value="delete_admin" />
            <input type="hidden" name="param" value="" />
        </form>
    </main>
    
    <script>
    function set_func_form(func, param) {
        document.form1.func.value = func;
        document.form1.param.value = param;
        document.form1.submit();
    }
    
    function delete_admin(admin_id) {
        if(confirm('本当に削除しますか？')) {
            document.getElementById('delete_admin_id').value = admin_id;
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