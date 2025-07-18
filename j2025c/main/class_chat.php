<?php
/*!
@file class_chat.php
@brief クラスチャチE��
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
	@brief  POST変数のチE��ォルト値をセチE��
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		if(!isset($_POST['message'])){
			$_POST['message'] = "";
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
		
		$this->class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		if(!$this->user || !$this->class_id){
			cutil::redirect_exit('community.php');
		}
		
		// DB接綁E
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作�E
		$this->message_box = new ctextarea('message', $_POST['message'], 'class="form-control me-2" placeholder="メチE��ージを�E劁E required rows="1"');
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
			// クラス惁E��取征E
			$this->get_class_info();
			
			if(!$this->class_info){
				cutil::redirect_exit('community.php');
			}
			
			// メチE��ージ送信処琁E
			if(isset($_POST['submit'])){
				$this->validate_message();
				if(empty($err_array)){
					$this->send_message();
				}
			}
			
			// チャチE��メチE��ージ取征E
			$this->get_messages();
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス惁E��取征E
	@return	なぁE
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
	@brief	メチE��ージ送信処琁E
	@return	なぁE
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
			
			// フォームリセチE��
			$_POST['message'] = '';
			$this->message_box = new ctextarea('message', '', 'class="form-control me-2" placeholder="メチE��ージを�E劁E required rows="1"');
			
			// ペ�EジリダイレクチE
			cutil::redirect_exit($_SERVER['REQUEST_URI']);
			
		} catch(PDOException $e){
			$this->error = 'メチE��ージの送信に失敗しました、E;
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	チャチE��メチE��ージ取征E
	@return	なぁE
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
	@brief	チャチE��メチE��ージ一覧表示の取征E
	@return	チャチE��メチE��ージ一覧表示斁E���E
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
	@brief	メチE��ージバリチE�Eション
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	function validate_message(){
		global $err_array;
		global $err_flag;
		
		$message = isset($_POST['message']) ? trim($_POST['message']) : '';
		if(empty($message)){
			$err_array['message'] = 'メチE��ージを�E力してください、E;
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メチE��ージエラー表示の取征E
	@return	メチE��ージエラー表示斁E���E
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
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロチE��終亁E
?>
<!-- コンチE��チE��-->
<head>
    <meta charset="UTF-8">
    <title><?= display($this->class_info['class_name']); ?> - チャチE��</title>
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

        <!-- チャチE��メチE��ージ表示エリア -->
        <div class="chat-messages bg-light rounded p-3 mb-4 flex-grow-1" style="overflow-y: auto;">
            <?= $this->get_chat_messages(); ?>
        </div>

        <!-- メチE��ージ入力フォーム -->
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
<!-- /コンチE��チE��-->

<script>
    // チャチE��を最新のメチE��ージまでスクロール
    window.onload = function() {
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };
</script>
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

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content-wrapper">
        <main class="container-fluid py-4 d-flex flex-column" style="height: calc(100vh - 56px);">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h2 class="mb-0"><?php echo htmlspecialchars($class['class_name']); ?></h2>
                    <small class="text-muted">
                        <a href="class_select.php?id=<?php echo $class['community_id']; ?>"
                            class="text-decoration-none">
                            <?php echo htmlspecialchars($class['community_name']); ?>
                        </a>
                    </small>
                    <a href="class_calender.php?id=<?php echo $class_id; ?>"
                        class="btn btn-outline-primary btn-sm ms-3">行事予定表</a>
                </div>
            </div>

            <!-- チャチE��メチE��ージ表示エリア -->
            <div class="chat-messages bg-light rounded p-3 mb-4 flex-grow-1" style="overflow-y: auto;">
                <?php foreach (array_reverse($messages) as $message): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($message['user_icon'])): ?>
                                    <img src="../<?php echo htmlspecialchars($message['user_icon']); ?>"
                                        class="rounded-circle me-2" style="width: 32px; height: 32px;" alt="">
                                <?php else: ?>
                                    <img src="../main/img/headerImg/account.png" class="rounded-circle me-2"
                                        style="width: 32px; height: 32px;" alt="">
                                <?php endif; ?>
                                <span class="fw-bold"><?php echo htmlspecialchars($message['user_name']); ?></span>
                            </div>
                            <small class="text-muted">
                                <?php echo date('Y/m/d H:i', strtotime($message['created_at'])); ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- メチE��ージ入力フォーム -->
            <div class="mt-auto bg-white border-top">
                <form method="post" class="d-flex align-items-center">
                    <textarea class="form-control me-2" name="message" id="chatInput" rows="1" placeholder="メチE��ージを�E劁E
                        required></textarea>
                    <!-- +アイコンとドロチE�Eダウン -->
                    <div class="dropdown">
                        <button class="btn btn-link px-2" type="button" id="templateDropdownBtn"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-plus fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="templateDropdownBtn"
                            style="max-height:200px;overflow-y:auto;">
                            <?php foreach ($templates as $template): ?>
                                <li>
                                    <!--チE��プレートドロチE�Eダウンメニュー-->
                                    <a class="dropdown-item template-insert-btn" href="#"
                                        data-body="<?php echo htmlspecialchars($template['temprate_text'], ENT_QUOTES); ?>">
                                        <?php echo htmlspecialchars($template['temprate_title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button type="submit" class="btn btn-primary">送信</button>
                </form>
            </div>


            <!--チE��プレートモーダル-->
            <div class="modal fade" id="myModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">チE��プレート編雁E�E差し替ぁE/h5>
                        </div>
                        <div class="modal-body" id="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じめE/button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // チャチE��を最新のメチE��ージまでスクロール
        window.onload = function () {
            const chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };

        // プレースホルダを�E部抜き出ぁE
        function extractPlaceholders(template) {
            const regex = /{([^{}]+)}/g;
            let match;
            const results = new Set();
            while ((match = regex.exec(template)) !== null) {
                results.add(match[1]);
            }
            return Array.from(results);
        }

        // 置揁E
        function fillTemplate(template, values) {
            return template.replace(/{([^{}]+)}/g, (m, key) => values[key] ?? m);
        }



        // ペ�Eジロード時にモーダルの背景めEodyクラスが残ってぁE��ら消す
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.remove('modal-open');
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function (bd) { bd.parentNode.removeChild(bd); });

            // チE��プレートを入力欁E��挿入する
            document.querySelectorAll('.template-insert-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const originalTemplate = btn.getAttribute('data-body');
                    // プレースホルダ重褁E��除�E�EetでOK�E�E
                    const regex = /{([^{}]+)}/g;
                    let match;
                    const placeholderSet = new Set();
                    while ((match = regex.exec(originalTemplate)) !== null) {
                        placeholderSet.add(match[1]);
                    }
                    const placeholders = Array.from(placeholderSet);

                    // 編雁E��能本斁E
                    let formHtml = `
            <div class="mb-3">
                <label>本斁E��書き換え可�E�E/label>
                <textarea id="modalTemplateText" class="form-control" rows="4">${originalTemplate}</textarea>
            </div>
            <form id="placeholderForm">
                <table class="table table-sm">
                  <thead><tr><th>頁E��</th><th>値</th></tr></thead><tbody>
        `;
                    placeholders.forEach(ph => {
                        formHtml += `
                <tr>
                  <td>${ph}</td>
                  <td><input type="text" class="form-control" name="${ph}"></td>
                </tr>
            `;
                    });
                    formHtml += `
                  </tbody>
                </table>
                <button type="submit" class="btn btn-primary mt-2">反映</button>
            </form>
        `;
                    document.getElementById('modal-body').innerHTML = formHtml;
                    const modal = new bootstrap.Modal(document.getElementById('myModal'));
                    modal.show();

                    // サブミチE��晁E
                    document.getElementById('placeholderForm').onsubmit = function (e) {
                        e.preventDefault();
                        const values = {};
                        placeholders.forEach(ph => values[ph] = this.elements[ph].value);
                        // 最新の本斁E��置揁E
                        const currentTemplate = document.getElementById('modalTemplateText').value;
                        document.getElementById('chatInput').value = fillTemplate(currentTemplate, values);
                        modal.hide();
                    };
                });
            });
        });
    </script>
</div>
</div>
<!-- /コンチE��チE��-->

<script>
// チャチE��を最新のメチE��ージまでスクロール
window.onload = function () {
    const chatMessages = document.querySelector('.chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
};
</script>
<?php 
//PHPブロチE��再開
}
