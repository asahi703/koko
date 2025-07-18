<?php
/*!
@file mypage.php
@brief マイペ�Eジ
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
		cutil::post_default("name",'');
		cutil::post_default("mail",'');
		cutil::post_default("password",'');
		cutil::post_default("password_confirm",'');
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
				case 'update':
					//パラメータのチェチE��
					$this->paramchk();
					if($err_flag != 0){
						$this->error = '入力エラーがあります、E;
					}
					else{
						$this->update_user();
					}
				break;
				default:
					//通常はありえなぁE
					echo '原因不�Eのエラーです、E;
					exit;
				break;
			}
		}
		else{
			// 初期表示時にユーザー惁E��をPOSTにセチE��
			$_POST['name'] = $this->user['name'];
			$_POST['mail'] = $this->user['mail'];
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
		
		/// 名前の存在と空白チェチE��
		if(cutil_ex::chkset_err_field($err_array,'name','名前','isset_nl')){
			$err_flag = 1;
		}
		
		/// メールアドレスの存在と空白チェチE��
		if(cutil_ex::chkset_err_field($err_array,'mail','メールアドレス','isset_nl')){
			$err_flag = 1;
		}
		
		// パスワード確誁E
		if(!empty($_POST['password'])){
			if($_POST['password'] != $_POST['password_confirm']){
				$err_array['password_confirm'] = 'パスワードが一致しません、E;
				$err_flag = 1;
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザー惁E��更新処琁E
	@return	なぁE
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
			
			// パスワードが入力されてぁE��場合�E更新
			if(!empty($_POST['password'])){
				$dataarr['user_password'] = sha1($_POST['password']);
			}
			
			$where = 'user_id = :user_id';
			$wherearr[':user_id'] = $this->user['user_id'];
			
			$result = $users_obj->update_core(false, 'users', $dataarr, $where, $wherearr, false);
			
			// セチE��ョン惁E��も更新
			$_SESSION['user']['name'] = $_POST['name'];
			$_SESSION['user']['mail'] = $_POST['mail'];
			
			$this->success = 'アカウント情報を更新しました、E;
			// ユーザー惁E��を�E取征E
			$this->user = get_login_user();
		} catch (Exception $e) {
			$this->error = '更新に失敗しました、E;
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
	@brief	名前入力頁E��の取征E
	@return	名前入力頁E��斁E���E
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
	@brief	メールアドレス入力頁E��の取征E
	@return	メールアドレス入力頁E��斁E���E
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
	@brief	パスワード�E力頁E��の取征E
	@return	パスワード�E力頁E��斁E���E
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
	@brief	パスワード確認�E力頁E��の取征E
	@return	パスワード確認�E力頁E��斁E���E
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
	@brief	教師メニューの取征E
	@return	教師メニュー斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_teacher_menu(){
		if(!empty($this->user['user_is_teacher'])){
			return <<<END_BLOCK
<hr>
<div class="mt-3">
    <a href="create_teacher.php" class="btn btn-info">教師アカウント作�Eペ�Eジへ</a>
</div>
END_BLOCK;
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
    // 画像アチE�Eロード�E琁E
    $icon_filename = $user['user_icon'] ?? '';
    if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['user_icon']['tmp_name'];
        $orig_name = $_FILES['user_icon']['name'];
        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        
        // ファイルサイズチェチE���E�EMB制限！E
        if ($_FILES['user_icon']['size'] > 2 * 1024 * 1024) {
            $error = '画像ファイルのサイズは2MB以下にしてください、E;
        } else if (empty($ext)) {
            $error = '画像ファイルの拡張子が取得できません。jpg, jpeg, png, gif 形式でアチE�Eロードしてください、E;
        } else if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
            // 保存�EチE��レクトリのパス設宁E
            $icon_dir_relative = 'img/user_icons/';
            $icon_dir_absolute = __DIR__ . '/../' . $icon_dir_relative;
            
            // チE��レクトリ存在確認と作�E
            if (!is_dir($icon_dir_absolute)) {
                if (!mkdir($icon_dir_absolute, 0755, true)) {
                    $error = 'アチE�Eロード用チE��レクトリを作�Eできませんでした。管琁E��E��お問ぁE��わせください、E;
                }
            }
            
            if (empty($error)) {
                // user_idを使ってファイル名を生�E
                $filename = ($user['user_id'] ?? '') . '.' . $ext;
                $icon_filename = $icon_dir_relative . $filename;
                $save_path = $icon_dir_absolute . $filename;
                
                // 既存画像�E削除処琁E
                foreach (['jpg', 'jpeg', 'png', 'gif'] as $old_ext) {
                    $old_file = $icon_dir_absolute . ($user['user_id'] ?? '') . '.' . $old_ext;
                    if (file_exists($old_file)) {
                        @unlink($old_file);
                    }
                }
                
                // ファイルアチE�EローチE
                if (move_uploaded_file($tmp_name, $save_path)) {
                    // 成功したらパーミッション設宁E
                    chmod($save_path, 0644);
                    
                    // アチE�Eロード確誁E
                    if (!file_exists($save_path)) {
                        $error = "ファイルの保存に失敗しました、E;
                    }
                } else {
                    $error = '画像�E保存に失敗しました。管琁E��E��お問ぁE��わせください、E;
                }
            }
        } else {
            $error = '画像�E jpg, jpeg, png, gif のみ対応です、E;
        }
    } else if (isset($_FILES['user_icon']) && $_FILES['user_icon']['error'] !== UPLOAD_ERR_NO_FILE) {
        // アチE�Eロードエラーの詳細
        switch ($_FILES['user_icon']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error = 'ファイルサイズが大きすぎます、E;
                break;
            case UPLOAD_ERR_PARTIAL:
                $error = 'ファイルのアチE�Eロードが中断されました、E;
                break;
            default:
                $error = 'ファイルのアチE�Eロードに失敗しました、E;
        }
    }

    if (empty($name) || empty($mail)) {
        $error = '名前とメールアドレスは忁E��です、E;
    } else if (empty($error)) {
        try {
            $db = new cdb();
            $sql = 'UPDATE users SET user_name = ?, user_mailaddress = ?, user_login = ?';
            $params = [$name, $mail, $mail];

            if (!empty($password)) {
                if ($password !== $password_confirm) {
                    $error = 'パスワードが一致しません、E;
                } else {
                    $sql .= ', user_password = ?';
                    $params[] = sha1($password);
                }
            }
            // アイコン更新�E�ファイルがアチE�Eロードされた場合�Eみ�E�E
            if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                $sql .= ', user_icon = ?';
                $params[] = $icon_filename;
            }

            if (empty($error)) {
                $sql .= ' WHERE user_id = ?';
                $params[] = $user['user_id'] ?? 0;

                $stmt = $db->prepare($sql);
                $stmt->execute($params);

                // セチE��ョン惁E��も更新
                $_SESSION['user']['user_name'] = $name;
                $_SESSION['user']['user_mailaddress'] = $mail;
                if (!empty($icon_filename) && $icon_filename !== ($user['user_icon'] ?? '')) {
                    $_SESSION['user']['user_icon'] = $icon_filename;
                }

                $success = 'アカウント情報を更新しました、E;
                $user = get_login_user();
            }
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                 $error = 'こ�Eメールアドレスは既に使用されてぁE��す、E;
            } else {
                 $error = '更新に失敗しました、E;
            }
        }
    }
}

// HTML出劁E
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<!-- コンチE��チE��-->
<head>
    <title>マイペ�Eジ</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>

<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">マイペ�Eジ</h3>
                <p class="card-text">アカウント情報を編雁E��ます、E/p>

                <?= $this->get_error_display(); ?>
                <?= $this->get_success_display(); ?>

                <form name="form1" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="mt-4" enctype="multipart/form-data">
                    <div class="mb-3 text-center">
                        <label class="form-label">プロフィール画僁E/label><br>
                        <?php
                        $user_icon = $user['user_icon'] ?? '';
                        $icon_path = !empty($user_icon) && file_exists(__DIR__ . '/../' . $user_icon)
                            ? $user_icon
                            : 'main/img/headerImg/account.png';
                        ?>
                        <div class="mb-3">
                            <img src="<?php echo htmlspecialchars($icon_path); ?>" alt="プロフィール画僁E 
                                 style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid #667eea; margin-bottom:10px;" 
                                 id="profilePreview">
                        </div>
                        <input type="file" name="user_icon" accept="image/*" class="form-control mt-2" 
                               style="max-width:300px; margin:auto;" id="iconInput">
                        <small class="form-text text-muted">jpg, jpeg, png, gif形式！EMB以下！E/small>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const input = document.getElementById('iconInput');
                        const preview = document.getElementById('profilePreview');
                        
                        input.addEventListener('change', function(e) {
                            const file = e.target.files[0];
                            if (file) {
                                // ファイルサイズチェチE���E�EMB制限！E
                                if (file.size > 2 * 1024 * 1024) {
                                    alert('ファイルサイズは2MB以下にしてください、E);
                                    this.value = '';
                                    return;
                                }
                                
                                // ファイル形式チェチE��
                                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                                if (!allowedTypes.includes(file.type)) {
                                    alert('jpg, jpeg, png, gif形式�EファイルをアチE�Eロードしてください、E);
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
                        <label for="name" class="form-label">名前<span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="name" name="name" value="<?php echo htmlspecialchars($user['user_name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">メールアドレス<span class="text-danger">*</span></label>
                        <input type="email" class="form-control shadow-sm" id="mail" name="mail" value="<?php echo htmlspecialchars($user['user_mailaddress'] ?? ''); ?>" required>
                        <label for="password" class="form-label">新しいパスワーチE(変更する場合�Eみ)</label>
                        <?= $this->get_password(); ?>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">新しいパスワーチE(確誁E</label>
                        <?= $this->get_password_confirm(); ?>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary px-5 mt-3" onClick="set_func_form('update','')">更新</button>
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
