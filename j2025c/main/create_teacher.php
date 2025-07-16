<?php
/*!
@file create_teacher.php
@brief 教師アカウント作成
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $user;
	public $db;
	public $name_box;
	public $email_box;
	public $password_box;
	public $password_confirm_box;
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
		$this->user = null;
		$this->db = null;
		$this->name_box = null;
		$this->email_box = null;
		$this->password_box = null;
		$this->password_confirm_box = null;
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
		if(!isset($_POST['name'])){
			$_POST['name'] = "";
		}
		if(!isset($_POST['mail'])){
			$_POST['mail'] = "";
		}
		if(!isset($_POST['password'])){
			$_POST['password'] = "";
		}
		if(!isset($_POST['password_confirm'])){
			$_POST['password_confirm'] = "";
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// セッション情報の取得
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
		
		if(!$this->user || empty($this->user['user_is_teacher'])){
			cutil::redirect_exit('community.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作成
		$this->name_box = new ctextbox('name', 'form-control shadow-sm', 255);
		$this->name_box->set_required(true);
		
		$this->email_box = new ctextbox('mail', 'form-control shadow-sm', 255);
		$this->email_box->set_required(true);
		$this->email_box->set_email_check(true);
		
		$this->password_box = new ctextbox('password', 'form-control shadow-sm', 255);
		$this->password_box->set_required(true);
		$this->password_box->set_attribute('type', 'password');
		
		$this->password_confirm_box = new ctextbox('password_confirm', 'form-control shadow-sm', 255);
		$this->password_confirm_box->set_required(true);
		$this->password_confirm_box->set_attribute('type', 'password');
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
		
		try{
			// POSTデータの検証
			$this->name_box->validate();
			$this->email_box->validate();
			$this->password_box->validate();
			$this->password_confirm_box->validate();
			
			// パスワード確認チェック
			if(!empty($_POST['password']) && !empty($_POST['password_confirm'])){
				if($_POST['password'] !== $_POST['password_confirm']){
					$err_array['password_confirm'] = 'パスワードが一致しません。';
				}
			}
			
			// 送信ボタンが押された場合
			if(isset($_POST['submit'])){
				if(empty($err_array)){
					$this->create_teacher_account();
				}
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	教師アカウント作成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function create_teacher_account(){
		global $err_flag;
		
		try{
			$name = $_POST['name'];
			$mail = $_POST['mail'];
			$password = $_POST['password'];
			
			$sql = 'INSERT INTO users (user_name, user_mailaddress, user_password, user_login, user_is_teacher) VALUES (?, ?, ?, ?, 1)';
			$params = [$name, $mail, sha1($password), $mail];

			$stmt = $this->db->prepare($sql);
			$stmt->execute($params);
			
			$this->success = '教師アカウント「' . $name . '」を作成しました。';
			
			// フォームリセット
			$_POST['name'] = '';
			$_POST['mail'] = '';
			$_POST['password'] = '';
			$_POST['password_confirm'] = '';
			$this->name_box->set_value('');
			$this->email_box->set_value('');
			$this->password_box->set_value('');
			$this->password_confirm_box->set_value('');
			
		} catch(PDOException $e){
			if($e->getCode() == '23000'){
				$this->error = 'このメールアドレスは既に使用されています。';
			} else {
				$this->error = 'アカウント作成に失敗しました。';
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー存在文字列の取得
	@return	エラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">入力エラーがあります。各項目のエラーを確認してください。</div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">処理に失敗しました。サポートを確認下さい。</div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
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
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロック終了
?>
<!-- コンテンツ　-->
<head>
    <title>教師アカウント作成</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">教師アカウント作成</h3>
                <p class="card-text">新しい教師アカウントを作成します。</p>

                <form method="post" action="create_teacher.php" class="mt-4">
                    <div class="mb-3 text-start">
                        <label for="name" class="form-label">名前<span class="text-danger">*</span></label>
                        <?= $this->name_box->get_tag(); ?>
                        <?= $this->name_box->get_error_message_tag(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">メールアドレス<span class="text-danger">*</span></label>
                        <?= $this->email_box->get_tag(); ?>
                        <?= $this->email_box->get_error_message_tag(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password" class="form-label">パスワード<span class="text-danger">*</span></label>
                        <?= $this->password_box->get_tag(); ?>
                        <?= $this->password_box->get_error_message_tag(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">パスワード (確認)<span class="text-danger">*</span></label>
                        <?= $this->password_confirm_box->get_tag(); ?>
                        <?= $this->password_confirm_box->get_error_message_tag(); ?>
                    </div>
                    <div class="d-grid">
                        <button type="submit" name="submit" class="btn btn-primary px-5 mt-3">作成</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</div>
<!-- /コンテンツ　-->
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
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//サイドバー追加
$page_obj->add_child(cutil::create('csidebar'));
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
