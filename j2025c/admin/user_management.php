<?php
/*!
@file user_management.php
@brief ユーザー管理画面
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
		cutil::post_default("user_name",'');
		cutil::post_default("user_mailaddress",'');
		cutil::post_default("user_password",'');
		cutil::post_default("user_is_teacher",'');
		cutil::post_default("delete_user_id",'');
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
				case 'add_user':
					$this->add_user();
				break;
				case 'delete_user':
					$this->delete_user();
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		
		// ユーザー一覧取得
		$this->get_users();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー追加処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function add_user(){
		$name = trim($_POST['user_name']);
		$mail = trim($_POST['user_mailaddress']);
		$pass = $_POST['user_password'];
		$is_teacher = isset($_POST['user_is_teacher']) ? 1 : 0;
		
		if(empty($name)){
			$this->error = 'ユーザー名を入力してください。';
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
			$stmt = $this->db->prepare('INSERT INTO users (user_name, user_mailaddress, user_password, user_is_teacher, user_login) VALUES (?, ?, ?, ?, ?)');
			$result = $stmt->execute([$name, $mail, sha1($pass), $is_teacher, $mail]);
			if($result){
				$this->success = 'ユーザーを追加しました。';
				// フォームリセット
				$_POST['user_name'] = '';
				$_POST['user_mailaddress'] = '';
				$_POST['user_password'] = '';
				$_POST['user_is_teacher'] = '';
			} else {
				$this->error = 'ユーザーの追加に失敗しました。';
			}
		} catch (Exception $e) {
			$this->error = 'データベースエラーが発生しました: ' . $e->getMessage();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー削除処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function delete_user(){
		$delete_id = intval($_POST['delete_user_id']);
		
		if($delete_id <= 0){
			$this->error = '無効なユーザーIDです。';
			return;
		}
		
		try {
			$stmt = $this->db->prepare('DELETE FROM users WHERE user_id = ?');
			$result = $stmt->execute([$delete_id]);
			if($result){
				$this->success = 'ユーザーを削除しました。';
			} else {
				$this->error = 'ユーザーの削除に失敗しました。';
			}
		} catch (Exception $e) {
			$this->error = 'データベースエラーが発生しました: ' . $e->getMessage();
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
			$stmt = $this->db->prepare('SELECT user_id, user_name, user_mailaddress, user_is_teacher FROM users ORDER BY user_id DESC');
			$stmt->execute();
			$this->users = $stmt->fetchAll();
		} catch (Exception $e) {
			$this->error = 'ユーザー一覧の取得に失敗しました。';
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
	@brief	ユーザー一覧テーブルの取得
	@return	ユーザー一覧テーブル文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_users_table(){
		$table_str = '';
		foreach($this->users as $user){
			$user_id = $user['user_id'];
			$user_name = display($user['user_name']);
			$user_mail = display($user['user_mailaddress']);
			$user_type = $user['user_is_teacher'] ? '教師' : '保護者';
			$badge_class = $user['user_is_teacher'] ? 'bg-primary' : 'bg-success';
			
			$table_str .= <<<END_BLOCK
<tr>
    <td>{$user_name}</td>
    <td>{$user_mail}</td>
    <td>
        <span class="badge {$badge_class}">{$user_type}</span>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm" onClick="delete_user({$user_id})">削除</button>
    </td>
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
    <title>ユーザー管理</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content-wrapper">
        <div class="container bg-white rounded shadow">
            <?= $this->get_error_display(); ?>
            <?= $this->get_success_display(); ?>
            
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    ユーザーを追加
                </button>
            </div>
            <h2>ユーザー一覧</h2>
            <table class="table table-striped table-hover mt-3">
                <thead class="table-light section-header">
                    <tr>
                        <th>ユーザー名</th>
                        <th>メールアドレス</th>
                        <th>アカウント種別</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?= $this->get_users_table(); ?>
                </tbody>
            </table>
        </div>

        <!-- ユーザー追加モーダル -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">ユーザーを追加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_name" class="form-label">ユーザー名</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" value="<?= display($_POST['user_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_mailaddress" class="form-label">メールアドレス</label>
                            <input type="email" class="form-control" id="user_mailaddress" name="user_mailaddress" value="<?= display($_POST['user_mailaddress']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_password" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="user_password" name="user_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">アカウント種別</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="user_is_teacher" name="user_is_teacher" <?= $_POST['user_is_teacher'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="user_is_teacher">
                                    教師アカウント（チェックしない場合は保護者アカウント）
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="button" class="btn btn-primary" onClick="set_func_form('add_user','')">追加</button>
                    </div>
                    <input type="hidden" name="func" value="" />
                    <input type="hidden" name="param" value="" />
                </form>
            </div>
        </div>
        
        <!-- 削除確認用フォーム -->
        <form name="form2" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" style="display:none;">
            <input type="hidden" name="delete_user_id" id="delete_user_id" value="">
            <input type="hidden" name="func" value="delete_user" />
            <input type="hidden" name="param" value="" />
        </form>
    </main>
    
    <script>
    function set_func_form(func, param) {
        document.form1.func.value = func;
        document.form1.param.value = param;
        document.form1.submit();
    }
    
    function delete_user(user_id) {
        if(confirm('本当に削除しますか？')) {
            document.getElementById('delete_user_id').value = user_id;
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