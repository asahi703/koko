<?php
/*!
@file class_chat.php
@brief クラスチャット
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
	public $messages;
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
		$this->messages = array();
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
		$this->message_box = new ctextarea('message', $_POST['message'], 'class="form-control me-2" placeholder="メッセージを入力" required rows="1"');
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
			
			// メッセージ送信処理
			if(isset($_POST['submit'])){
				$this->validate_message();
				if(empty($err_array)){
					$this->send_message();
				}
			}
			
			// チャットメッセージ取得
			$this->get_messages();
			
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
	@brief	メッセージ送信処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function send_message(){
		global $err_flag;
		
		try{
			$message = $_POST['message'];
			
			$stmt = $this->db->prepare('
				INSERT INTO class_chats (class_id, user_id, message) 
				VALUES (?, ?, ?)
			');
			$stmt->execute([$this->class_id, $this->user['uuid'], $message]);
			
			// フォームリセット
			$_POST['message'] = '';
			$this->message_box = new ctextarea('message', '', 'class="form-control me-2" placeholder="メッセージを入力" required rows="1"');
			
			// ページリダイレクト
			cutil::redirect_exit($_SERVER['REQUEST_URI']);
			
		} catch(PDOException $e){
			$this->error = 'メッセージの送信に失敗しました。';
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	チャットメッセージ取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_messages(){
		try{
			$stmt = $this->db->prepare('
				SELECT ch.*, u.user_name, u.user_icon 
				FROM class_chats ch 
				JOIN users u ON ch.user_id = u.user_id 
				WHERE ch.class_id = ? 
				ORDER BY ch.created_at DESC 
				LIMIT 100
			');
			$stmt->execute([$this->class_id]);
			$this->messages = $stmt->fetchAll();
		} catch(exception $e){
			$this->messages = array();
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
	@brief	チャットメッセージ一覧表示の取得
	@return	チャットメッセージ一覧表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_chat_messages(){
		$messages_str = '';
		
		foreach(array_reverse($this->messages) as $message){
			$user_name = display($message['user_name']);
			$message_text = nl2br(display($message['message']));
			$created_at = date('Y/m/d H:i', strtotime($message['created_at']));
			
			$user_icon = '';
			if($message['user_icon']){
				$icon_data = base64_encode($message['user_icon']);
				$user_icon = '<img src="data:image/jpeg;base64,' . $icon_data . '" class="rounded-circle me-2" style="width: 32px; height: 32px;" alt="">';
			} else {
				$user_icon = '<div class="bg-secondary rounded-circle me-2" style="width: 32px; height: 32px;"></div>';
			}
			
			$messages_str .= <<<END_BLOCK
<div class="card mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <div class="d-flex align-items-center">
            {$user_icon}
            <span class="fw-bold">{$user_name}</span>
        </div>
        <small class="text-muted">{$created_at}</small>
    </div>
    <div class="card-body">
        {$message_text}
    </div>
</div>
END_BLOCK;
		}
		
		return $messages_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メッセージバリデーション
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function validate_message(){
		global $err_array;
		global $err_flag;
		
		$message = isset($_POST['message']) ? trim($_POST['message']) : '';
		if(empty($message)){
			$err_array['message'] = 'メッセージを入力してください。';
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メッセージエラー表示の取得
	@return	メッセージエラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_message_error(){
		global $err_array;
		if(isset($err_array['message'])){
			return '<div class="text-danger mt-1">' . display($err_array['message']) . '</div>';
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
    <meta charset="UTF-8">
    <title><?= display($this->class_info['class_name']); ?> - チャット</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/chat.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>
<div class="main-content-wrapper">
    <main class="container-fluid py-4 d-flex flex-column" style="height: calc(100vh - 56px);">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h2 class="mb-0"><?= display($this->class_info['class_name']); ?></h2>
                <small class="text-muted">
                    <a href="class_select.php?id=<?= $this->class_info['community_id']; ?>" class="text-decoration-none">
                        <?= display($this->class_info['community_name']); ?>
                    </a>
                </small>
                <a href="class_calender.php?id=<?= $this->class_id; ?>" class="btn btn-outline-primary btn-sm ms-3">行事予定表</a>
            </div>
        </div>

        <!-- チャットメッセージ表示エリア -->
        <div class="chat-messages bg-light rounded p-3 mb-4 flex-grow-1" style="overflow-y: auto;">
            <?= $this->get_chat_messages(); ?>
        </div>

        <!-- メッセージ入力フォーム -->
        <div class="mt-auto bg-white border-top py-3">
            <form method="post" class="d-flex align-items-center">
                <?= $this->message_box->get(false); ?>
                <button type="submit" name="submit" class="btn btn-primary">送信</button>
                <?= $this->get_message_error(); ?>
            </form>
        </div>
    </main>
</div>
</div>
<!-- /コンテンツ　-->

<script>
    // チャットを最新のメッセージまでスクロール
    window.onload = function() {
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };
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

