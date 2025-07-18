<?php
/*!
@file faq_create.php
@brief 繧医￥縺ゅｋ雉ｪ蝠丈ｽ懈・
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
	public $title_box;
	public $content_box;
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
		$this->title_box = null;
		$this->content_box = null;
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
		if(!isset($_POST['title'])){
			$_POST['title'] = "";
		}
		if(!isset($_POST['content'])){
			$_POST['content'] = "";
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
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
		
		//繝輔か繝ｼ繝繝懊ャ繧ｯ繧ｹ菴懈・
		$this->title_box = new ctextbox('title', 'form-control', 255);
		$this->title_box->set_required(true);
		
		$this->content_box = new ctextarea('content', 'form-control', 5);
		$this->content_box->set_required(true);
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
			// POST繝・・繧ｿ縺ｮ讀懆ｨｼ
			$this->title_box->validate();
			$this->content_box->validate();
			
			// 騾∽ｿ｡繝懊ち繝ｳ縺梧款縺輔ｌ縺溷ｴ蜷・
			if(isset($_POST['submit'])){
				if(empty($err_array)){
					// FAQ雉ｪ蝠上ｒ騾∽ｿ｡縺吶ｋ蜃ｦ逅・
					$this->submit_faq();
				}
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	FAQ雉ｪ蝠城∽ｿ｡蜃ｦ逅・
	@return	縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	function submit_faq(){
		global $err_flag;
		
		try{
			$title = $_POST['title'];
			$content = $_POST['content'];
			
			// 螳滄圀縺ｮFAQ雉ｪ蝠城∽ｿ｡蜃ｦ逅・ｒ縺薙％縺ｫ螳溯｣・
			// 迴ｾ蝨ｨ縺ｯ繧ｵ繝ｳ繝励Ν縺ｨ縺励※謌仙粥繝｡繝・そ繝ｼ繧ｸ縺ｮ縺ｿ陦ｨ遉ｺ
			$this->success = '雉ｪ蝠上ｒ騾∽ｿ｡縺励∪縺励◆縲ょ屓遲斐∪縺ｧ縺励・繧峨￥縺雁ｾ・■縺上□縺輔＞縲・;
			
			// 繝輔か繝ｼ繝繧偵Μ繧ｻ繝・ヨ
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
	@brief  陦ｨ遉ｺ(邯呎価縺励※菴ｿ逕ｨ)
	@return 縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHP繝悶Ο繝・け邨ゆｺ・
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

// 繝輔か繝ｼ繝騾∽ｿ｡蜃ｦ逅・
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $question = trim($_POST['question'] ?? '');
    
    if (empty($title)) {
        $error = '繧ｿ繧､繝医Ν繧貞・蜉帙＠縺ｦ縺上□縺輔＞縲・;
    } elseif (empty($question)) {
        $error = '雉ｪ蝠丞・螳ｹ繧貞・蜉帙＠縺ｦ縺上□縺輔＞縲・;
    } else {
        try {
            // 繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ縺ｮ繝・ヰ繝・げ
            error_log('FAQ Create - User Info: ' . print_r($user, true));
            error_log('FAQ Create - User ID: ' . ($user['uuid'] ?? 'NULL'));
            
            // user_id縺悟ｭ伜惠縺励↑縺・ｴ蜷医・繧ｨ繝ｩ繝ｼ繝上Φ繝峨Μ繝ｳ繧ｰ
            if (!isset($user['uuid']) || empty($user['uuid'])) {
                $error = '繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ縺梧ｭ｣縺励￥蜿門ｾ励〒縺阪∪縺帙ｓ縺ｧ縺励◆縲ょ・蠎ｦ繝ｭ繧ｰ繧､繝ｳ縺励※縺上□縺輔＞縲・;
                error_log('FAQ Create Error: User UUID is missing or empty');
            } else {
                $db = new cdb();
                $stmt = $db->prepare('
                    INSERT INTO faq (faq_title, faq_question, faq_user_id, faq_created_at) 
                    VALUES (?, ?, ?, NOW())
                ');
                
                if ($stmt->execute([$title, $question, $user['uuid']])) {
                    $faq_id = $db->lastInsertId();
                    
                    // 謨吝ｸｫ縺ｸ縺ｮ雉ｪ蝠城夂衍繧帝∽ｿ｡
                    $questioner_name = $user['user_name'] ?? $user['name'] ?? '繝ｦ繝ｼ繧ｶ繝ｼ';
                    notify_faq_question($faq_id, $user['uuid'], $questioner_name, $title);
                    
                    $message = '雉ｪ蝠上ｒ謚慕ｨｿ縺励∪縺励◆縲ょ屓遲斐ｒ縺雁ｾ・■縺上□縺輔＞縲・;
                    $title = ''; // 繝輔か繝ｼ繝繧偵Μ繧ｻ繝・ヨ
                    $question = ''; // 繝輔か繝ｼ繝繧偵Μ繧ｻ繝・ヨ
                    error_log('FAQ Created successfully with user_id: ' . $user['uuid']);
                } else {
                    $error = '雉ｪ蝠上・謚慕ｨｿ縺ｫ螟ｱ謨励＠縺ｾ縺励◆縲ゅｂ縺・ｸ蠎ｦ縺願ｩｦ縺励￥縺縺輔＞縲・;
                    // 繝・ヰ繝・げ諠・ｱ
                    error_log('FAQ Insert Error: ' . print_r($stmt->errorInfo(), true));
                }
            }
        } catch (Exception $e) {
            $error = '雉ｪ蝠上・謚慕ｨｿ荳ｭ縺ｫ繧ｨ繝ｩ繝ｼ縺檎匱逕溘＠縺ｾ縺励◆縲・;
            // 繝・ヰ繝・げ諠・ｱ
            error_log('FAQ Exception: ' . $e->getMessage());
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<!-- 繧ｳ繝ｳ繝・Φ繝・-->
<head>
    <title>繧医￥縺ゅｋ雉ｪ蝠丈ｽ懈・</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 40px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">雉ｪ蝠上・菴輔〒縺吶°・・/h2>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success mx-auto" style="max-width: 800px;" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                <div class="mt-2">
                    <a href="faq.php" class="btn btn-sm btn-outline-success">FAQ繝壹・繧ｸ縺ｧ遒ｺ隱・/a>
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
                        <label for="title" class="form-label">繧ｿ繧､繝医Ν<span class="text-danger">*</span></label>
                        <?= $this->title_box->get_tag(); ?>
                        <?= $this->title_box->get_error_message_tag(); ?>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">雉ｪ蝠丞・螳ｹ<span class="text-danger">*</span></label>
                        <?= $this->content_box->get_tag(); ?>
                        <?= $this->content_box->get_error_message_tag(); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary px-5">騾∽ｿ｡</button>
                    <div class="mb-3">
                        <label for="title" class="form-label">繧ｿ繧､繝医Ν<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="雉ｪ蝠上・繧ｿ繧､繝医Ν繧貞・蜉帙＠縺ｦ縺上□縺輔＞..." maxlength="100" required 
                               value="<?php echo htmlspecialchars($title ?? ''); ?>">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            雉ｪ蝠丞・螳ｹ繧堤ｰ｡貎斐↓陦ｨ縺吶ち繧､繝医Ν繧偵▽縺代※縺上□縺輔＞・域怙螟ｧ100譁・ｭ暦ｼ・
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="question" class="form-label">雉ｪ蝠丞・螳ｹ<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="question" name="question" rows="6" 
                                  placeholder="縺薙％縺ｫ雉ｪ蝠上ｒ蜈･蜉帙＠縺ｦ縺上□縺輔＞..." required><?php echo htmlspecialchars($question ?? ''); ?></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            縺ｧ縺阪ｋ縺縺大・菴鍋噪縺ｫ雉ｪ蝠丞・螳ｹ繧定ｨ倩ｼ峨＠縺ｦ縺上□縺輔＞縲ょ屓遲斐′譌ｩ縺上↑繧翫∪縺吶・
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-paper-plane me-2"></i>雉ｪ蝠上ｒ騾∽ｿ｡
                        </button>
                        <a href="faq.php" class="btn btn-outline-secondary px-5 ms-3">
                            <i class="fas fa-arrow-left me-2"></i>謌ｻ繧・
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
</div>
<!-- /繧ｳ繝ｳ繝・Φ繝・-->
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
