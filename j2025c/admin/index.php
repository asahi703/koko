<?php
/*!
@file index.php
@brief 管理者ログイン画面
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
	public $error;
	public $success;
	public $mail_box;
	public $password_box;
	
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
		$this->error = '';
		$this->success = '';
		$this->mail_box = null;
		$this->password_box = null;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("administer_mailaddress",'');
		cutil::post_default("administer_password",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// 管理者セッション確認
		if(is_admin_logged_in()){
			cutil::redirect_exit('user_management.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/../main/common/dbmanager.php');
		
		//フォームボックス作成
		$this->mail_box = new ctextbox('administer_mailaddress', '', 'class="form-control" type="email" placeholder="メールアドレス" required');
		$this->password_box = new ctextbox('administer_password', '', 'class="form-control" type="password" placeholder="パスワード" required');
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
				case 'login':
					$this->admin_login();
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	管理者ログイン処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function admin_login(){
		$mail = trim($_POST['administer_mailaddress']);
		$pass = trim($_POST['administer_password']);
		
		if(empty($mail)){
			$this->error = 'メールアドレスを入力してください。';
			return;
		}
		
		if(empty($pass)){
			$this->error = 'パスワードを入力してください。';
			return;
		}
		
		try {
			$db = new cdb();
			$stmt = $db->prepare('SELECT * FROM administers WHERE administer_mailaddress = ?');
			$stmt->execute([$mail]);
			$administer = $stmt->fetch();
			
			if ($administer && $administer['administer_password'] === sha1($pass)) {
				// ログイン成功
				login_admin([
					'auid' => $administer['administer_id'],
					'name' => $administer['administer_name'],
					'mail' => $administer['administer_mailaddress']
				]);
				cutil::redirect_exit('user_management.php');
			} else {
				$this->error = 'メールアドレスまたはパスワードが違います。';
			}
		} catch (Exception $e) {
			$this->error = 'ログイン処理でエラーが発生しました。';
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
			return '<div class="alert alert-danger" role="alert">' . display($this->error) . '</div>';
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
			return '<div class="alert alert-success" role="alert">' . display($this->success) . '</div>';
		}
		return '';
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
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>管理者ログイン</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
            crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="css/Global.css">
</head>

<div class="login-wrapper">
    <div class="container d-flex flex-column align-items-center justify-content-center vw-100">
        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-md-3">
            <div class="mb-3">
                <h2>管理者ログイン</h2>
            </div>
            <?= $this->get_error_display(); ?>
            <?= $this->get_success_display(); ?>
            <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="border rounded shadow p-4 w-100 login-form">
                <div class="form-group my-2 px-2 w-100">
                    <label>メールアドレス <span class="text-danger fw-bold">*</span></label>
                    <input type="email" name="administer_mailaddress" class="form-control" placeholder="メールアドレス" value="<?= display($_POST['administer_mailaddress']); ?>" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>パスワード <span class="text-danger fw-bold">*</span></label>
                    <input type="password" name="administer_password" class="form-control" placeholder="パスワード" required>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <button type="button" class="btn btn-primary w-100" onClick="set_func_form('login','')">ログイン</button>
                </div>
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
            </form>
        </div>
    </div>
</div>

<script>
function set_func_form(func, param) {
    document.form1.func.value = func;
    document.form1.param.value = param;
    document.form1.submit();
}
</script>
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
