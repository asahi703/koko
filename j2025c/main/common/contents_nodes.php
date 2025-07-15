<?php
/*!
@file contents_node.php
@brief 共有するノード
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

////////////////////////////////////


//--------------------------------------------------------------------------------------
///	ヘッダノード
//--------------------------------------------------------------------------------------
class cheader extends cnode {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		require_once(__DIR__ . '/session.php');
		$is_logged_in = is_logged_in();
		$user = get_login_user();
		$username = $is_logged_in && isset($user['name']) ? htmlspecialchars($user['name']) : '';
		$uuid = $is_logged_in && isset($user['uuid']) ? htmlspecialchars($user['uuid']) : '';
		$user_id = $uuid; // user_idはuuidと同じ値
		$email = $is_logged_in && isset($user['mail']) ? htmlspecialchars($user['mail']) : '';

		// ログアウト処理
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
			logout_user();
			header('Location: index.php');
			exit;
		}

		$echo_str = <<< END_BLOCK
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/Global.css">
</head>

<!--PC時ヘッダー-->
<header class="d-none d-md-flex w-100 navbar navbar-expand-md align-items-center py-md-2 fixed-top shadow-sm">
    <nav class="container-fluid d-flex flex-row justify-content-between align-items-center">
        <!-- ブランドロゴとタイトル -->
        <a class="navbar-brand d-flex align-items-center me-auto ms-3" href="#">
            <img src="img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid"
                 alt="">
            <img src="img/headerImg/account.png" style="width: 50px"
                 class="hd-img d-inline-block align-top img-fluid ms-4" alt="">
        </a>
        <!-- ユーザー情報表示 -->
END_BLOCK;

		if ($is_logged_in && $user) {
			$echo_str .= <<< END_BLOCK
            <div class="ms-4 d-flex align-items-center">
                <span class="me-2 fw-bold">{$username}</span>
                <span class="text-secondary small">{$email}</span>
                <form method="post" style="display: inline;">
                    <button type="submit" name="logout" class="btn btn-outline-secondary btn-sm ms-3">ログアウト</button>
                </form>
            </div>
END_BLOCK;
		} else {
			$echo_str .= <<< END_BLOCK
            <div class="ms-4">
                <span class="text-secondary small">未ログイン</span>
            </div>
END_BLOCK;
		}

		$echo_str .= <<< END_BLOCK
    </nav>
</header>
END_BLOCK;

		echo $echo_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	サイドバー付きヘッダノード
//--------------------------------------------------------------------------------------
class cside_header extends cnode {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		$echo_str = <<< END_BLOCK


END_BLOCK;
		echo $echo_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}


//--------------------------------------------------------------------------------------
///	フッターノード
//--------------------------------------------------------------------------------------
class cfooter extends cnode {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		$echo_str = <<< END_BLOCK
<footer class="footer">
    <div class="container">
        <footer class="py-3 my-4">
            <ul class="nav justify-content-center border-bottom pb-3 mb-3">
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Home</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Features</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">Pricing</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">FAQs</a></li>
                <li class="nav-item"><a href="#" class="nav-link px-2 text-body-secondary">About</a></li>
				<li class="nav-item"><a href="sell_request.php" class="nav-link px-2 text-body-secondary">出品者になる</a></li>
            </ul>
            <p class="text-center text-body-secondary">&copy; <?php echo date('Y'); ?> Team I. All rights reserved.</p>
        </footer>
    </div>
</footer>
END_BLOCK;
		echo $echo_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}


//--------------------------------------------------------------------------------------
///	サイドバー付きフッターノード
//--------------------------------------------------------------------------------------
class cside_footer extends cnode {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		$echo_str = <<< END_BLOCK


END_BLOCK;
		echo $echo_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}


//--------------------------------------------------------------------------------------
///	住所ノード
//--------------------------------------------------------------------------------------
class caddress extends cnode {
	public $param_arr;
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct($param_arr) {
		$this->param_arr = $param_arr;
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	パラメータのチェック
	@return	なし（エラーの場合はエラーフラグを立てる）
	*/
	//--------------------------------------------------------------------------------------
	public function paramchk(){
		global $err_array;
		global $err_flag;
		if($this->param_arr['cntl_header_name'] == 'par' && $_POST['member_minor'] == 0 ){
			//保護者は未成年の時だけ必須
			return;
		}
		/// 名前の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,"{$this->param_arr['cntl_header_name']}_name","{$this->param_arr['head']}名",'isset_nl')){
			$err_flag = 1;
		}
		/// 都道府県チェック
		if(cutil_ex::chkset_err_field($err_array,"{$this->param_arr['cntl_header_name']}_prefecture_id","{$this->param_arr['head']}都道府県",'isset_num_range',1,47)){
			$err_flag = 1;
		}
		/// 住所の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,"{$this->param_arr['cntl_header_name']}_address","{$this->param_arr['head']}市区郡町村以下",'isset_nl')){
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("{$this->param_arr['cntl_header_name']}_prefecture_id",0);
		cutil::post_default("{$this->param_arr['cntl_header_name']}_name",'');
		cutil::post_default("{$this->param_arr['cntl_header_name']}_address",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	名前コントロールの取得
	@return	名前コントロール
	*/
	//--------------------------------------------------------------------------------------
	function get_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox("{$this->param_arr['cntl_header_name']}_name",
				$_POST["{$this->param_arr['cntl_header_name']}_name"],'size="70"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array["{$this->param_arr['cntl_header_name']}_name"])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array["{$this->param_arr['cntl_header_name']}_name"]) 
			. '</span>';
		}
		return $ret_str;
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	都道府県プルダウンの取得
	@return	都道府県プルダウン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_prefecture_select(){
		global $err_array;
		//都道府県の一覧を取得
		$prefecture_obj = new cprefecture();
		$allcount = $prefecture_obj->get_all_count(false);
		$prefecture_rows = $prefecture_obj->get_all(false,0,$allcount);
		$tgt = new cselect("{$this->param_arr['cntl_header_name']}_prefecture_id");
		$tgt->add(0,'選択してください',$_POST["{$this->param_arr['cntl_header_name']}_prefecture_id"] == 0);
		foreach($prefecture_rows as $key => $val){
			$tgt->add($val['prefecture_id'],$val['prefecture_name'],
			$val['prefecture_id'] == $_POST["{$this->param_arr['cntl_header_name']}_prefecture_id"]);
		}
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array["{$this->param_arr['cntl_header_name']}_prefecture_id"])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array["{$this->param_arr['cntl_header_name']}_prefecture_id"]) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	住所の取得
	@return	住所文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_address(){
		global $err_array;
		$tgt = new ctextbox("{$this->param_arr['cntl_header_name']}_address",
				$_POST["{$this->param_arr['cntl_header_name']}_address"],'size="80"');
		$ret_str = $tgt->get($_POST['func'] == 'conf');
		if(isset($err_array["{$this->param_arr['cntl_header_name']}_address"])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array["{$this->param_arr['cntl_header_name']}_address"]) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		$name_str = "{$this->param_arr['head']}名";
		$prefec_str = "{$this->param_arr['head']}都道府県";
		$address_str = "{$this->param_arr['head']}市区郡町村以下";
//PHPブロック終了
?>

<?php 
//PHPブロック再開
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	サイドバーノード
//--------------------------------------------------------------------------------------
class csidebar extends cnode {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		//PHPブロック終了
		?>
<head>
    <link rel="stylesheet" href="css/sidebar.css"/>
</head>
<nav class="top-sidebar d-none d-md-flex flex-column align-items-center p-0" style="width: 100px;">
    <ul class="nav flex-column sidebar-content w-100">
 
        <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="notification.php">
                <img src="img/sidebarImg/notifications.png" class="icon-img img-fluid" width="50" alt="...">
                <span class="nav-label">通知</span>
            </a>
        </li>
        <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="chat.php">
                <img src="img/sidebarImg/chat.png" class="icon-img img-fluid" width="50" alt="...">
                <span class="nav-label">チャット</span>
            </a>
        </li>
        <li class="nav-item mb-4 text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="community.php">
                <img src="img/sidebarImg/community.png" class="icon-img img-fluid" width="50" alt="...">
                <span class="nav-label">コミュニティ</span>
            </a>
        </li>
        <li class="nav-item text-center">
            <a class="nav-link p-0 d-flex flex-column align-items-center text-white hover-bg-dark" href="faq.php">
                <img src="img/sidebarImg/FAQ.png" class="icon-img img-fluid" width="50" alt="...">
                <span class="nav-label">よくある質問</span>
            </a>
        </li>
    </ul>
</nav>
<?php 
		//PHPブロック再開
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//--------------------------------------------------------------------------------------
///	クラスサイドバーノード
//--------------------------------------------------------------------------------------
class cclass_sidebar extends cnode {
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
		//PHPブロック終了
		?>
<head>
    <link rel="stylesheet" href="css/class_sidebar.css">
</head>
<!-- クラスサイドバー -->
<nav id="class-sidebar" class="d-none d-md-block flex-column px-0" style="border:none;">
    <div class="d-flex flex-row justify-content-start align-items-center ms-2 py-md-2 text-nowrap" style="border:none;">
        <!-- ← だけ残す（枠線なし） -->
        <a class="nav-link text-dark d-flex align-items-center" href="javascript:history.back();">
            <img src="img/sidebarImg/arrow_back_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt=""
                 class="me-2" style="width: 24px; height: 24px;">
        </a>
    </div>
</nav>

<!-- スマホ用(ヘッダーの直下に置く) -->
<div class="d-flex d-md-none bg-white py-2 px-3 justify-content-between align-items-center sticky-top"
     style="top: 80px; z-index: 1020; border:none;">
    <!-- 左：戻る（←だけ残す、枠線なし） -->
    <a href="javascript:history.back();" class="text-dark text-decoration-none d-flex align-items-center">
        <img src="img/sidebarImg/arrow_back_25dp_999999_FILL0_wght400_GRAD0_opsz24.svg" alt="戻る"
             style="width: 24px; height: 24px;">
    </a>
    <!-- 中央・右は非表示 -->
</div>
<?php 
		//PHPブロック再開
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}
