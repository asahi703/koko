<?php
/*!
@file chat_several_send.php
@brief 複数人チャット送信
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
	public $members;
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
		$this->db = null;
		$this->members = array();
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
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作成
		$this->message_box = new ctextbox('message', 'form-control rounded-pill', 1, 100);
		$this->message_box->set_required(true);
		$this->message_box->set_attribute('placeholder', 'メッセージを入力');
		$this->message_box->set_attribute('style', 'border: none; box-shadow: none;');
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
			// メンバー一覧取得
			$this->get_members();
			
			// メッセージ送信処理
			if(isset($_POST['submit'])){
				$this->message_box->validate();
				if(empty($_POST['selected_users'])){
					$err_array['selected_users'] = '送信先を選択してください。';
					$err_flag = 1;
				}
				
				if(empty($err_array)){
					$this->send_message();
				}
			}
			
		} catch(exception $e){
			$err_flag = 2;
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
			// サンプルメンバーデータ
			$this->members = array(
				array(
					'uuid' => '1',
					'user_name' => '鈴木一太'
				),
				array(
					'uuid' => '2',
					'user_name' => '近道夜'
				),
				array(
					'uuid' => '3',
					'user_name' => '五十嵐小樹'
				),
				array(
					'uuid' => '4',
					'user_name' => '田中太郎'
				),
				array(
					'uuid' => '5',
					'user_name' => '佐藤花子'
				),
				array(
					'uuid' => '6',
					'user_name' => '山田次郎'
				)
			);
		} catch(exception $e){
			$this->members = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メッセージ送信処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function send_message(){
		global $err_flag;
		
		try{
			$message = $_POST['message'];
			$selected_users = $_POST['selected_users'];
			
			// 各選択されたユーザーにメッセージを送信
			// 実際のDBスキーマに合わせて調整してください
			foreach($selected_users as $user_id){
				// メッセージ送信処理（サンプル）
			}
			
			$this->success = count($selected_users) . '人にメッセージを送信しました。';
			
			// フォームリセット
			$_POST['message'] = '';
			$_POST['selected_users'] = array();
			$this->message_box->set_value('');
			
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
	@brief	メンバー一覧表示の取得
	@return	メンバー一覧表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_members_display(){
		$members_str = '';
		
		foreach($this->members as $member){
			$user_id = $member['uuid'];
			$user_name = display($member['user_name']);
			$checked = in_array($user_id, $_POST['selected_users']) ? 'checked' : '';
			
			$members_str .= <<<END_BLOCK
<div class="multi-item">
    <input type="checkbox" class="multi-checkbox" id="user{$user_id}" name="selected_users[]" value="{$user_id}" {$checked}>
    <label class="multi-label fs-5" for="user{$user_id}">{$user_name}</label>
</div>
END_BLOCK;
		}
		
		return $members_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	選択エラー表示の取得
	@return	選択エラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_selection_error(){
		global $err_array;
		if(isset($err_array['selected_users'])){
			return '<div class="text-danger mt-2">' . display($err_array['selected_users']) . '</div>';
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
    <title>クラスメンバー追加</title>
    <link rel="stylesheet" href="../main/css/chat_several_send.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main
            class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto"
            style="margin-top: 80px; margin-bottom: 60px; max-width: 1200px; height: calc(100vh - 140px); overflow-y: auto;">
        <div class="multi-title fs-3">複数に送る</div>

        <div class="chat-multiple-arrowback">
            <a href="chat.php">
                <img src="../main/img/arrow_back_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="">
            </a>
        </div>

        <form method="post">
            <div class="multi-box rounded">
                <div class="multi-list">
                    <?= $this->get_members_display(); ?>
                </div>
                <?= $this->get_selection_error(); ?>
            </div>

            <!-- チャット入力エリア -->
            <!-- メッセージ入力フォーム -->
            <div class="row justify-content-center w-100 mt-4">
                <div class="col-12 col-md-6">
                    <div class="chat-input-multiple">
                        <div class="input-group bg-white rounded-pill border px-3 py-1" style="height: 45px;">
                            <?= $this->message_box->get_tag(); ?>
                            <button type="submit" name="submit"
                                    class="btn btn-primary rounded-pill ms-2 d-flex align-items-center justify-content-center"
                                    style="height: 100%; padding: 0 16px; border: none; background: none;">
                                <img src="../main/img/send_24dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="送信"
                                     style="width: 24px; height: 24px;">
                            </button>
                        </div>
                        <?= $this->message_box->get_error_message_tag(); ?>
                    </div>
                </div>
            </div>
        </form>
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
