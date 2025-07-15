<?php
/*!
@file mypage.php
@brief マイページ
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
		cutil::post_default("name",'');
		cutil::post_default("mail",'');
		cutil::post_default("password",'');
		cutil::post_default("password_confirm",'');
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
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
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
				case 'update':
					//パラメータのチェック
					$this->paramchk();
					if($err_flag != 0){
						$this->error = '入力エラーがあります。';
					}
					else{
						$this->update_user();
					}
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		else{
			// 初期表示時にユーザー情報をPOSTにセット
			$_POST['name'] = $this->user['name'];
			$_POST['mail'] = $this->user['mail'];
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
		
		/// 名前の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'name','名前','isset_nl')){
			$err_flag = 1;
		}
		
		/// メールアドレスの存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'mail','メールアドレス','isset_nl')){
			$err_flag = 1;
		}
		
		// パスワード確認
		if(!empty($_POST['password'])){
			if($_POST['password'] != $_POST['password_confirm']){
				$err_array['password_confirm'] = 'パスワードが一致しません。';
				$err_flag = 1;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー情報更新処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function update_user(){
		try {
			$users_obj = new cusers();
			$dataarr = array(
				'user_name' => $_POST['name'],
				'user_mailaddress' => $_POST['mail'],
				'user_login' => $_POST['mail']
			);
			
			// パスワードが入力されている場合は更新
			if(!empty($_POST['password'])){
				$dataarr['user_password'] = sha1($_POST['password']);
			}
			
			$where = 'user_id = :user_id';
			$wherearr[':user_id'] = $this->user['user_id'];
			
			$result = $users_obj->update_core(false, 'users', $dataarr, $where, $wherearr, false);
			
			// セッション情報も更新
			$_SESSION['user']['name'] = $_POST['name'];
			$_SESSION['user']['mail'] = $_POST['mail'];
			
			$this->success = 'アカウント情報を更新しました。';
			// ユーザー情報を再取得
			$this->user = get_login_user();
		} catch (Exception $e) {
			$this->error = '更新に失敗しました。';
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
	@brief	名前入力項目の取得
	@return	名前入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('name',$_POST['name'],'class="form-control shadow-sm" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['name']) 
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
	function get_mail(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('mail',$_POST['mail'],'type="email" class="form-control shadow-sm" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['mail'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['mail']) 
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
	function get_password(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('password','','type="password" class="form-control shadow-sm"');
		$ret_str = $tgt->get(false);
		if(isset($err_array['password'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['password']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	パスワード確認入力項目の取得
	@return	パスワード確認入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_password_confirm(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('password_confirm','','type="password" class="form-control shadow-sm"');
		$ret_str = $tgt->get(false);
		if(isset($err_array['password_confirm'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['password_confirm']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	教師メニューの取得
	@return	教師メニュー文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_teacher_menu(){
		if(!empty($this->user['user_is_teacher'])){
			return <<<END_BLOCK
<hr>
<div class="mt-3">
    <a href="create_teacher.php" class="btn btn-info">教師アカウント作成ページへ</a>
</div>
END_BLOCK;
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
    <title>マイページ</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>

<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">マイページ</h3>
                <p class="card-text">アカウント情報を編集します。</p>

                <?= $this->get_error_display(); ?>
                <?= $this->get_success_display(); ?>

                <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="mt-4">
                    <div class="mb-3 text-start">
                        <label for="name" class="form-label">名前<span class="text-danger">*</span></label>
                        <?= $this->get_name(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">メールアドレス<span class="text-danger">*</span></label>
                        <?= $this->get_mail(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password" class="form-label">新しいパスワード (変更する場合のみ)</label>
                        <?= $this->get_password(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">新しいパスワード (確認)</label>
                        <?= $this->get_password_confirm(); ?>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary px-5 mt-3" onClick="set_func_form('update','')">更新</button>
                    </div>
                    <input type="hidden" name="func" value="" />
                    <input type="hidden" name="param" value="" />
                </form>
                
                <?= $this->get_teacher_menu(); ?>
            </div>
        </div>
    </main>
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
