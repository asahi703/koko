<?php
/*!
@file class_add.php
@brief クラスメンバー追加
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
	public $class_id;
	public $db;
	public $class_info;
	public $available_users;
	public $current_members;
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
		$this->class_id = 0;
		$this->db = null;
		$this->class_info = null;
		$this->available_users = array();
		$this->current_members = array();
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
		if(!isset($_POST['selected_users'])){
			$_POST['selected_users'] = array();
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
		
		$this->class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		if(!$this->user || !$this->class_id){
			cutil::redirect_exit('community.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
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
		
		try{
			// クラス情報取得
			$this->get_class_info();
			
			if(!$this->class_info){
				cutil::redirect_exit('community.php');
			}
			
			// 利用可能ユーザー一覧取得
			$this->get_available_users();
			
			// 現在のメンバー取得
			$this->get_current_members();
			
			// メンバー追加処理
			if(isset($_POST['update'])){
				$this->update_members();
			}
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス情報取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_class_info(){
		try{
			$stmt = $this->db->prepare('
				SELECT c.*, com.community_name, com.community_id 
				FROM classes c 
				JOIN communities com ON c.class_community = com.community_id 
				WHERE c.class_id = ?
			');
			$stmt->execute([$this->class_id]);
			$this->class_info = $stmt->fetch();
		} catch(exception $e){
			$this->class_info = null;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	利用可能ユーザー一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_available_users(){
		try{
			// サンプルユーザーデータ
			$this->available_users = array(
				array(
					'uuid' => '1',
					'user_name' => '田中太郎',
					'user_icon' => null
				),
				array(
					'uuid' => '2',
					'user_name' => '佐藤花子',
					'user_icon' => null
				),
				array(
					'uuid' => '3',
					'user_name' => '山田次郎',
					'user_icon' => null
				),
				array(
					'uuid' => '4',
					'user_name' => '鈴木三郎',
					'user_icon' => null
				)
			);
		} catch(exception $e){
			$this->available_users = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	現在のメンバー取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_current_members(){
		try{
			// サンプル現在メンバー
			$this->current_members = array('1', '3'); // ユーザーID1と3が現在のメンバー
		} catch(exception $e){
			$this->current_members = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メンバー更新処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function update_members(){
		global $err_flag;
		
		try{
			$selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : array();
			
			// メンバー更新処理（サンプル）
			// 実際のDBスキーマに合わせて調整してください
			
			$this->success = 'メンバーを更新しました。';
			
			// 現在のメンバーを更新
			$this->current_members = $selected_users;
			
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
	@brief	ユーザー一覧表示の取得
	@return	ユーザー一覧表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_users_display(){
		$users_str = '';
		
		foreach($this->available_users as $user){
			$user_id = $user['uuid'];
			$user_name = display($user['user_name']);
			$checked = in_array($user_id, $this->current_members) ? 'checked' : '';
			
			$users_str .= <<<END_BLOCK
<li class="border-top">
    <div class="user-checkbox-row d-flex align-items-center gap-3 px-3 py-2">
        <img src="../main/img/headerImg/account.png" style="width: 30px"
             class="hd-img d-inline-block align-top img-fluid" alt="ユーザーアイコン">
        <span class="user-name"
              style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
            {$user_name}
        </span>
        <div class="form-check ms-auto">
            <input type="checkbox" class="form-check-input custom-checkbox-lg" 
                   name="selected_users[]" value="{$user_id}" id="user{$user_id}" {$checked}>
        </div>
    </div>
</li>
END_BLOCK;
		}
		
		return $users_str;
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
    <title>クラスメンバー追加</title>
    <link rel="stylesheet" href="../main/css/class_add.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <?php include 'includes/class_sidebar.php'?>

    <!-- メインコンテンツ -->
    <main class="class-main-content col-12 col-md-9 col-lg-10 p-5"
          style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">
        <div class="container" style="background-color: #f5f5f5;">
            <h2 class="mb-5">メンバー追加</h2>
            
            <form method="post">
                <button type="submit" name="update" class="btn btn-primary update-button" style="top: 150px;">更新</button>
                <ul class="list-unstyled">
                    <?= $this->get_users_display(); ?>
                </ul>
            </form>
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

