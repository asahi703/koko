<?php
/*!
@file faq_create.php
@brief よくある質問作�E
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルーチE
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノーチE
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
	@brief  POST変数のチE��ォルト値をセチE��
	@return なぁE
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
	@brief	構築時の処琁E継承して使用)
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// セチE��ョン惁E��の取征E
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
		
		//フォームボックス作�E
		$this->title_box = new ctextbox('title', 'form-control', 255);
		$this->title_box->set_required(true);
		
		$this->content_box = new ctextarea('content', 'form-control', 5);
		$this->content_box->set_required(true);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  本体実行（表示前�E琁E��E
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo 'ペ�Eジが無効でぁE;
			exit();
		}
		
		try{
			// POSTチE�Eタの検証
			$this->title_box->validate();
			$this->content_box->validate();
			
			// 送信ボタンが押された場吁E
			if(isset($_POST['submit'])){
				if(empty($err_array)){
					// FAQ質問を送信する処琁E
					$this->submit_faq();
				}
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	FAQ質問送信処琁E
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	function submit_faq(){
		global $err_flag;
		
		try{
			$title = $_POST['title'];
			$content = $_POST['content'];
			
			// 実際のFAQ質問送信処琁E��ここに実裁E
			// 現在はサンプルとして成功メチE��ージのみ表示
			$this->success = '質問を送信しました。回答までし�Eらくお征E��ください、E;
			
			// フォームをリセチE��
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
	@brief	エラー存在斁E���Eの取征E
	@return	エラー表示斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">入力エラーがあります。各頁E��のエラーを確認してください、E/div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">処琁E��失敗しました。サポ�Eトを確認下さぁE��E/div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー表示の取征E
	@return	エラー表示斁E���E
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
	@brief	成功メチE��ージ表示の取征E
	@return	成功メチE��ージ表示斁E���E
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
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロチE��終亁E
require_once('common/session.php');
require_once('common/dbmanager.php');
require_once('common/notification_helper.php');

$user = get_login_user();
if (!$user) {
    header('Location: signin.php');
    exit;
}

$message = '';
$error = '';

// フォーム送信処琁E
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $question = trim($_POST['question'] ?? '');
    
    if (empty($title)) {
        $error = 'タイトルを�E力してください、E;
    } elseif (empty($question)) {
        $error = '質問�E容を�E力してください、E;
    } else {
        try {
            // ユーザー惁E��のチE��チE��
            error_log('FAQ Create - User Info: ' . print_r($user, true));
            error_log('FAQ Create - User ID: ' . ($user['uuid'] ?? 'NULL'));
            
            // user_idが存在しなぁE��合�Eエラーハンドリング
            if (!isset($user['uuid']) || empty($user['uuid'])) {
                $error = 'ユーザー惁E��が正しく取得できませんでした。�E度ログインしてください、E;
                error_log('FAQ Create Error: User UUID is missing or empty');
            } else {
                $db = new cdb();
                $stmt = $db->prepare('
                    INSERT INTO faq (faq_title, faq_question, faq_user_id, faq_created_at) 
                    VALUES (?, ?, ?, NOW())
                ');
                
                if ($stmt->execute([$title, $question, $user['uuid']])) {
                    $faq_id = $db->lastInsertId();
                    
                    // 教師への質問通知を送信
                    $questioner_name = $user['user_name'] ?? $user['name'] ?? 'ユーザー';
                    notify_faq_question($faq_id, $user['uuid'], $questioner_name, $title);
                    
                    $message = '質問を投稿しました。回答をお征E��ください、E;
                    $title = ''; // フォームをリセチE��
                    $question = ''; // フォームをリセチE��
                    error_log('FAQ Created successfully with user_id: ' . $user['uuid']);
                } else {
                    $error = '質問�E投稿に失敗しました。もぁE��度お試しください、E;
                    // チE��チE��惁E��
                    error_log('FAQ Insert Error: ' . print_r($stmt->errorInfo(), true));
                }
            }
        } catch (Exception $e) {
            $error = '質問�E投稿中にエラーが発生しました、E;
            // チE��チE��惁E��
            error_log('FAQ Exception: ' . $e->getMessage());
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<!-- コンチE��チE��-->
<head>
    <title>よくある質問作�E</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 40px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">質問�E何ですか�E�E/h2>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success mx-auto" style="max-width: 800px;" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                <div class="mt-2">
                    <a href="faq.php" class="btn btn-sm btn-outline-success">FAQペ�Eジで確誁E/a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger mx-auto" style="max-width: 800px;" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form method="post" action="faq_create.php">
                    <div class="mb-3">
                        <label for="title" class="form-label">タイトル<span class="text-danger">*</span></label>
                        <?= $this->title_box->get_tag(); ?>
                        <?= $this->title_box->get_error_message_tag(); ?>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">質問�E容<span class="text-danger">*</span></label>
                        <?= $this->content_box->get_tag(); ?>
                        <?= $this->content_box->get_error_message_tag(); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary px-5">送信</button>
                    <div class="mb-3">
                        <label for="title" class="form-label">タイトル<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="質問�Eタイトルを�E力してください..." maxlength="100" required 
                               value="<?php echo htmlspecialchars($title ?? ''); ?>">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            質問�E容を簡潔に表すタイトルをつけてください�E�最大100斁E��！E
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="question" class="form-label">質問�E容<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="question" name="question" rows="6" 
                                  placeholder="ここに質問を入力してください..." required><?php echo htmlspecialchars($question ?? ''); ?></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            できるだけ�E体的に質問�E容を記載してください。回答が早くなります、E
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-paper-plane me-2"></i>質問を送信
                        </button>
                        <a href="faq.php" class="btn btn-outline-secondary px-5 ms-3">
                            <i class="fas fa-arrow-left me-2"></i>戻めE
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</div>
<!-- /コンチE��チE��-->
<?php 
//PHPブロチE��再開
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	チE��トラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのチE��トラクタを呼ぶ
		parent::__destruct();
	}
}

//ペ�Eジを作�E
$page_obj = new cnode();
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//サイドバー追加
$page_obj->add_child(cutil::create('csidebar'));
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処琁E
$page_obj->create();
//POST変数のチE��ォルト値をセチE��
$main_obj->post_default();
//本体実行（表示前�E琁E��E
$main_obj->execute();
//ペ�Eジ全体を表示
$page_obj->display();

?>
