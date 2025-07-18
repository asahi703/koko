<?php
/*!
@file mypage.php
@brief 繝槭う繝壹・繧ｸ
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
		cutil::post_default("name",'');
		cutil::post_default("mail",'');
		cutil::post_default("password",'');
		cutil::post_default("password_confirm",'');
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
				case 'update':
					//繝代Λ繝｡繝ｼ繧ｿ縺ｮ繝√ぉ繝・け
					$this->paramchk();
					if($err_flag != 0){
						$this->error = '蜈･蜉帙お繝ｩ繝ｼ縺後≠繧翫∪縺吶・;
					}
					else{
						$this->update_user();
					}
				break;
				default:
					//騾壼ｸｸ縺ｯ縺ゅｊ縺医↑縺・
					echo '蜴溷屏荳肴・縺ｮ繧ｨ繝ｩ繝ｼ縺ｧ縺吶・;
					exit;
				break;
			}
		}
		else{
			// 蛻晄悄陦ｨ遉ｺ譎ゅ↓繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ繧単OST縺ｫ繧ｻ繝・ヨ
			$_POST['name'] = $this->user['name'];
			$_POST['mail'] = $this->user['mail'];
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
		
		/// 蜷榊燕縺ｮ蟄伜惠縺ｨ遨ｺ逋ｽ繝√ぉ繝・け
		if(cutil_ex::chkset_err_field($err_array,'name','蜷榊燕','isset_nl')){
			$err_flag = 1;
		}
		
		/// 繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ縺ｮ蟄伜惠縺ｨ遨ｺ逋ｽ繝√ぉ繝・け
		if(cutil_ex::chkset_err_field($err_array,'mail','繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ','isset_nl')){
			$err_flag = 1;
		}
		
		// 繝代せ繝ｯ繝ｼ繝臥｢ｺ隱・
		if(!empty($_POST['password'])){
			if($_POST['password'] != $_POST['password_confirm']){
				$err_array['password_confirm'] = '繝代せ繝ｯ繝ｼ繝峨′荳閾ｴ縺励∪縺帙ｓ縲・;
				$err_flag = 1;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ譖ｴ譁ｰ蜃ｦ逅・
	@return	縺ｪ縺・
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
			
			// 繝代せ繝ｯ繝ｼ繝峨′蜈･蜉帙＆繧後※縺・ｋ蝣ｴ蜷医・譖ｴ譁ｰ
			if(!empty($_POST['password'])){
				$dataarr['user_password'] = sha1($_POST['password']);
			}
			
			$where = 'user_id = :user_id';
			$wherearr[':user_id'] = $this->user['user_id'];
			
			$result = $users_obj->update_core(false, 'users', $dataarr, $where, $wherearr, false);
			
			// 繧ｻ繝・す繝ｧ繝ｳ諠・ｱ繧よ峩譁ｰ
			$_SESSION['user']['name'] = $_POST['name'];
			$_SESSION['user']['mail'] = $_POST['mail'];
			
			$this->success = '繧｢繧ｫ繧ｦ繝ｳ繝域ュ蝣ｱ繧呈峩譁ｰ縺励∪縺励◆縲・;
			// 繝ｦ繝ｼ繧ｶ繝ｼ諠・ｱ繧貞・蜿門ｾ・
			$this->user = get_login_user();
		} catch (Exception $e) {
			$this->error = '譖ｴ譁ｰ縺ｫ螟ｱ謨励＠縺ｾ縺励◆縲・;
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
	@brief	蜷榊燕蜈･蜉幃・岼縺ｮ蜿門ｾ・
	@return	蜷榊燕蜈･蜉幃・岼譁・ｭ怜・
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
	@brief	繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ蜈･蜉幃・岼縺ｮ蜿門ｾ・
	@return	繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ蜈･蜉幃・岼譁・ｭ怜・
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
	@brief	繝代せ繝ｯ繝ｼ繝牙・蜉幃・岼縺ｮ蜿門ｾ・
	@return	繝代せ繝ｯ繝ｼ繝牙・蜉幃・岼譁・ｭ怜・
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
	@brief	繝代せ繝ｯ繝ｼ繝臥｢ｺ隱榊・蜉幃・岼縺ｮ蜿門ｾ・
	@return	繝代せ繝ｯ繝ｼ繝臥｢ｺ隱榊・蜉幃・岼譁・ｭ怜・
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
	@brief	謨吝ｸｫ繝｡繝九Η繝ｼ縺ｮ蜿門ｾ・
	@return	謨吝ｸｫ繝｡繝九Η繝ｼ譁・ｭ怜・
	*/
	//--------------------------------------------------------------------------------------
	function get_teacher_menu(){
		if(!empty($this->user['user_is_teacher'])){
			return <<<END_BLOCK
<hr>
<div class="mt-3">
    <a href="create_teacher.php" class="btn btn-info">謨吝ｸｫ繧｢繧ｫ繧ｦ繝ｳ繝井ｽ懈・繝壹・繧ｸ縺ｸ</a>
</div>
END_BLOCK;
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
    // 逕ｻ蜒上い繝・・繝ｭ繝ｼ繝牙・逅・
    $icon_filename = $user['user_icon'] ?? '';
    if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['user_icon']['tmp_name'];
        $orig_name = $_FILES['user_icon']['name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        
        // 繝輔ぃ繧､繝ｫ繧ｵ繧､繧ｺ繝√ぉ繝・け・・MB蛻ｶ髯撰ｼ・
        if ($_FILES['user_icon']['size'] > 2 * 1024 * 1024) {
            $error = '逕ｻ蜒上ヵ繧｡繧､繝ｫ縺ｮ繧ｵ繧､繧ｺ縺ｯ2MB莉･荳九↓縺励※縺上□縺輔＞縲・;
        } else if (empty($ext)) {
            $error = '逕ｻ蜒上ヵ繧｡繧､繝ｫ縺ｮ諡｡蠑ｵ蟄舌′蜿門ｾ励〒縺阪∪縺帙ｓ縲Ｋpg, jpeg, png, gif 蠖｢蠑上〒繧｢繝・・繝ｭ繝ｼ繝峨＠縺ｦ縺上□縺輔＞縲・;
        } else if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            // 菫晏ｭ伜・繝・ぅ繝ｬ繧ｯ繝医Μ縺ｮ繝代せ險ｭ螳・
            $icon_dir_relative = 'img/user_icons/';
            $icon_dir_absolute = __DIR__ . '/../' . $icon_dir_relative;
            
            // 繝・ぅ繝ｬ繧ｯ繝医Μ蟄伜惠遒ｺ隱阪→菴懈・
            if (!is_dir($icon_dir_absolute)) {
                if (!mkdir($icon_dir_absolute, 0755, true)) {
                    $error = '繧｢繝・・繝ｭ繝ｼ繝臥畑繝・ぅ繝ｬ繧ｯ繝医Μ繧剃ｽ懈・縺ｧ縺阪∪縺帙ｓ縺ｧ縺励◆縲らｮ｡逅・・↓縺雁撫縺・粋繧上○縺上□縺輔＞縲・;
                }
            }
            
            if (empty($error)) {
                // user_id繧剃ｽｿ縺｣縺ｦ繝輔ぃ繧､繝ｫ蜷阪ｒ逕滓・
                $filename = ($user['user_id'] ?? '') . '.' . $ext;
                $icon_filename = $icon_dir_relative . $filename;
                $save_path = $icon_dir_absolute . $filename;
                
                // 譌｢蟄倡判蜒上・蜑企勁蜃ｦ逅・
                foreach (['jpg', 'jpeg', 'png', 'gif'] as $old_ext) {
                    $old_file = $icon_dir_absolute . ($user['user_id'] ?? '') . '.' . $old_ext;
                    if (file_exists($old_file)) {
                        @unlink($old_file);
                    }
                }
                
                // 繝輔ぃ繧､繝ｫ繧｢繝・・繝ｭ繝ｼ繝・
                if (move_uploaded_file($tmp_name, $save_path)) {
                    // 謌仙粥縺励◆繧峨ヱ繝ｼ繝溘ャ繧ｷ繝ｧ繝ｳ險ｭ螳・
                    chmod($save_path, 0644);
                    
                    // 繧｢繝・・繝ｭ繝ｼ繝臥｢ｺ隱・
                    if (!file_exists($save_path)) {
                        $error = "繝輔ぃ繧､繝ｫ縺ｮ菫晏ｭ倥↓螟ｱ謨励＠縺ｾ縺励◆縲・;
                    }
                } else {
                    $error = '逕ｻ蜒上・菫晏ｭ倥↓螟ｱ謨励＠縺ｾ縺励◆縲らｮ｡逅・・↓縺雁撫縺・粋繧上○縺上□縺輔＞縲・;
                }
            }
        } else {
            $error = '逕ｻ蜒上・ jpg, jpeg, png, gif 縺ｮ縺ｿ蟇ｾ蠢懊〒縺吶・;
        }
    } else if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] !== UPLOAD_ERR_NO_FILE) {
        // 繧｢繝・・繝ｭ繝ｼ繝峨お繝ｩ繝ｼ縺ｮ隧ｳ邏ｰ
        switch ($_FILES['user_icon']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error = '繝輔ぃ繧､繝ｫ繧ｵ繧､繧ｺ縺悟､ｧ縺阪☆縺弱∪縺吶・;
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = '繝輔ぃ繧､繝ｫ縺ｮ繧｢繝・・繝ｭ繝ｼ繝峨′荳ｭ譁ｭ縺輔ｌ縺ｾ縺励◆縲・;
                break;
            default:
                $error = '繝輔ぃ繧､繝ｫ縺ｮ繧｢繝・・繝ｭ繝ｼ繝峨↓螟ｱ謨励＠縺ｾ縺励◆縲・;
        }
    }

    if (empty($name) || empty($mail)) {
        $error = '蜷榊燕縺ｨ繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ縺ｯ蠢・医〒縺吶・;
    } else if (empty($error)) {
        try {
            $db = new cdb();
            $sql = 'UPDATE users SET user_name = ?, user_mailaddress = ?, user_login = ?';
            $params = [$name, $mail, $mail];

            if (!empty($password)) {
                if ($password !== $password_confirm) {
                    $error = '繝代せ繝ｯ繝ｼ繝峨′荳閾ｴ縺励∪縺帙ｓ縲・;
                } else {
                    $sql .= ', user_password = ?';
                    $params[] = sha1($password);
                }
            }
            // 繧｢繧､繧ｳ繝ｳ譖ｴ譁ｰ・医ヵ繧｡繧､繝ｫ縺後い繝・・繝ｭ繝ｼ繝峨＆繧後◆蝣ｴ蜷医・縺ｿ・・
            if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                $sql .= ', user_icon = ?';
                $params[] = $icon_filename;
            }

            if (empty($error)) {
                $sql .= ' WHERE user_id = ?';
                $params[] = $user['user_id'] ?? 0;

                $stmt = $db->prepare($sql);
                $stmt->execute($params);

                // 繧ｻ繝・す繝ｧ繝ｳ諠・ｱ繧よ峩譁ｰ
                $_SESSION['user']['user_name'] = $name;
                $_SESSION['user']['user_mailaddress'] = $mail;
                if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                    $_SESSION['user']['user_icon'] = $icon_filename;
                }

                $success = '繧｢繧ｫ繧ｦ繝ｳ繝域ュ蝣ｱ繧呈峩譁ｰ縺励∪縺励◆縲・;
                $user = get_login_user();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                 $error = '縺薙・繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ縺ｯ譌｢縺ｫ菴ｿ逕ｨ縺輔ｌ縺ｦ縺・∪縺吶・;
            } else {
                 $error = '譖ｴ譁ｰ縺ｫ螟ｱ謨励＠縺ｾ縺励◆縲・;
            }
        }
    }
}

// HTML蜃ｺ蜉・
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<!-- 繧ｳ繝ｳ繝・Φ繝・-->
<head>
    <title>繝槭う繝壹・繧ｸ</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>

<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">繝槭う繝壹・繧ｸ</h3>
                <p class="card-text">繧｢繧ｫ繧ｦ繝ｳ繝域ュ蝣ｱ繧堤ｷｨ髮・＠縺ｾ縺吶・/p>

                <?= $this->get_error_display(); ?>
                <?= $this->get_success_display(); ?>

                <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="mt-4" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <label class="form-label">繝励Ο繝輔ぅ繝ｼ繝ｫ逕ｻ蜒・/label><br>
                        <?php
                        $user_icon = $user['user_icon'] ?? '';
                        $icon_path = !empty($user_icon) && file_exists(__DIR__ . '/../' . $user_icon)
                            ? $user_icon
                            : 'main/img/headerImg/account.png';
                        ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($icon_path); ?>" alt="繝励Ο繝輔ぅ繝ｼ繝ｫ逕ｻ蜒・ 
                                 style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #667eea; margin-bottom:10px;" 
                                 id="profilePreview">
                        </div>
                        <input type="file" name="user_icon" accept="image/*" class="form-control mt-2" 
                               style="max-width:300px; margin:auto;" id="iconInput">
                        <small class="form-text text-muted">jpg, jpeg, png, gif蠖｢蠑擾ｼ・MB莉･荳具ｼ・/small>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('iconInput');
                        const preview = document.getElementById('profilePreview');
                        
                        input.addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            if (file) {
                                // 繝輔ぃ繧､繝ｫ繧ｵ繧､繧ｺ繝√ぉ繝・け・・MB蛻ｶ髯撰ｼ・
                                if (file.size > 2 * 1024 * 1024) {
                                    alert('繝輔ぃ繧､繝ｫ繧ｵ繧､繧ｺ縺ｯ2MB莉･荳九↓縺励※縺上□縺輔＞縲・);
                                    this.value = '';
                                    return;
                                }
                                
                                // 繝輔ぃ繧､繝ｫ蠖｢蠑上メ繧ｧ繝・け
                                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                                if (!allowedTypes.includes(file.type)) {
                                    alert('jpg, jpeg, png, gif蠖｢蠑上・繝輔ぃ繧､繝ｫ繧偵い繝・・繝ｭ繝ｼ繝峨＠縺ｦ縺上□縺輔＞縲・);
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
                        <label for="name" class="form-label">蜷榊燕<span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="name" name="name" value="<?php echo htmlspecialchars($user['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">繝｡繝ｼ繝ｫ繧｢繝峨Ξ繧ｹ<span class="text-danger">*</span></label>
                        <input type="email" class="form-control shadow-sm" id="mail" name="mail" value="<?php echo htmlspecialchars($user['user_mailaddress'] ?? ''); ?>" required>
                        <label for="password" class="form-label">譁ｰ縺励＞繝代せ繝ｯ繝ｼ繝・(螟画峩縺吶ｋ蝣ｴ蜷医・縺ｿ)</label>
                        <?= $this->get_password(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">譁ｰ縺励＞繝代せ繝ｯ繝ｼ繝・(遒ｺ隱・</label>
                        <?= $this->get_password_confirm(); ?>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary px-5 mt-3" onClick="set_func_form('update','')">譖ｴ譁ｰ</button>
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
