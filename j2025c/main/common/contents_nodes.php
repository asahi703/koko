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
		$username = $is_logged_in ? htmlspecialchars($user['username']) : '';
		$uuid = $is_logged_in ? htmlspecialchars($user['uuid']) : '';
		$user_id = $is_logged_in ? htmlspecialchars($user['user_id']) : '';
		$email = $is_logged_in ? htmlspecialchars($user['email']) : '';
		$echo_str =
			<<< END_BLOCK
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function(d) {
            var config = {
                    kitId: 'uqx7pie',
                    scriptTimeout: 3000,
                    async: true
                },
                h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
        })(document);
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/~j2025i/main/css/global.css">
</head>
<header class="header">
END_BLOCK;
		/*
		if($is_logged_in) {
			// ログインユーザー情報を取得
			$user_id = htmlspecialchars($user['user_id']);
			$username = htmlspecialchars($user['username']);
			$email = htmlspecialchars($user['email']);
			$uuid = htmlspecialchars($user['uuid']);
		} else {
			// 非ログイン時のデフォルト値
			$user_id = '';
			$username = '';
			$email = '';
			$uuid = '';
		}
		// デバッグ用ログインステータス表示（修正版）
		echo "ログイン状態:" . ($is_logged_in ? 'ログイン中' : 'ログインしていません');
		echo "/ユーザーID:" . $user_id;
		echo "/ユーザー名:" . $username;
		echo "/メールアドレス:" . $email;
		echo "/UUID:" . $uuid;
		*/
		$echo_str .= <<< END_BLOCK
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
    <link rel="stylesheet" href="../main/css/header.css">
    <link rel="stylesheet" href="../main/css/Global.css">
</head>

<!--PC時ヘッダー-->
<header class="d-none d-md-flex w-100 navbar navbar-expand-md align-items-center py-md-2 fixed-top shadow-sm">
    <nav class="container-fluid d-flex flex-row justify-content-between align-items-center">
        <!-- ブランドロゴとタイトル -->
        <a class="navbar-brand d-flex align-items-center me-auto ms-3" href="#">
            <img src="../main/img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid" alt="">
        </a>
        <a href="../main/mypage.php">
            <?php
            // ユーザーアイコンのパスを取得（なければデフォルト）
            $icon_file = !empty($user['user_icon']) && file_exists(__DIR__ . '/../../' . $user['user_icon'])
                ? $user['user_icon']
                : '../main/img/headerImg/account.png';
            ?>
            <img src="<?php echo htmlspecialchars($icon_file); ?>" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #667eea;"
                 class="hd-img d-inline-block align-top img-fluid ms-2" alt="プロフィールアイコン">
        </a>
        <!-- ユーザー情報表示 -->
        <?php if ($user): ?>
            <div class="ms-4 d-flex align-items-center">
                <span class="me-2 fw-bold"><?php echo htmlspecialchars($user['name']); ?></span>
                <span class="text-secondary small"><?php echo htmlspecialchars($user['mail']); ?></span>
                <form method="post" style="display: inline;">
                    <button type="submit" name="logout" class="btn btn-outline-secondary btn-sm ms-3">ログアウト</button>
                </form>
            </div>
        <?php else: ?>
            <div class="ms-4">
                <span class="text-secondary small">未ログイン</span>
            </div>
        <?php endif; ?>
    </nav>
</header>
END_BLOCK;
		// 右側ユーザー名ボタン（ログイン時のみ表示）
		if ($is_logged_in) {
			$echo_str .= <<< END_BLOCK
                <div class="d-flex">
                    <button class="btn btn-outline-success me-2 text-nowrap" type="submit" onclick="location.href='/~j2025i/main/cart.php'">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16">
						  <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
						</svg>
                        カート
                    </button>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-success me-2 text-nowrap fw-bold" type="button" onclick="location.href='/~j2025i/main/mypage.php'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16">
                        	<path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>
						{$username}
                    </button>
                </div>
END_BLOCK;
		}
		// ログインボタン（非ログイン時のみ表示）
		if (!$is_logged_in) {
			$echo_str .= <<< END_BLOCK
                <div class="d-flex">
                    <button class="btn btn-outline-success me-2 text-nowrap" type="submit" onclick="location.href='/~j2025i/main/login.php'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="bi bi-box-arrow-in-left" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 3.5a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-2a.5.5 0 0 1 1 0v2A1.5 1.5 0 0 1 9.5 14h-8A1.5 1.5 0 0 1 0 12.5v-9A1.5 1.5 0 0 1 1.5 2h8A1.5 1.5 0 0 1 11 3.5v2a.5.5 0 0 1-1 0z"/>
                            <path fill-rule="evenodd" d="M4.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H14.5a.5.5 0 0 1 0 1H5.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                        </svg>
                        ログイン/新規登録
                    </button>
                </div>
END_BLOCK;
		}
		$echo_str .= <<< END_BLOCK
            </div>
        </div>
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
