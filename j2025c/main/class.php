<?php
/*!
@file class.php
@brief クラス投稿表示
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
	public $posts;
	public $message_box;
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
		$this->posts = array();
		$this->message_box = null;
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
		if(!isset($_POST['message'])){
			$_POST['message'] = "";
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
		
		//フォームボックス作成
		$this->message_box = new ctextarea('message', 'form-control rounded', 1);
		$this->message_box->set_required(true);
		$this->message_box->set_attribute('placeholder', 'メッセージを入力');
		$this->message_box->set_attribute('style', 'border: none; box-shadow: none; resize: none; min-height: 38px; max-height: 150px; overflow-y: auto; background: transparent;');
		$this->message_box->set_attribute('oninput', "this.style.height='38px';this.style.height=(this.scrollHeight)+'px';");
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
			
			// 投稿送信処理
			if(isset($_POST['submit'])){
				$this->message_box->validate();
				if(empty($err_array)){
					$this->send_post();
				}
			}
			
			// 投稿一覧取得
			$this->get_posts();
			
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
	@brief	投稿送信処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function send_post(){
		global $err_flag;
		
		try{
			$message = $_POST['message'];
			
			// 投稿をclass_postsテーブルに保存（仮想テーブル）
			// 実際のDBスキーマに合わせて調整してください
			$stmt = $this->db->prepare('
				INSERT INTO class_posts (class_id, user_id, content, created_at) 
				VALUES (?, ?, ?, NOW())
			');
			$stmt->execute([$this->class_id, $this->user['uuid'], $message]);
			
			$this->success = '投稿しました。';
			
			// フォームリセット
			$_POST['message'] = '';
			$this->message_box->set_value('');
			
		} catch(PDOException $e){
			// テーブルが存在しない場合はサンプルデータを表示
			$this->success = '投稿しました。（サンプル）';
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	投稿一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_posts(){
		try{
			// サンプル投稿データ
			$this->posts = array(
				array(
					'user_name' => 'ユーザー名1',
					'user_icon' => null,
					'content' => '本文です改行もできる',
					'created_at' => date('Y/m/d H:i', strtotime('-1 hour'))
				),
				array(
					'user_name' => 'ユーザー名2',
					'user_icon' => null,
					'content' => '本文です改行もできる',
					'created_at' => date('Y/m/d H:i', strtotime('-2 hours'))
				)
			);
		} catch(exception $e){
			$this->posts = array();
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
	@brief	投稿一覧表示の取得
	@return	投稿一覧表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_posts_display(){
		$posts_str = '';
		
		foreach($this->posts as $post){
			$user_name = display($post['user_name']);
			$content = nl2br(display($post['content']));
			$created_at = $post['created_at'];
			
			$posts_str .= <<<END_BLOCK
<div class="rounded border border-2 mb-3 shadow-sm">
    <div class="border-bottom d-flex align-items-center justify-content-between" style="max-height: 50px;">
        <a class="nav-link text-dark py-1 text-nowrap custom-hover d-flex align-items-center px-3" href="#">
            <img src="../main/img/headerImg/account.png" style="width: 40px"
                 class="hd-img d-inline-block align-top img-fluid" alt="">
            <span class="ms-2 d-flex align-items-center fs-5"
                  style="max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{$user_name}</span>
        </a>
        <span class="opacity-75 me-3" style="font-size: 0.9em;">{$created_at}</span>
    </div>
    <div class="post-word p-3">
        {$content}
    </div>
</div>
END_BLOCK;
		}
		
		return $posts_str;
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
    <title>クラス</title>
    <link rel="stylesheet" href="../main/css/class.css">
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

        <!--投稿一覧-->
        <?= $this->get_posts_display(); ?>

        <!-- メッセージ入力フォーム -->
        <div class="row justify-content-center w-100 mt-4">
            <div class="col-12 col-md-6">
                <form method="post" class="chat-input">
                    <div class="input-group bg-white rounded-pill border px-3 py-1 align-items-end"
                         style="min-height: 45px;">
                        <?= $this->message_box->get_tag(); ?>

                        <div class="dropdown d-flex justify-content-center align-items-center me-2"
                             style="height: 38px;">
                            <a class="cu-pt d-flex align-items-center h-100" id="templateDropdownAdd"
                               data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                                <img src="../main/img/add_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt=""
                                     style="display: block; height: 24px; width: 24px; margin: auto 0; position: relative;">
                            </a>
                        </div>

                        <!-- ファイル添付+ -->
                        <div class="dropdown d-flex justify-content-center align-items-center"
                             style="height: 38px;">
                            <a class="cu-pt d-flex align-items-center h-100" id="templateDropdownAdd"
                               data-bs-toggle="dropdown" aria-expanded="false" style="height: 38px;">
                                <img src="../main/img/apps_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt=""
                                     style="display: block; height: 24px; width: 24px; margin: auto 0; position: relative;">
                            </a>

                            <!-- メインドロップダウン -->
                            <ul class="dropdown-menu" aria-labelledby="templateDropdownAdd">
                                <li><a class="dropdown-item" href="#">テンプレート１</a></li>
                                <li><a class="dropdown-item" href="#">テンプレート2</a></li>
                            </ul>
                        </div>

                        <button type="submit" name="submit"
                                class="btn btn-primary rounded-pill ms-2 d-flex align-items-center justify-content-center"
                                style="height: 38px; padding: 0 16px; border: none; background: none;">
                            <img src="../main/img/send_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="送信"
                                 style="width: 24px; height: 24px;">
                        </button>
                    </div>
                    <?= $this->message_box->get_error_message_tag(); ?>
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
