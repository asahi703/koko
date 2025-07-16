<?php
/*!
@file access.php
@brief 権限付与画面
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
		$this->user = null;
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
		
		// ユーザー一覧取得（サンプルデータ）
		$this->get_users();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_users(){
		// サンプルユーザーデータ
		$this->users = array(
			array(
				'user_id' => 1,
				'user_name' => 'ユーザーA',
				'user_mailaddress' => 'userA@example.com',
				'user_is_teacher' => false
			),
			array(
				'user_id' => 2,
				'user_name' => 'ユーザーB',
				'user_mailaddress' => 'userB@example.com',
				'user_is_teacher' => true
			),
			array(
				'user_id' => 3,
				'user_name' => 'ユーザーC',
				'user_mailaddress' => 'userC@example.com',
				'user_is_teacher' => false
			)
		);
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
	@brief	ユーザー一覧表示の取得
	@return	ユーザー一覧表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_user_list(){
		$list_str = '';
		$count = 1;
		
		foreach($this->users as $user){
			$user_name = display($user['user_name']);
			$user_email = display($user['user_mailaddress']);
			$checked = $user['user_is_teacher'] ? 'checked' : '';
			
			$list_str .= <<<END_BLOCK
<div class="access-table-row d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
    <div class="col-4">{$user_name}</div>
    <div class="col-5">{$user_email}</div>
    <div class="col-3 d-flex justify-content-center align-items-center">
        <div class="form-check form-switch custom-toggle">
            <input class="form-check-input" type="checkbox" role="switch" id="toggleSwitch{$count}" {$checked}>
            <label class="form-check-label" for="toggleSwitch{$count}"></label>
        </div>
    </div>
</div>
END_BLOCK;
			$count++;
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
    <title>権限付与画面</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="access-title mb-0">権限付与画面</h2>
        </div>
        <div class="responsive-table-wrapper">
            <div class="access-table-container bg-white rounded shadow-sm">
                <div class="access-table-header d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                    <div class="col-4">ユーザー名</div>
                    <div class="col-5">Eメールアドレス</div>
                    <div class="col-3 text-center">権限</div>
                </div>
                <div class="access-table-body">
                    <?= $this->get_user_list(); ?>
                </div>
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
