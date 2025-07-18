<?php
/*!
@file community_join.php
@brief 繧ｳ繝溘Η繝九ユ繧｣蜿ょ刈
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
	public $db;
	public $invite_code_box;
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
		$this->db = null;
		$this->invite_code_box = null;
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
		if(!isset($_POST['invite_code'])){
			$_POST['invite_code'] = "";
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
		
		// DB謗･邯・
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//繝輔か繝ｼ繝繝懊ャ繧ｯ繧ｹ菴懈・
		$this->invite_code_box = new ctextbox('invite_code', 'form-control', 255);
		$this->invite_code_box->set_required(true);
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
			$this->invite_code_box->validate();
			
			// 騾∽ｿ｡繝懊ち繝ｳ縺梧款縺輔ｌ縺溷ｴ蜷・
			if(isset($_POST['submit'])){
				if(empty($err_array)){
					$this->join_community();
				}
			}
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	繧ｳ繝溘Η繝九ユ繧｣蜿ょ刈蜃ｦ逅・
	@return	縺ｪ縺・
	*/
	//--------------------------------------------------------------------------------------
	function join_community(){
		global $err_flag;
		
		try{
			$invite_code = trim($_POST['invite_code']);
			
			if(!$this->user){
				$this->error = '繝ｭ繧ｰ繧､繝ｳ縺励※縺上□縺輔＞縲・;
				return;
			}
			
			if($invite_code === ''){
				$this->error = '諡帛ｾ・さ繝ｼ繝峨ｒ蜈･蜉帙＠縺ｦ縺上□縺輔＞縲・;
				return;
			}
			
			// 繧ｳ繝ｼ繝峨′譛牙柑縺狗｢ｺ隱・
			$stmt = $this->db->prepare('SELECT community_id FROM community_invite_codes WHERE invite_code = ?');
			$stmt->execute([$invite_code]);
			$row = $stmt->fetch();
			
			if($row){
				$community_id = $row['community_id'];
				// 譌｢縺ｫ蜿ょ刈縺励※縺・↑縺・°遒ｺ隱・
				$stmt2 = $this->db->prepare('SELECT * FROM community_users WHERE user_id = ? AND community_id = ?');
				$stmt2->execute([$this->user['uuid'], $community_id]);
				
				if(!$stmt2->fetch()){
					// 蜿ょ刈蜃ｦ逅・
					$stmt3 = $this->db->prepare('INSERT INTO community_users (user_id, community_id) VALUES (?, ?)');
					$stmt3->execute([$this->user['uuid'], $community_id]);
					$this->success = '繧ｳ繝溘Η繝九ユ繧｣縺ｫ蜿ょ刈縺励∪縺励◆縲・;
					
					// 繝輔か繝ｼ繝繝ｪ繧ｻ繝・ヨ
					$_POST['invite_code'] = '';
					$this->invite_code_box->set_value('');
				} else {
					$this->error = '縺吶〒縺ｫ縺薙・繧ｳ繝溘Η繝九ユ繧｣縺ｫ蜿ょ刈縺励※縺・∪縺吶・;
				}
			} else {
				$this->error = '諡帛ｾ・さ繝ｼ繝峨′辟｡蜉ｹ縺ｧ縺吶・;
			}
			
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_code'])) {
    $invite_code = trim($_POST['invite_code']);
    if (!$user) {
        $error = '繝ｭ繧ｰ繧､繝ｳ縺励※縺上□縺輔＞縲・;
    } elseif ($invite_code === '') {
        $error = '諡帛ｾ・さ繝ｼ繝峨ｒ蜈･蜉帙＠縺ｦ縺上□縺輔＞縲・;
    } else {
        $db = new cdb();
        // 繧ｳ繝ｼ繝峨′譛牙柑縺狗｢ｺ隱・
        $stmt = $db->prepare('SELECT community_id FROM community_invite_codes WHERE invite_code = ?');
        $stmt->execute([$invite_code]);
        $row = $stmt->fetch();
        if ($row) {
            $community_id = $row['community_id'];
            
            // 繧ｳ繝溘Η繝九ユ繧｣蜷阪ｒ蜿門ｾ・
            $community_stmt = $db->prepare('SELECT community_name FROM communities WHERE community_id = ?');
            $community_stmt->execute([$community_id]);
            $community_data = $community_stmt->fetch();
            $community_name = $community_data['community_name'] ?? '繧ｳ繝溘Η繝九ユ繧｣';
            
            // 譌｢縺ｫ蜿ょ刈縺励※縺・↑縺・°遒ｺ隱・
            $stmt2 = $db->prepare('SELECT * FROM community_users WHERE user_id = ? AND community_id = ?');
            $stmt2->execute([$user['uuid'], $community_id]);
            if (!$stmt2->fetch()) {
                // 蜿ょ刈蜃ｦ逅・
                $stmt3 = $db->prepare('INSERT INTO community_users (user_id, community_id) VALUES (?, ?)');
                $stmt3->execute([$user['uuid'], $community_id]);
                
                // 譁ｰ繝｡繝ｳ繝舌・蜿ょ刈騾夂衍繧帝∽ｿ｡
                $new_member_name = $user['user_name'] ?? $user['name'] ?? '繝ｦ繝ｼ繧ｶ繝ｼ';
                notify_community_join($community_id, $user['uuid'], $new_member_name, $community_name);
                
                $success = '繧ｳ繝溘Η繝九ユ繧｣縺ｫ蜿ょ刈縺励∪縺励◆縲・;
            } else {
                $error = '縺吶〒縺ｫ縺薙・繧ｳ繝溘Η繝九ユ繧｣縺ｫ蜿ょ刈縺励※縺・∪縺吶・;
            }
        } else {
            $error = '諡帛ｾ・さ繝ｼ繝峨′辟｡蜉ｹ縺ｧ縺吶・;
        }
    }
}
?>
<!-- 繧ｳ繝ｳ繝・Φ繝・-->
<head>
    <title>繧ｳ繝溘Η繝九ユ繧｣蜿ょ刈</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 150px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">繧ｳ繝溘Η繝九ユ繧｣縺ｫ蜿ょ刈</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="inviteCode" class="form-label">諡帛ｾ・さ繝ｼ繝・span class="text-danger">*</span></label>
                        <?= $this->invite_code_box->get_tag(); ?>
                        <?= $this->invite_code_box->get_error_message_tag(); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary px-5">蜿ょ刈</button>
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
