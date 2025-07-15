<?php
/*!
@file class_select.php
@brief クラス選択
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;
//プライマリキー
$community_id = 0;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $user;
	public $community;
	public $classes;
	public $invite_code;
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
		//プライマリキー
		global $community_id;
		if(isset($_GET['id']) 
		//cutilクラスのメンバ関数をスタティック呼出
			&& cutil::is_number($_GET['id'])
			&& $_GET['id'] > 0){
			$community_id = $_GET['id'];
		}
		
		$this->user = null;
		$this->community = null;
		$this->classes = array();
		$this->invite_code = null;
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
		cutil::post_default("class_name",'');
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
		global $community_id;
		
		if(is_null($page_obj)){
			echo 'ページが無効です';
			exit();
		}
		
		if(!$this->user || $community_id <= 0){
			cutil::redirect_exit('community.php');
		}
		
		// コミュニティ情報の取得
		$this->get_community();
		if(!$this->community){
			cutil::redirect_exit('community.php');
		}
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'create_class':
					//パラメータのチェック
					$this->paramchk_create();
					if($err_flag != 0){
						$this->error = '入力エラーがあります。';
					}
					else{
						$this->create_class();
					}
				break;
				case 'generate_invite_code':
					$this->generate_invite_code();
				break;
				case 'delete_invite_code':
					$this->delete_invite_code();
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		
		// データ取得
		$this->get_classes();
		$this->get_invite_code();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス作成のパラメータチェック
	@return	エラーの場合はfalseを返す
	*/
	//--------------------------------------------------------------------------------------
	function paramchk_create(){
		global $err_array;
		global $err_flag;
		
		// 権限チェック
		if($this->community['community_owner'] != $this->user['uuid'] || empty($this->user['user_is_teacher'])){
			$this->error = 'クラス作成権限がありません。';
			$err_flag = 1;
			return;
		}
		
		/// クラス名の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'class_name','クラス名','isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ情報取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_community(){
		global $community_id;
		$communities_obj = new ccommunities();
		$this->community = $communities_obj->get_community(false, $community_id);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス作成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function create_class(){
		global $community_id;
		try {
			$classes_obj = new cclasses();
			$result = $classes_obj->insert_class(false, array(
				'class_name' => $_POST['class_name'],
				'class_community' => $community_id
			));
			$this->success = 'クラスを作成しました。';
		} catch (Exception $e) {
			$this->error = 'クラス作成に失敗しました。';
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード生成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function generate_invite_code(){
		global $community_id;
		// 権限チェック
		if($this->community['community_owner'] != $this->user['uuid'] || empty($this->user['user_is_teacher'])){
			$this->error = '招待コード生成権限がありません。';
			return;
		}
		
		try {
			$invite_code = bin2hex(random_bytes(8));
			$invite_obj = new ccommunity_invite_codes();
			$result = $invite_obj->create_invite_code(false, $community_id, $invite_code);
			$this->success = '招待コードを生成しました。';
		} catch (Exception $e) {
			$this->error = '招待コードの生成に失敗しました。';
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード削除処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function delete_invite_code(){
		global $community_id;
		// 権限チェック
		if($this->community['community_owner'] != $this->user['uuid'] || empty($this->user['user_is_teacher'])){
			$this->error = '招待コード削除権限がありません。';
			return;
		}
		
		try {
			$invite_obj = new ccommunity_invite_codes();
			$result = $invite_obj->delete_invite_code(false, $community_id);
			$this->success = '招待コードを削除しました。';
		} catch (Exception $e) {
			$this->error = '招待コードの削除に失敗しました。';
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_classes(){
		global $community_id;
		$classes_obj = new cclasses();
		$this->classes = $classes_obj->get_community_classes(false, $community_id);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_invite_code(){
		global $community_id;
		$invite_obj = new ccommunity_invite_codes();
		$this->invite_code = $invite_obj->get_invite_code_by_community(false, $community_id);
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
	@brief	コミュニティ名の取得
	@return	コミュニティ名文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_community_name(){
		if($this->community){
			return display($this->community['community_name']);
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス名入力項目の取得
	@return	クラス名入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_class_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('class_name',$_POST['class_name'],'class="form-control" placeholder="新しいクラス名" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['class_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['class_name']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス作成フォームの取得
	@return	クラス作成フォーム文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_class_create_form(){
		if($this->community['community_owner'] != $this->user['uuid'] || empty($this->user['user_is_teacher'])){
			return '';
		}
		
		$form_str = <<<END_BLOCK
<form name="form1" action="{$_SERVER['PHP_SELF']}?id={$this->community['community_id']}" method="post" class="mb-4">
    <div class="input-group">
        {$this->get_class_name()}
        <button type="button" class="btn btn-primary" onClick="set_func_form('create_class','')">クラス作成</button>
    </div>
    <input type="hidden" name="func" value="" />
    <input type="hidden" name="param" value="" />
</form>
END_BLOCK;
		return $form_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード管理UIの取得
	@return	招待コード管理UI文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_invite_code_ui(){
		if($this->community['community_owner'] != $this->user['uuid'] || empty($this->user['user_is_teacher'])){
			return '';
		}
		
		$ui_str = '<div class="mb-4">';
		$ui_str .= '<form name="form2" action="' . $_SERVER['PHP_SELF'] . '?id=' . $this->community['community_id'] . '" method="post" style="display:inline;">';
		
		if($this->invite_code){
			$ui_str .= '<span class="me-3">招待コード: <strong>' . display($this->invite_code) . '</strong></span>';
			$ui_str .= '<button type="button" class="btn btn-danger btn-sm" onClick="set_func_form2(\'delete_invite_code\',\'\')">削除</button>';
		} else {
			$ui_str .= '<button type="button" class="btn btn-success" onClick="set_func_form2(\'generate_invite_code\',\'\')">招待コード生成</button>';
		}
		
		$ui_str .= '<input type="hidden" name="func" value="" />';
		$ui_str .= '<input type="hidden" name="param" value="" />';
		$ui_str .= '</form>';
		$ui_str .= '</div>';
		
		return $ui_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス一覧の取得
	@return	クラス一覧文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_class_list(){
		if(count($this->classes) == 0){
			return '<div class="col-12"><div class="alert alert-info">クラスがありません。</div></div>';
		}
		
		$list_str = '';
		foreach($this->classes as $class){
			$class_name = display($class['class_name']);
			$class_id = $class['class_id'];
			
			$list_str .= <<<END_BLOCK
<div class="col-12 col-md-6 col-lg-4 mb-2">
    <a href="class_chat.php?id={$class_id}" class="text-decoration-none">
        <div class="card p-3 hover-shadow">
            <div class="fw-bold">{$class_name}</div>
        </div>
    </a>
</div>
END_BLOCK;
		}
		return $list_str;
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
    <title>クラス選択</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/community.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="container py-4">
        <h2 class="mb-3"><?= $this->get_community_name(); ?> のクラス一覧</h2>
        
        <?= $this->get_class_create_form(); ?>
        <?= $this->get_invite_code_ui(); ?>

        <h4>コミュニティ内のクラス</h4>
        <div class="row">
            <?= $this->get_class_list(); ?>
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

function set_func_form2(func, param) {
    document.form2.func.value = func;
    document.form2.param.value = param;
    document.form2.submit();
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
