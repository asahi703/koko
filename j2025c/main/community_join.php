<?php
/*!
@file community_join.php
@brief コミュニティ参加
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
	public $db;
	public $invite_code_box;
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
		$this->invite_code_box = null;
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
		if(!isset($_POST['invite_code'])){
			$_POST['invite_code'] = "";
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
		
		// DB接綁E
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作�E
		$this->invite_code_box = new ctextbox('invite_code', 'form-control', 255);
		$this->invite_code_box->set_required(true);
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
			$this->invite_code_box->validate();
			
			// 送信ボタンが押された場吁E
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
	@brief	コミュニティ参加処琁E
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	function join_community(){
		global $err_flag;
		
		try{
			$invite_code = trim($_POST['invite_code']);
			
			if(!$this->user){
				$this->error = 'ログインしてください、E;
				return;
			}
			
			if($invite_code === ''){
				$this->error = '招征E��ードを入力してください、E;
				return;
			}
			
			// コードが有効か確誁E
			$stmt = $this->db->prepare('SELECT community_id FROM community_invite_codes WHERE invite_code = ?');
			$stmt->execute([$invite_code]);
			$row = $stmt->fetch();
			
			if($row){
				$community_id = $row['community_id'];
				// 既に参加してぁE��ぁE��確誁E
				$stmt2 = $this->db->prepare('SELECT * FROM community_users WHERE user_id = ? AND community_id = ?');
				$stmt2->execute([$this->user['uuid'], $community_id]);
				
				if(!$stmt2->fetch()){
					// 参加処琁E
					$stmt3 = $this->db->prepare('INSERT INTO community_users (user_id, community_id) VALUES (?, ?)');
					$stmt3->execute([$this->user['uuid'], $community_id]);
					$this->success = 'コミュニティに参加しました、E;
					
					// フォームリセチE��
					$_POST['invite_code'] = '';
					$this->invite_code_box->set_value('');
				} else {
					$this->error = 'すでにこ�Eコミュニティに参加してぁE��す、E;
				}
			} else {
				$this->error = '招征E��ードが無効です、E;
			}
			
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_code'])) {
    $invite_code = trim($_POST['invite_code']);
    if (!$user) {
        $error = 'ログインしてください、E;
    } elseif ($invite_code === '') {
        $error = '招征E��ードを入力してください、E;
    } else {
        $db = new cdb();
        // コードが有効か確誁E
        $stmt = $db->prepare('SELECT community_id FROM community_invite_codes WHERE invite_code = ?');
        $stmt->execute([$invite_code]);
        $row = $stmt->fetch();
        if ($row) {
            $community_id = $row['community_id'];
            
            // コミュニティ名を取征E
            $community_stmt = $db->prepare('SELECT community_name FROM communities WHERE community_id = ?');
            $community_stmt->execute([$community_id]);
            $community_data = $community_stmt->fetch();
            $community_name = $community_data['community_name'] ?? 'コミュニティ';
            
            // 既に参加してぁE��ぁE��確誁E
            $stmt2 = $db->prepare('SELECT * FROM community_users WHERE user_id = ? AND community_id = ?');
            $stmt2->execute([$user['uuid'], $community_id]);
            if (!$stmt2->fetch()) {
                // 参加処琁E
                $stmt3 = $db->prepare('INSERT INTO community_users (user_id, community_id) VALUES (?, ?)');
                $stmt3->execute([$user['uuid'], $community_id]);
                
                // 新メンバ�E参加通知を送信
                $new_member_name = $user['user_name'] ?? $user['name'] ?? 'ユーザー';
                notify_community_join($community_id, $user['uuid'], $new_member_name, $community_name);
                
                $success = 'コミュニティに参加しました、E;
            } else {
                $error = 'すでにこ�Eコミュニティに参加してぁE��す、E;
            }
        } else {
            $error = '招征E��ードが無効です、E;
        }
    }
}
?>
<!-- コンチE��チE��-->
<head>
    <title>コミュニティ参加</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 150px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">コミュニティに参加</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="inviteCode" class="form-label">招征E��ーチEspan class="text-danger">*</span></label>
                        <?= $this->invite_code_box->get_tag(); ?>
                        <?= $this->invite_code_box->get_error_message_tag(); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="btn btn-primary px-5">参加</button>
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
