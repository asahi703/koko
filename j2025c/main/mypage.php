<?php
/*!
@file mypage.php
@brief ãã¤ããEã¸
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ã©ã¤ãã©ãªãã¤ã³ã¯ã«ã¼ãE
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	æ¬ä½ãã¼ãE
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $user;
	public $error;
	public $success;
	
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ã³ã³ã¹ãã©ã¯ã¿
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//è¦ªã¯ã©ã¹ã®ã³ã³ã¹ãã©ã¯ã¿ãå¼ã¶
		parent::__construct();
		$this->user = null;
		$this->error = '';
		$this->success = '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POSTå¤æ°ã®ãEã©ã«ãå¤ãã»ãE
	@return ãªãE
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("name",'');
		cutil::post_default("mail",'');
		cutil::post_default("password",'');
		cutil::post_default("password_confirm",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	æ§ç¯æã®å¦çEç¶æ¿ãã¦ä½¿ç¨)
	@return	ãªãE
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// ã»ãE·ã§ã³æE ±ã®åå¾E
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  æ¬ä½å®è¡ï¼è¡¨ç¤ºååEçE¼E
	@return ãªãE
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo 'ããEã¸ãç¡å¹ã§ãE;
			exit();
		}
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'update':
					//ãã©ã¡ã¼ã¿ã®ãã§ãE¯
					$this->paramchk();
					if($err_flag != 0){
						$this->error = 'å¥åã¨ã©ã¼ãããã¾ããE;
					}
					else{
						$this->update_user();
					}
				break;
				default:
					//éå¸¸ã¯ããããªãE
					echo 'åå ä¸æEã®ã¨ã©ã¼ã§ããE;
					exit;
				break;
			}
		}
		else{
			// åæè¡¨ç¤ºæã«ã¦ã¼ã¶ã¼æE ±ãPOSTã«ã»ãE
			$_POST['name'] = $this->user['name'];
			$_POST['mail'] = $this->user['mail'];
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ãã©ã¡ã¼ã¿ã®ãã§ãE¯
	@return	ã¨ã©ã¼ã®å ´åãEfalseãè¿ã
	*/
	//--------------------------------------------------------------------------------------
	function paramchk(){
		global $err_array;
		global $err_flag;
		
		/// ååã®å­å¨ã¨ç©ºç½ãã§ãE¯
		if(cutil_ex::chkset_err_field($err_array,'name','åå','isset_nl')){
			$err_flag = 1;
		}
		
		/// ã¡ã¼ã«ã¢ãã¬ã¹ã®å­å¨ã¨ç©ºç½ãã§ãE¯
		if(cutil_ex::chkset_err_field($err_array,'mail','ã¡ã¼ã«ã¢ãã¬ã¹','isset_nl')){
			$err_flag = 1;
		}
		
		// ãã¹ã¯ã¼ãç¢ºèªE
		if(!empty($_POST['password'])){
			if($_POST['password'] != $_POST['password_confirm']){
				$err_array['password_confirm'] = 'ãã¹ã¯ã¼ããä¸è´ãã¾ãããE;
				$err_flag = 1;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ã¦ã¼ã¶ã¼æE ±æ´æ°å¦çE
	@return	ãªãE
	*/
	//--------------------------------------------------------------------------------------
	function update_user(){
		try {
			$users_obj = new cusers();
			$dataarr = array(
				'user_name' => $_POST['name'],
				'user_mailaddress' => $_POST['mail'],
				'user_login' => $_POST['mail']
			);
			
			// ãã¹ã¯ã¼ããå¥åããã¦ãEå ´åãEæ´æ°
			if(!empty($_POST['password'])){
				$dataarr['user_password'] = sha1($_POST['password']);
			}
			
			$where = 'user_id = :user_id';
			$wherearr[':user_id'] = $this->user['user_id'];
			
			$result = $users_obj->update_core(false, 'users', $dataarr, $where, $wherearr, false);
			
			// ã»ãE·ã§ã³æE ±ãæ´æ°
			$_SESSION['user']['name'] = $_POST['name'];
			$_SESSION['user']['mail'] = $_POST['mail'];
			
			$this->success = 'ã¢ã«ã¦ã³ãæå ±ãæ´æ°ãã¾ãããE;
			// ã¦ã¼ã¶ã¼æE ±ãåEåå¾E
			$this->user = get_login_user();
		} catch (Exception $e) {
			$this->error = 'æ´æ°ã«å¤±æãã¾ãããE;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ã¨ã©ã¼å­å¨æE­åEã®åå¾E
	@return	ã¨ã©ã¼è¡¨ç¤ºæE­åE
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">å¥åã¨ã©ã¼ãããã¾ããåé E®ã®ã¨ã©ã¼ãç¢ºèªãã¦ãã ãããE/div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">å¦çE«å¤±æãã¾ããããµããEããç¢ºèªä¸ããEE/div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ã¨ã©ã¼è¡¨ç¤ºã®åå¾E
	@return	ã¨ã©ã¼è¡¨ç¤ºæE­åE
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
	@brief	æåã¡ãE»ã¼ã¸è¡¨ç¤ºã®åå¾E
	@return	æåã¡ãE»ã¼ã¸è¡¨ç¤ºæE­åE
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
	@brief	ååå¥åé E®ã®åå¾E
	@return	ååå¥åé E®æE­åE
	*/
	//--------------------------------------------------------------------------------------
	function get_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('name',$_POST['name'],'class="form-control shadow-sm" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['name']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ã¡ã¼ã«ã¢ãã¬ã¹å¥åé E®ã®åå¾E
	@return	ã¡ã¼ã«ã¢ãã¬ã¹å¥åé E®æE­åE
	*/
	//--------------------------------------------------------------------------------------
	function get_mail(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('mail',$_POST['mail'],'type="email" class="form-control shadow-sm" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['mail'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['mail']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ãã¹ã¯ã¼ãåEåé E®ã®åå¾E
	@return	ãã¹ã¯ã¼ãåEåé E®æE­åE
	*/
	//--------------------------------------------------------------------------------------
	function get_password(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('password','','type="password" class="form-control shadow-sm"');
		$ret_str = $tgt->get(false);
		if(isset($err_array['password'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['password']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ãã¹ã¯ã¼ãç¢ºèªåEåé E®ã®åå¾E
	@return	ãã¹ã¯ã¼ãç¢ºèªåEåé E®æE­åE
	*/
	//--------------------------------------------------------------------------------------
	function get_password_confirm(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('password_confirm','','type="password" class="form-control shadow-sm"');
		$ret_str = $tgt->get(false);
		if(isset($err_array['password_confirm'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['password_confirm']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	æå¸«ã¡ãã¥ã¼ã®åå¾E
	@return	æå¸«ã¡ãã¥ã¼æE­åE
	*/
	//--------------------------------------------------------------------------------------
	function get_teacher_menu(){
		if(!empty($this->user['user_is_teacher'])){
			return <<<END_BLOCK
<hr>
<div class="mt-3">
    <a href="create_teacher.php" class="btn btn-info">æå¸«ã¢ã«ã¦ã³ãä½æEããEã¸ã¸</a>
</div>
END_BLOCK;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  è¡¨ç¤º(ç¶æ¿ãã¦ä½¿ç¨)
	@return ãªãE
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPãã­ãE¯çµäºE
    // ç»åã¢ãEEã­ã¼ãåEçE
    $icon_filename = $user['user_icon'] ?? '';
    if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['user_icon']['tmp_name'];
        $orig_name = $_FILES['user_icon']['name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        
        // ãã¡ã¤ã«ãµã¤ãºãã§ãE¯EEMBå¶éï¼E
        if ($_FILES['user_icon']['size'] > 2 * 1024 * 1024) {
            $error = 'ç»åãã¡ã¤ã«ã®ãµã¤ãºã¯2MBä»¥ä¸ã«ãã¦ãã ãããE;
        } else if (empty($ext)) {
            $error = 'ç»åãã¡ã¤ã«ã®æ¡å¼µå­ãåå¾ã§ãã¾ãããjpg, jpeg, png, gif å½¢å¼ã§ã¢ãEEã­ã¼ããã¦ãã ãããE;
        } else if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            // ä¿å­åEãE£ã¬ã¯ããªã®ãã¹è¨­å®E
            $icon_dir_relative = 'img/user_icons/';
            $icon_dir_absolute = __DIR__ . '/../' . $icon_dir_relative;
            
            // ãE£ã¬ã¯ããªå­å¨ç¢ºèªã¨ä½æE
            if (!is_dir($icon_dir_absolute)) {
                if (!mkdir($icon_dir_absolute, 0755, true)) {
                    $error = 'ã¢ãEEã­ã¼ãç¨ãE£ã¬ã¯ããªãä½æEã§ãã¾ããã§ãããç®¡çEE«ãåãEãããã ãããE;
                }
            }
            
            if (empty($error)) {
                // user_idãä½¿ã£ã¦ãã¡ã¤ã«åãçæE
                $filename = ($user['user_id'] ?? '') . '.' . $ext;
                $icon_filename = $icon_dir_relative . $filename;
                $save_path = $icon_dir_absolute . $filename;
                
                // æ¢å­ç»åãEåé¤å¦çE
                foreach (['jpg', 'jpeg', 'png', 'gif'] as $old_ext) {
                    $old_file = $icon_dir_absolute . ($user['user_id'] ?? '') . '.' . $old_ext;
                    if (file_exists($old_file)) {
                        @unlink($old_file);
                    }
                }
                
                // ãã¡ã¤ã«ã¢ãEEã­ã¼ãE
                if (move_uploaded_file($tmp_name, $save_path)) {
                    // æåããããã¼ããã·ã§ã³è¨­å®E
                    chmod($save_path, 0644);
                    
                    // ã¢ãEEã­ã¼ãç¢ºèªE
                    if (!file_exists($save_path)) {
                        $error = "ãã¡ã¤ã«ã®ä¿å­ã«å¤±æãã¾ãããE;
                    }
                } else {
                    $error = 'ç»åãEä¿å­ã«å¤±æãã¾ãããç®¡çEE«ãåãEãããã ãããE;
                }
            }
        } else {
            $error = 'ç»åãE jpg, jpeg, png, gif ã®ã¿å¯¾å¿ã§ããE;
        }
    } else if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] !== UPLOAD_ERR_NO_FILE) {
        // ã¢ãEEã­ã¼ãã¨ã©ã¼ã®è©³ç´°
        switch ($_FILES['user_icon']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error = 'ãã¡ã¤ã«ãµã¤ãºãå¤§ãããã¾ããE;
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = 'ãã¡ã¤ã«ã®ã¢ãEEã­ã¼ããä¸­æ­ããã¾ãããE;
                break;
            default:
                $error = 'ãã¡ã¤ã«ã®ã¢ãEEã­ã¼ãã«å¤±æãã¾ãããE;
        }
    }

    if (empty($name) || empty($mail)) {
        $error = 'ååã¨ã¡ã¼ã«ã¢ãã¬ã¹ã¯å¿E ã§ããE;
    } else if (empty($error)) {
        try {
            $db = new cdb();
            $sql = 'UPDATE users SET user_name = ?, user_mailaddress = ?, user_login = ?';
            $params = [$name, $mail, $mail];

            if (!empty($password)) {
                if ($password !== $password_confirm) {
                    $error = 'ãã¹ã¯ã¼ããä¸è´ãã¾ãããE;
                } else {
                    $sql .= ', user_password = ?';
                    $params[] = sha1($password);
                }
            }
            // ã¢ã¤ã³ã³æ´æ°Eãã¡ã¤ã«ãã¢ãEEã­ã¼ããããå ´åãEã¿EE
            if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                $sql .= ', user_icon = ?';
                $params[] = $icon_filename;
            }

            if (empty($error)) {
                $sql .= ' WHERE user_id = ?';
                $params[] = $user['user_id'] ?? 0;

                $stmt = $db->prepare($sql);
                $stmt->execute($params);

                // ã»ãE·ã§ã³æE ±ãæ´æ°
                $_SESSION['user']['user_name'] = $name;
                $_SESSION['user']['user_mailaddress'] = $mail;
                if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                    $_SESSION['user']['user_icon'] = $icon_filename;
                }

                $success = 'ã¢ã«ã¦ã³ãæå ±ãæ´æ°ãã¾ãããE;
                $user = get_login_user();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                 $error = 'ããEã¡ã¼ã«ã¢ãã¬ã¹ã¯æ¢ã«ä½¿ç¨ããã¦ãE¾ããE;
            } else {
                 $error = 'æ´æ°ã«å¤±æãã¾ãããE;
            }
        }
    }
}

// HTMLåºåE
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<!-- ã³ã³ãE³ãE-->
<head>
    <title>ãã¤ããEã¸</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>

<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">ãã¤ããEã¸</h3>
                <p class="card-text">ã¢ã«ã¦ã³ãæå ±ãç·¨éEã¾ããE/p>

                <?= $this->get_error_display(); ?>
                <?= $this->get_success_display(); ?>

                <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="mt-4" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <label class="form-label">ãã­ãã£ã¼ã«ç»åE/label><br>
                        <?php
                        $user_icon = $user['user_icon'] ?? '';
                        $icon_path = !empty($user_icon) && file_exists(__DIR__ . '/../' . $user_icon)
                            ? $user_icon
                            : 'main/img/headerImg/account.png';
                        ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($icon_path); ?>" alt="ãã­ãã£ã¼ã«ç»åE 
                                 style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #667eea; margin-bottom:10px;" 
                                 id="profilePreview">
                        </div>
                        <input type="file" name="user_icon" accept="image/*" class="form-control mt-2" 
                               style="max-width:300px; margin:auto;" id="iconInput">
                        <small class="form-text text-muted">jpg, jpeg, png, gifå½¢å¼ï¼EMBä»¥ä¸ï¼E/small>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('iconInput');
                        const preview = document.getElementById('profilePreview');
                        
                        input.addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            if (file) {
                                // ãã¡ã¤ã«ãµã¤ãºãã§ãE¯EEMBå¶éï¼E
                                if (file.size > 2 * 1024 * 1024) {
                                    alert('ãã¡ã¤ã«ãµã¤ãºã¯2MBä»¥ä¸ã«ãã¦ãã ãããE);
                                    this.value = '';
                                    return;
                                }
                                
                                // ãã¡ã¤ã«å½¢å¼ãã§ãE¯
                                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                                if (!allowedTypes.includes(file.type)) {
                                    alert('jpg, jpeg, png, gifå½¢å¼ãEãã¡ã¤ã«ãã¢ãEEã­ã¼ããã¦ãã ãããE);
                                    this.value = '';
                                    return;
                                }
                                
                                const reader = new FileReader();
                                reader.onload = function(ev) {
                                    preview.src = ev.target.result;
                                };
                                reader.readAsDataURL(file);
                            }
                        });
                    });
                    </script>
                    <div class="mb-3 text-start">
                        <label for="name" class="form-label">åå<span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="name" name="name" value="<?php echo htmlspecialchars($user['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">ã¡ã¼ã«ã¢ãã¬ã¹<span class="text-danger">*</span></label>
                        <input type="email" class="form-control shadow-sm" id="mail" name="mail" value="<?php echo htmlspecialchars($user['user_mailaddress'] ?? ''); ?>" required>
                        <label for="password" class="form-label">æ°ãããã¹ã¯ã¼ãE(å¤æ´ããå ´åãEã¿)</label>
                        <?= $this->get_password(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">æ°ãããã¹ã¯ã¼ãE(ç¢ºèªE</label>
                        <?= $this->get_password_confirm(); ?>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary px-5 mt-3" onClick="set_func_form('update','')">æ´æ°</button>
                    </div>
                    <input type="hidden" name="func" value="" />
                    <input type="hidden" name="param" value="" />
                </form>
                
                <?= $this->get_teacher_menu(); ?>
            </div>
        </div>
    </main>
</div>
</div>
<!-- /ã³ã³ãE³ãE-->
<script>
// prefecture_detail.phpã¹ã¿ã¤ã«ã®ãã©ã¼ã æä½é¢æ°
function set_func_form(func, param) {
    document.form1.func.value = func;
    document.form1.param.value = param;
    document.form1.submit();
}
</script>
<?php 
//PHPãã­ãE¯åé
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ãE¹ãã©ã¯ã¿
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//è¦ªã¯ã©ã¹ã®ãE¹ãã©ã¯ã¿ãå¼ã¶
		parent::__destruct();
	}
}

//ããEã¸ãä½æE
$page_obj = new cnode();
//ãããè¿½å 
$page_obj->add_child(cutil::create('cheader'));
//ãµã¤ããã¼è¿½å 
$page_obj->add_child(cutil::create('csidebar'));
//æ¬ä½è¿½å 
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//æ§ç¯æå¦çE
$page_obj->create();
//POSTå¤æ°ã®ãEã©ã«ãå¤ãã»ãE
$main_obj->post_default();
//æ¬ä½å®è¡ï¼è¡¨ç¤ºååEçE¼E
$main_obj->execute();
//ããEã¸å¨ä½ãè¡¨ç¤º
$page_obj->display();

?>
