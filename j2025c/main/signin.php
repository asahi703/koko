<?php
/*!
@file signin.php
@brief 新規登録
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

require_once('common/config.php');
require_once('common/dbmanager.php');
require_once('common/notification_helper.php');

//ライブラリをインクルード
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
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
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
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
				case 'register':
					//パラメータのチェック
					$this->paramchk();
					if($err_flag != 0){
						$this->error = '入力エラーがあります。';
					}
					else{
						$this->register_user();
					}
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
	@brief	パラメータのチェック
	@return	エラーの場合はfalseを返す
	*/
	//--------------------------------------------------------------------------------------
	function paramchk(){
		global $err_array;
		global $err_flag;
		
		/// ユーザー名の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'user_name','ユーザーネーム','isset_nl')){
			$err_flag = 1;
		}
		
		/// メールアドレスの存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'user_mailaddress','メールアドレス','isset_nl')){
			$err_flag = 1;
		}
		
		/// パスワードの存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'user_password','パスワード','isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー登録処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function register_user(){
		try {
			$users_obj = new cusers();
			$result = $users_obj->insert_user(false, array(
				'user_name' => $_POST['user_name'],
				'user_mailaddress' => $_POST['user_mailaddress'],
				'user_password' => sha1($_POST['user_password']),
				'user_login' => $_POST['user_mailaddress']
			));
			
			// 新規登録されたユーザーIDを取得
			$new_user_id = $result; // insert_userが新しいユーザーIDを返すと仮定
			
			// ウェルカム通知を送信
			if (function_exists('notify_welcome')) {
				notify_welcome($new_user_id, $_POST['user_name']);
			}
			
			// 登録成功時はログインページにリダイレクト
			cutil::redirect_exit('index.php');
		} catch (Exception $e) {
			$this->error = 'ユーザー登録に失敗しました。';
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

<div class="alert alert-danger">入力エラーがあります。各項目のエラーを確認してください。</div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger">処理に失敗しました。サポートを確認下さい。</div>
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
			return '<div class="alert alert-danger">' . display($this->error) . '</div>';
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
			return '<div class="alert alert-success">' . display($this->success) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー名入力項目の取得
	@return	ユーザー名入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_user_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('user_name',$_POST['user_name'],'class="form-control" placeholder="ユーザーネーム" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['user_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['user_name']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メールアドレス入力項目の取得
	@return	メールアドレス入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_user_mailaddress(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('user_mailaddress',$_POST['user_mailaddress'],'type="email" class="form-control" placeholder="メールアドレス" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['user_mailaddress'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['user_mailaddress']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	パスワード入力項目の取得
	@return	パスワード入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_user_password(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('user_password','','type="password" class="form-control" placeholder="パスワード" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['user_password'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['user_password']) 
			. '</span>';
		}
		return $ret_str;
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
    <title>新規登録</title>
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

<!-- コンテンツ　-->
<div class="contents">
<?= $this->get_err_flag(); ?>

<div class="main-content-wrapper">
    <div class="container d-flex flex-column align-items-center justify-content-center vw-100">
        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-md-3">
            <div class="mb-3">
                <h2>新規登録</h2>
            </div>
            <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="border rounded shadow p-4 w-100 login-form">
                <div class="form-group my-2 px-2 w-100">
                    <label>ユーザーネーム <span class="text-danger fw-bold">*</span></label>
                    <?= $this->get_user_name(); ?>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>メールアドレス <span class="text-danger fw-bold">*</span></label>
                    <?= $this->get_user_mailaddress(); ?>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>パスワード <span class="text-danger fw-bold">*</span></label>
                    <?= $this->get_user_password(); ?>
                </div>
                <div class="text-center">
                    <p>利用規約は <a href="">こちらから</a></p>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <button type="button" class="btn btn-primary w-100" onClick="set_func_form('register','')">新規登録</button>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <a href="index.php">ログインはこちら</a>
                </div>
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
                
                <?= $this->get_error_display(); ?>
            </form>
        </div>
    </div>
</div>
</div>
<!-- /コンテンツ　-->
<script>
// prefecture_detail.phpスタイルのフォーム操作関数
function set_func_form(func, param) {
    document.form1.func.value = func;
    document.form1.param.value = param;
    document.form1.submit();
}
</script>
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
//シンプルヘッダ追加
$page_obj->add_child(cutil::create('csimple_header'));
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
