<?php
/*!
@file faq_create.php
@brief よくある質問作成
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
	public $title_box;
	public $content_box;
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
		$this->title_box = null;
		$this->content_box = null;
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
		if(!isset($_POST['title'])){
			$_POST['title'] = "";
		}
		if(!isset($_POST['content'])){
			$_POST['content'] = "";
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
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
		
		//フォームボックス作成
		$this->title_box = new ctextbox('title', 'form-control', 255);
		$this->title_box->set_required(true);
		
		$this->content_box = new ctextarea('content', 'form-control', 5);
		$this->content_box->set_required(true);
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
			$this->title_box->validate();
			$this->content_box->validate();
			
			// 送信ボタンが押された場合
			if(isset($_POST['submit'])){
				if(empty($err_array)){
					// FAQ質問を送信する処理
					$this->submit_faq();
				}
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	FAQ質問送信処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function submit_faq(){
		global $err_flag;
		
		try{
			$title = $_POST['title'];
			$content = $_POST['content'];
			
			// 実際のFAQ質問送信処理をここに実装
			// 現在はサンプルとして成功メッセージのみ表示
			$this->success = '質問を送信しました。回答までしばらくお待ちください。';
			
			// フォームをリセット
			$_POST['title'] = '';
			$_POST['content'] = '';
			$this->title_box->set_value('');
			$this->content_box->set_value('');
			
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
    <title>よくある質問作成</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 40px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">質問は何ですか？</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form method="post" action="faq_create.php">
                    <div class="mb-3">
                        <label for="title" class="form-label">タイトル<span class="text-danger">*</span></label>
                        <?= $this->title_box->get_tag(); ?>
                        <?= $this->title_box->get_error_message_tag(); ?>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">質問内容<span class="text-danger">*</span></label>
                        <?= $this->content_box->get_tag(); ?>
                        <?= $this->content_box->get_error_message_tag(); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary px-5">送信</button>
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
