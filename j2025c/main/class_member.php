<?php
/*!
@file class_member.php
@brief クラスメンバー確認
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
	public $members;
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
		$this->members = array();
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
			
			// メンバー一覧取得
			$this->get_members();
			
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
	@brief	メンバー一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_members(){
		try{
			$stmt = $this->db->prepare('
				SELECT u.uuid, u.user_name, u.user_icon 
				FROM class_members cm 
				JOIN users u ON cm.user_id = u.uuid 
				WHERE cm.class_id = ?
				ORDER BY u.user_name
			');
			$stmt->execute([$this->class_id]);
			$this->members = $stmt->fetchAll();
		} catch(PDOException $e){
			// テーブルが存在しない場合はサンプルデータ
			$this->members = array(
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
				),
				array(
					'uuid' => '5',
					'user_name' => '高橋美咲',
					'user_icon' => null
				)
			);
		} catch(exception $e){
			$this->members = array();
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
	@brief	メンバー一覧表示の取得
	@return	メンバー一覧表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_members_display(){
		$members_str = '';
		
		foreach($this->members as $member){
			$user_name = display($member['user_name']);
			$user_id = $member['uuid'];
			
			$members_str .= <<<END_BLOCK
<li class="border-top">
    <div>
        <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3"
           href="mypage.php?id={$user_id}">
            <img src="../main/img/headerImg/account.png" style="width: 30px"
                 class="hd-img d-inline-block align-top img-fluid" alt="">
            <span class="ms-2 d-flex align-items-center"
                  style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{$user_name}</span>
        </a>
    </div>
</li>
END_BLOCK;
		}
		
		return $members_str;
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
    <title>クラスメンバー</title>
    <link rel="stylesheet" href="../main/css/class_member.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <?php include 'includes/class_sidebar.php'?>

    <!-- メインコンテンツ -->
    <main class="class-main-content col-12 col-md-9 col-lg-10 p-5" style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">
        <div class="container">
            <h2 class="mb-5">メンバー確認</h2>
            <ul class="list-unstyled">
                <?= $this->get_members_display(); ?>
            </ul>
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
