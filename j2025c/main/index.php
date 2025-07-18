<?php
/*!
@file index.php
@brief 繝ｭ繧ｰ繧､繝ｳ逕ｻ髱｢
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
		cutil::post_default("user_mailaddress",'');
		cutil::post_default("user_password",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	讒狗ｯ画凾縺ｮ蜃ｦ逅・邯呎価縺励※菴ｿ逕ｨ)
	@return	縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
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
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'login':
					//繝代Λ繝｡繝ｼ繧ｿ縺ｮ繝√ぉ繝・け
					$this->paramchk();
					if($err_flag != 0){
						$this->error = '蜈･蜉帙お繝ｩ繝ｼ縺後≠繧翫∪縺吶・;
					}
					else{
						$this->login_user();
					}
				break;
				default:
					//騾壼ｸｸ縺ｯ縺ゅｊ縺医↑縺・
					echo '蜴溷屏荳肴・縺ｮ繧ｨ繝ｩ繝ｼ縺ｧ縺吶・;
					exit;
				break;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝代Λ繝｡繝ｼ繧ｿ縺ｮ繝√ぉ繝・け
	@return	繧ｨ繝ｩ繝ｼ縺ｮ蝣ｴ蜷医・false繧定ｿ斐☆
	*/
	//--------------------------------------------------------------------------------------
	function paramchk(){
		global $err_array;
		global $err_flag;
		
		/// 繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ縺ｮ蟄伜惠縺ｨ遨ｺ逋ｽ繝√ぉ繝・け
		if(cutil_ex::chkset_err_field($err_array,'user_mailaddress','繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ','isset_nl')){
			$err_flag = 1;
		}
		
		/// 繝代せ繝ｯ繝ｼ繝峨・蟄伜惠縺ｨ遨ｺ逋ｽ繝√ぉ繝・け
		if(cutil_ex::chkset_err_field($err_array,'user_password','繝代せ繝ｯ繝ｼ繝・,'isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝ｭ繧ｰ繧､繝ｳ蜃ｦ逅・
	@return	縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	function login_user(){
		try {
			$users_obj = new cusers();
			$user = $users_obj->authenticate_user(false, $_POST['user_mailaddress'], sha1($_POST['user_password']));
			
			if($user){
				// 繝ｭ繧ｰ繧､繝ｳ謌仙粥
				require_once(__DIR__ . '/common/session.php');
				login_user([
					'uuid' => $user['user_id'],
					'user_id' => $user['user_id'],
					'name' => $user['user_name'],
					'mail' => $user['user_mailaddress'],
					'user_is_teacher' => $user['user_is_teacher']
				]);
				cutil::redirect_exit('community.php');
			} else {
				$this->error = '繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ縺ｾ縺溘・繝代せ繝ｯ繝ｼ繝峨′驕輔＞縺ｾ縺吶・;
			}
		} catch (Exception $e) {
			$this->error = '繝ｭ繧ｰ繧､繝ｳ蜃ｦ逅・〒繧ｨ繝ｩ繝ｼ縺檎匱逕溘＠縺ｾ縺励◆縲・;
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

<div class="alert alert-danger">蜈･蜉帙お繝ｩ繝ｼ縺後≠繧翫∪縺吶ょ推鬆・岼縺ｮ繧ｨ繝ｩ繝ｼ繧堤｢ｺ隱阪＠縺ｦ縺上□縺輔＞縲・/div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger">蜃ｦ逅・↓螟ｱ謨励＠縺ｾ縺励◆縲ゅし繝昴・繝医ｒ遒ｺ隱堺ｸ九＆縺・・/div>
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
			return '<div class="alert alert-danger">' . display($this->error) . '</div>';
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
			return '<div class="alert alert-success">' . display($this->success) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ蜈･蜉幃・岼縺ｮ蜿門ｾ・
	@return	繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ蜈･蜉幃・岼譁・ｭ怜・
	*/
	//--------------------------------------------------------------------------------------
	function get_user_mailaddress(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('user_mailaddress',$_POST['user_mailaddress'],'type="email" class="form-control" placeholder="繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['user_mailaddress'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['user_mailaddress']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝代せ繝ｯ繝ｼ繝牙・蜉幃・岼縺ｮ蜿門ｾ・
	@return	繝代せ繝ｯ繝ｼ繝牙・蜉幃・岼譁・ｭ怜・
	*/
	//--------------------------------------------------------------------------------------
	function get_user_password(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('user_password','','type="password" class="form-control" placeholder="繝代せ繝ｯ繝ｼ繝・ required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['user_password'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['user_password']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  陦ｨ遉ｺ(邯呎価縺励※菴ｿ逕ｨ)
	@return 縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHP繝悶Ο繝・け邨ゆｺ・
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['user_mailaddress'] ?? '';
    $pass = $_POST['user_password'] ?? '';
    if ($mail && $pass) {
        try {
            $db = new cdb();
            $stmt = $db->prepare('SELECT * FROM users WHERE user_mailaddress = ?');
            $stmt->execute([$mail]);
            $user = $stmt->fetch();
            if ($user && $user['user_password'] === sha1($pass)) {
                // 譛邨ゅΟ繧ｰ繧､繝ｳ譎る俣繧呈峩譁ｰ
                try {
                    $update_stmt = $db->prepare('UPDATE users SET last_login_time = NOW() WHERE user_id = ?');
                    $update_stmt->execute([$user['user_id']]);
                } catch (Exception $e) {
                    // 繝ｭ繧ｰ繧､繝ｳ譎る俣縺ｮ譖ｴ譁ｰ縺ｫ螟ｱ謨励＠縺ｦ繧ゅΟ繧ｰ繧､繝ｳ蜃ｦ逅・・邯咏ｶ・
                    error_log('Failed to update last login time: ' . $e->getMessage());
                }
                
                // 繝ｭ繧ｰ繧､繝ｳ謌仙粥
                login_user([
                    'uuid' => $user['user_id'],
                    'user_id' => $user['user_id'],
                    'user_name' => $user['user_name'],
                    'user_mailaddress' => $user['user_mailaddress'],
                    'user_icon' => $user['user_icon'],
                    'user_is_teacher' => $user['user_is_teacher'],
                    // 蠕梧婿莠呈鋤諤ｧ縺ｮ縺溘ａ
                    'name' => $user['user_name'],
                    'mail' => $user['user_mailaddress']
                ]);
                header('Location: community.php');
                exit;
            } else {
                $error = '繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ縺ｾ縺溘・繝代せ繝ｯ繝ｼ繝峨′驕輔＞縺ｾ縺吶・;
            }
        } catch (Exception $e) {
            $error = '繝ｭ繧ｰ繧､繝ｳ蜃ｦ逅・〒繧ｨ繝ｩ繝ｼ縺檎匱逕溘＠縺ｾ縺励◆縲・;
        }
    } else {
        $error = '繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ縺ｨ繝代せ繝ｯ繝ｼ繝峨ｒ蜈･蜉帙＠縺ｦ縺上□縺輔＞縲・;
    }
}
?>
<!-- 繧ｳ繝ｳ繝・Φ繝・-->
<head>
    <title>繝ｭ繧ｰ繧､繝ｳ</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>

<div class="main-content-wrapper">
    <div class="container d-flex flex-column align-items-center justify-content-center vw-100">
        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-md-3">
            <div class="mb-3">
                <h2>繝ｭ繧ｰ繧､繝ｳ</h2>
            </div>
            
            <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="border rounded shadow p-4 w-100 login-form">
                <div class="form-group my-2 px-2 w-100">
                    <label>繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ <span class="text-danger fw-bold">*</span></label>
                    <?= $this->get_user_mailaddress(); ?>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>繝代せ繝ｯ繝ｼ繝・<span class="text-danger fw-bold">*</span></label>
                    <?= $this->get_user_password(); ?>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <button type="button" class="btn btn-primary w-100" onClick="set_func_form('login','')">繝ｭ繧ｰ繧､繝ｳ</button>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <a href="signin.php">譁ｰ隕冗匳骭ｲ縺ｯ縺薙■繧・/a>
                </div>
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
            </form>
        </div>
    </div>
</div>
</div>
<!-- /繧ｳ繝ｳ繝・Φ繝・-->
<script>
// prefecture_detail.php繧ｹ繧ｿ繧､繝ｫ縺ｮ繝輔か繝ｼ繝謫堺ｽ憺未謨ｰ
function set_func_form(func, param) {
    document.form1.func.value = func;
    document.form1.param.value = param;
    document.form1.submit();
}
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
//繧ｷ繝ｳ繝励Ν繝倥ャ繝霑ｽ蜉
$page_obj->add_child(cutil::create('csimple_header'));
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
