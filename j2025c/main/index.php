<?php
/*!
@file index.php
@brief ログイン画面
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
		cutil::post_default("user_mailaddress",'');
		cutil::post_default("user_password",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処琁E継承して使用)
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
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
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'login':
					//パラメータのチェチE��
					$this->paramchk();
					if($err_flag != 0){
						$this->error = '入力エラーがあります、E;
					}
					else{
						$this->login_user();
					}
				break;
				default:
					//通常はありえなぁE
					echo '原因不�Eのエラーです、E;
					exit;
				break;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	パラメータのチェチE��
	@return	エラーの場合�Efalseを返す
	*/
	//--------------------------------------------------------------------------------------
	function paramchk(){
		global $err_array;
		global $err_flag;
		
		/// メールアドレスの存在と空白チェチE��
		if(cutil_ex::chkset_err_field($err_array,'user_mailaddress','メールアドレス','isset_nl')){
			$err_flag = 1;
		}
		
		/// パスワード�E存在と空白チェチE��
		if(cutil_ex::chkset_err_field($err_array,'user_password','パスワーチE,'isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ログイン処琁E
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	function login_user(){
		try {
			$users_obj = new cusers();
			$user = $users_obj->authenticate_user(false, $_POST['user_mailaddress'], sha1($_POST['user_password']));
			
			if($user){
				// ログイン成功
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
				$this->error = 'メールアドレスまた�Eパスワードが違います、E;
			}
		} catch (Exception $e) {
			$this->error = 'ログイン処琁E��エラーが発生しました、E;
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

<div class="alert alert-danger">入力エラーがあります。各頁E��のエラーを確認してください、E/div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger">処琁E��失敗しました。サポ�Eトを確認下さぁE��E/div>
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
			return '<div class="alert alert-danger">' . display($this->error) . '</div>';
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
			return '<div class="alert alert-success">' . display($this->success) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メールアドレス入力頁E��の取征E
	@return	メールアドレス入力頁E��斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_user_mailaddress(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('user_mailaddress',$_POST['user_mailaddress'],'type="email" class="form-control" placeholder="メールアドレス" required');
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
	@brief	パスワード�E力頁E��の取征E
	@return	パスワード�E力頁E��斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_user_password(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('user_password','','type="password" class="form-control" placeholder="パスワーチE required');
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
	@brief  表示(継承して使用)
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロチE��終亁E
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
                // 最終ログイン時間を更新
                try {
                    $update_stmt = $db->prepare('UPDATE users SET last_login_time = NOW() WHERE user_id = ?');
                    $update_stmt->execute([$user['user_id']]);
                } catch (Exception $e) {
                    // ログイン時間の更新に失敗してもログイン処琁E�E継綁E
                    error_log('Failed to update last login time: ' . $e->getMessage());
                }
                
                // ログイン成功
                login_user([
                    'uuid' => $user['user_id'],
                    'user_id' => $user['user_id'],
                    'user_name' => $user['user_name'],
                    'user_mailaddress' => $user['user_mailaddress'],
                    'user_icon' => $user['user_icon'],
                    'user_is_teacher' => $user['user_is_teacher'],
                    // 後方互換性のため
                    'name' => $user['user_name'],
                    'mail' => $user['user_mailaddress']
                ]);
                header('Location: community.php');
                exit;
            } else {
                $error = 'メールアドレスまた�Eパスワードが違います、E;
            }
        } catch (Exception $e) {
            $error = 'ログイン処琁E��エラーが発生しました、E;
        }
    } else {
        $error = 'メールアドレスとパスワードを入力してください、E;
    }
}
?>
<!-- コンチE��チE��-->
<head>
    <title>ログイン</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>

<div class="main-content-wrapper">
    <div class="container d-flex flex-column align-items-center justify-content-center vw-100">
        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-md-3">
            <div class="mb-3">
                <h2>ログイン</h2>
            </div>
            
            <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="border rounded shadow p-4 w-100 login-form">
                <div class="form-group my-2 px-2 w-100">
                    <label>メールアドレス <span class="text-danger fw-bold">*</span></label>
                    <?= $this->get_user_mailaddress(); ?>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>パスワーチE<span class="text-danger fw-bold">*</span></label>
                    <?= $this->get_user_password(); ?>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <button type="button" class="btn btn-primary w-100" onClick="set_func_form('login','')">ログイン</button>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <a href="signin.php">新規登録はこちめE/a>
                </div>
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
            </form>
        </div>
    </div>
</div>
</div>
<!-- /コンチE��チE��-->
<script>
// prefecture_detail.phpスタイルのフォーム操作関数
function set_func_form(func, param) {
    document.form1.func.value = func;
    document.form1.param.value = param;
    document.form1.submit();
}
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
//シンプルヘッダ追加
$page_obj->add_child(cutil::create('csimple_header'));
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
