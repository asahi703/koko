<?php
/*!
@file class_chat.php
@brief 繧ｯ繝ｩ繧ｹ繝√Ε繝・ヨ
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//繝ｩ繧､繝悶Λ繝ｪ繧偵う繝ｳ繧ｯ繝ｫ繝ｼ繝・
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	譛ｬ菴薙ヮ繝ｼ繝・
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
	@brief	繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繧ｳ繝ｳ繧ｹ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
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
	@brief  POST螟画焚縺ｮ繝・ヵ繧ｩ繝ｫ繝亥､繧偵そ繝・ヨ
	@return 縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		if(!isset($_POST['message'])){
			$_POST['message'] = "";
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	讒狗ｯ画凾縺ｮ蜃ｦ逅・邯呎価縺励※菴ｿ逕ｨ)
	@return	縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// 繧ｻ繝・す繝ｧ繝ｳ諠・ｱ縺ｮ蜿門ｾ・
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
		
		$this->class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		if(!$this->user || !$this->class_id){
			cutil::redirect_exit('community.php');
		}
		
		// DB謗･邯・
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//繝輔か繝ｼ繝繝懊ャ繧ｯ繧ｹ菴懈・
		$this->message_box = new ctextarea('message', $_POST['message'], 'class="form-control me-2" placeholder="繝｡繝・そ繝ｼ繧ｸ繧貞・蜉・ required rows="1"');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  譛ｬ菴灘ｮ溯｡鯉ｼ郁｡ｨ遉ｺ蜑榊・逅・ｼ・
	@return 縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo '繝壹・繧ｸ縺檎┌蜉ｹ縺ｧ縺・;
			exit();
		}
		
		try{
			// 繧ｯ繝ｩ繧ｹ諠・ｱ蜿門ｾ・
			$this->get_class_info();
			
			if(!$this->class_info){
				cutil::redirect_exit('community.php');
			}
			
			// 繝｡繝・そ繝ｼ繧ｸ騾∽ｿ｡蜃ｦ逅・
			if(isset($_POST['submit'])){
				$this->validate_message();
				if(empty($err_array)){
					$this->send_message();
				}
			}
			
			// 繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ蜿門ｾ・
			$this->get_messages();
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｯ繝ｩ繧ｹ諠・ｱ蜿門ｾ・
	@return	縺ｪ縺・
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
	@brief	繝｡繝・そ繝ｼ繧ｸ騾∽ｿ｡蜃ｦ逅・
	@return	縺ｪ縺・
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
			
			// 繝輔か繝ｼ繝繝ｪ繧ｻ繝・ヨ
			$_POST['message'] = '';
			$this->message_box = new ctextarea('message', '', 'class="form-control me-2" placeholder="繝｡繝・そ繝ｼ繧ｸ繧貞・蜉・ required rows="1"');
			
			// 繝壹・繧ｸ繝ｪ繝繧､繝ｬ繧ｯ繝・
			cutil::redirect_exit($_SERVER['REQUEST_URI']);
			
		} catch(PDOException $e){
			$this->error = '繝｡繝・そ繝ｼ繧ｸ縺ｮ騾∽ｿ｡縺ｫ螟ｱ謨励＠縺ｾ縺励◆縲・;
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ蜿門ｾ・
	@return	縺ｪ縺・
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
	@brief	繧ｨ繝ｩ繝ｼ蟄伜惠譁・ｭ怜・縺ｮ蜿門ｾ・
	@return	繧ｨ繝ｩ繝ｼ陦ｨ遉ｺ譁・ｭ怜・
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">蜈･蜉帙お繝ｩ繝ｼ縺後≠繧翫∪縺吶ょ推鬆・岼縺ｮ繧ｨ繝ｩ繝ｼ繧堤｢ｺ隱阪＠縺ｦ縺上□縺輔＞縲・/div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">蜃ｦ逅・↓螟ｱ謨励＠縺ｾ縺励◆縲ゅし繝昴・繝医ｒ遒ｺ隱堺ｸ九＆縺・・/div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｨ繝ｩ繝ｼ陦ｨ遉ｺ縺ｮ蜿門ｾ・
	@return	繧ｨ繝ｩ繝ｼ陦ｨ遉ｺ譁・ｭ怜・
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
	@brief	謌仙粥繝｡繝・そ繝ｼ繧ｸ陦ｨ遉ｺ縺ｮ蜿門ｾ・
	@return	謌仙粥繝｡繝・そ繝ｼ繧ｸ陦ｨ遉ｺ譁・ｭ怜・
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
	@brief	繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ荳隕ｧ陦ｨ遉ｺ縺ｮ蜿門ｾ・
	@return	繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ荳隕ｧ陦ｨ遉ｺ譁・ｭ怜・
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
	@brief	繝｡繝・そ繝ｼ繧ｸ繝舌Μ繝・・繧ｷ繝ｧ繝ｳ
	@return	縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	function validate_message(){
		global $err_array;
		global $err_flag;
		
		$message = isset($_POST['message']) ? trim($_POST['message']) : '';
		if(empty($message)){
			$err_array['message'] = '繝｡繝・そ繝ｼ繧ｸ繧貞・蜉帙＠縺ｦ縺上□縺輔＞縲・;
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝｡繝・そ繝ｼ繧ｸ繧ｨ繝ｩ繝ｼ陦ｨ遉ｺ縺ｮ蜿門ｾ・
	@return	繝｡繝・そ繝ｼ繧ｸ繧ｨ繝ｩ繝ｼ陦ｨ遉ｺ譁・ｭ怜・
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
	@brief  陦ｨ遉ｺ(邯呎価縺励※菴ｿ逕ｨ)
	@return 縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHP繝悶Ο繝・け邨ゆｺ・
?>
<!-- 繧ｳ繝ｳ繝・Φ繝・-->
<head>
    <meta charset="UTF-8">
    <title><?= display($this->class_info['class_name']); ?> - 繝√Ε繝・ヨ</title>
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
                <a href="class_calender.php?id=<?= $this->class_id; ?>" class="btn btn-outline-primary btn-sm ms-3">陦御ｺ倶ｺ亥ｮ夊｡ｨ</a>
            </div>
        </div>

        <!-- 繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ陦ｨ遉ｺ繧ｨ繝ｪ繧｢ -->
        <div class="chat-messages bg-light rounded p-3 mb-4 flex-grow-1" style="overflow-y: auto;">
            <?= $this->get_chat_messages(); ?>
        </div>

        <!-- 繝｡繝・そ繝ｼ繧ｸ蜈･蜉帙ヵ繧ｩ繝ｼ繝 -->
        <div class="mt-auto bg-white border-top py-3">
            <form method="post" class="d-flex align-items-center">
                <?= $this->message_box->get(false); ?>
                <button type="submit" name="submit" class="btn btn-primary">騾∽ｿ｡</button>
                <?= $this->get_message_error(); ?>
            </form>
        </div>
    </main>
</div>
</div>
<!-- /繧ｳ繝ｳ繝・Φ繝・-->

<script>
    // 繝√Ε繝・ヨ繧呈怙譁ｰ縺ｮ繝｡繝・そ繝ｼ繧ｸ縺ｾ縺ｧ繧ｹ繧ｯ繝ｭ繝ｼ繝ｫ
    window.onload = function() {
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };
</script>
<?php 
//PHP繝悶Ο繝・け蜀埼幕
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝・せ繝医Λ繧ｯ繧ｿ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//隕ｪ繧ｯ繝ｩ繧ｹ縺ｮ繝・せ繝医Λ繧ｯ繧ｿ繧貞他縺ｶ
		parent::__destruct();
	}
}

//繝壹・繧ｸ繧剃ｽ懈・
$page_obj = new cnode();
//繝倥ャ繝霑ｽ蜉
$page_obj->add_child(cutil::create('cheader'));
//繧ｵ繧､繝峨ヰ繝ｼ霑ｽ蜉
$page_obj->add_child(cutil::create('csidebar'));
//譛ｬ菴楢ｿｽ蜉
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//讒狗ｯ画凾蜃ｦ逅・
$page_obj->create();
//POST螟画焚縺ｮ繝・ヵ繧ｩ繝ｫ繝亥､繧偵そ繝・ヨ
$main_obj->post_default();
//譛ｬ菴灘ｮ溯｡鯉ｼ郁｡ｨ遉ｺ蜑榊・逅・ｼ・
$main_obj->execute();
//繝壹・繧ｸ蜈ｨ菴薙ｒ陦ｨ遉ｺ
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
                        class="btn btn-outline-primary btn-sm ms-3">陦御ｺ倶ｺ亥ｮ夊｡ｨ</a>
                </div>
            </div>

            <!-- 繝√Ε繝・ヨ繝｡繝・そ繝ｼ繧ｸ陦ｨ遉ｺ繧ｨ繝ｪ繧｢ -->
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

            <!-- 繝｡繝・そ繝ｼ繧ｸ蜈･蜉帙ヵ繧ｩ繝ｼ繝 -->
            <div class="mt-auto bg-white border-top">
                <form method="post" class="d-flex align-items-center">
                    <textarea class="form-control me-2" name="message" id="chatInput" rows="1" placeholder="繝｡繝・そ繝ｼ繧ｸ繧貞・蜉・
                        required></textarea>
                    <!-- +繧｢繧､繧ｳ繝ｳ縺ｨ繝峨Ο繝・・繝繧ｦ繝ｳ -->
                    <div class="dropdown">
                        <button class="btn btn-link px-2" type="button" id="templateDropdownBtn"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-plus fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="templateDropdownBtn"
                            style="max-height:200px;overflow-y:auto;">
                            <?php foreach ($templates as $template): ?>
                                <li>
                                    <!--繝・Φ繝励Ξ繝ｼ繝医ラ繝ｭ繝・・繝繧ｦ繝ｳ繝｡繝九Η繝ｼ-->
                                    <a class="dropdown-item template-insert-btn" href="#"
                                        data-body="<?php echo htmlspecialchars($template['temprate_text'], ENT_QUOTES); ?>">
                                        <?php echo htmlspecialchars($template['temprate_title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button type="submit" class="btn btn-primary">騾∽ｿ｡</button>
                </form>
            </div>


            <!--繝・Φ繝励Ξ繝ｼ繝医Δ繝ｼ繝繝ｫ-->
            <div class="modal fade" id="myModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">繝・Φ繝励Ξ繝ｼ繝育ｷｨ髮・・蟾ｮ縺玲崛縺・/h5>
                        </div>
                        <div class="modal-body" id="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">髢峨§繧・/button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 繝√Ε繝・ヨ繧呈怙譁ｰ縺ｮ繝｡繝・そ繝ｼ繧ｸ縺ｾ縺ｧ繧ｹ繧ｯ繝ｭ繝ｼ繝ｫ
        window.onload = function () {
            const chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };

        // 繝励Ξ繝ｼ繧ｹ繝帙Ν繝繧貞・驛ｨ謚懊″蜃ｺ縺・
        function extractPlaceholders(template) {
            const regex = /{([^{}]+)}/g;
            let match;
            const results = new Set();
            while ((match = regex.exec(template)) !== null) {
                results.add(match[1]);
            }
            return Array.from(results);
        }

        // 鄂ｮ謠・
        function fillTemplate(template, values) {
            return template.replace(/{([^{}]+)}/g, (m, key) => values[key] ?? m);
        }



        // 繝壹・繧ｸ繝ｭ繝ｼ繝画凾縺ｫ繝｢繝ｼ繝繝ｫ縺ｮ閭梧勹繧・ody繧ｯ繝ｩ繧ｹ縺梧ｮ九▲縺ｦ縺・◆繧画ｶ医☆
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.remove('modal-open');
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function (bd) { bd.parentNode.removeChild(bd); });

            // 繝・Φ繝励Ξ繝ｼ繝医ｒ蜈･蜉帶ｬ・↓謖ｿ蜈･縺吶ｋ
            document.querySelectorAll('.template-insert-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const originalTemplate = btn.getAttribute('data-body');
                    // 繝励Ξ繝ｼ繧ｹ繝帙Ν繝驥崎､・賜髯､・・et縺ｧOK・・
                    const regex = /{([^{}]+)}/g;
                    let match;
                    const placeholderSet = new Set();
                    while ((match = regex.exec(originalTemplate)) !== null) {
                        placeholderSet.add(match[1]);
                    }
                    const placeholders = Array.from(placeholderSet);

                    // 邱ｨ髮・庄閭ｽ譛ｬ譁・
                    let formHtml = `
            <div class="mb-3">
                <label>譛ｬ譁・ｼ域嶌縺肴鋤縺亥庄・・/label>
                <textarea id="modalTemplateText" class="form-control" rows="4">${originalTemplate}</textarea>
            </div>
            <form id="placeholderForm">
                <table class="table table-sm">
                  <thead><tr><th>鬆・岼</th><th>蛟､</th></tr></thead><tbody>
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
                <button type="submit" class="btn btn-primary mt-2">蜿肴丐</button>
            </form>
        `;
                    document.getElementById('modal-body').innerHTML = formHtml;
                    const modal = new bootstrap.Modal(document.getElementById('myModal'));
                    modal.show();

                    // 繧ｵ繝悶Α繝・ヨ譎・
                    document.getElementById('placeholderForm').onsubmit = function (e) {
                        e.preventDefault();
                        const values = {};
                        placeholders.forEach(ph => values[ph] = this.elements[ph].value);
                        // 譛譁ｰ縺ｮ譛ｬ譁・〒鄂ｮ謠・
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
<!-- /繧ｳ繝ｳ繝・Φ繝・-->

<script>
// 繝√Ε繝・ヨ繧呈怙譁ｰ縺ｮ繝｡繝・そ繝ｼ繧ｸ縺ｾ縺ｧ繧ｹ繧ｯ繝ｭ繝ｼ繝ｫ
window.onload = function () {
    const chatMessages = document.querySelector('.chat-messages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
};
</script>
<?php 
//PHP繝悶Ο繝・け蜀埼幕
}
