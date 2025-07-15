<?php
/*!
@file home.php
@brief ホーム画面
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $user;
	public $communities;
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
		$this->communities = array();
		$this->error = '';
		$this->success = '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("community_name",'');
		cutil::post_default("community_description",'');
		cutil::post_default("invite_code",'');
		cutil::post_default("search_keyword",'');
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// セッション情報の取得
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  本体実行（表示前処理）
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo 'ページが無効です';
			exit();
		}
		
		// GET パラメータでの成功メッセージ
		if(isset($_GET['created'])){
			$this->success = 'コミュニティを作成しました。';
		}
		if(isset($_GET['joined'])){
			$this->success = 'コミュニティに参加しました。';
		}
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'create_community':
					//パラメータのチェック
					$this->paramchk_create();
					if($err_flag != 0){
						$this->error = '入力エラーがあります。';
					}
					else{
						$this->create_community();
					}
				break;
				case 'join_community':
					$this->join_community();
				break;
				case 'search':
					// 検索処理
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		
		// コミュニティ一覧取得
		$this->get_communities();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作成のパラメータチェック
	@return	エラーの場合はfalseを返す
	*/
	//--------------------------------------------------------------------------------------
	function paramchk_create(){
		global $err_array;
		global $err_flag;
		
		if(!$this->user){
			$this->error = 'ログインしてください。';
			$err_flag = 1;
			return;
		}
		
		/// コミュニティ名の存在と空白チェック
		if(cutil_ex::chkset_err_field($err_array,'community_name','コミュニティ名','isset_nl')){
			$err_flag = 1;
		}
		
		if(empty($this->user['user_is_teacher'])){
			$this->error = 'コミュニティ作成権限がありません。';
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function create_community(){
		try {
			$communities_obj = new ccommunities();
			$result = $communities_obj->insert_community(false, array(
				'community_name' => $_POST['community_name'],
				'community_description' => $_POST['community_description'],
				'community_owner' => $this->user['uuid']
			));
			cutil::redirect_exit($_SERVER['PHP_SELF'] . '?created=1');
		} catch (Exception $e) {
			$this->error = 'コミュニティ作成に失敗しました。';
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ参加処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function join_community(){
		if(!$this->user){
			$this->error = 'ログインしてください。';
			return;
		}
		
		$invite_code = trim($_POST['invite_code']);
		if(empty($invite_code)){
			$this->error = '招待コードを入力してください。';
			return;
		}
		
		// 招待コードの確認
		$invite_obj = new ccommunity_invite_codes();
		$community_id = $invite_obj->get_community_by_code(false, $invite_code);
		
		if($community_id){
			// 既に参加していないか確認
			$community_users_obj = new ccommunity_users();
			$is_member = $community_users_obj->is_member(false, $this->user['uuid'], $community_id);
			
			if(!$is_member){
				// 参加処理を実行
				try {
					$result = $community_users_obj->add_user(false, $this->user['uuid'], $community_id);
					cutil::redirect_exit($_SERVER['PHP_SELF'] . '?joined=1');
				} catch (Exception $e) {
					$this->error = 'コミュニティ参加に失敗しました。';
				}
			} else {
				$this->error = 'すでにこのコミュニティに参加しています。';
			}
		} else {
			$this->error = '招待コードが無効です。';
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_communities(){
		if($this->user){
			$communities_obj = new ccommunities();
			$this->communities = $communities_obj->get_user_communities(false, $this->user['uuid']);
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー存在文字列の取得
	@return	エラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">入力エラーがあります。各項目のエラーを確認してください。</div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">処理に失敗しました。サポートを確認下さい。</div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー表示の取得
	@return	エラー表示文字列
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
	@brief	成功メッセージ表示の取得
	@return	成功メッセージ表示文字列
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
	@brief	コミュニティ名入力項目の取得
	@return	コミュニティ名入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_community_name(){
		global $err_array;
		$ret_str = '';
		$tgt = new ctextbox('community_name',$_POST['community_name'],'class="form-control" required');
		$ret_str = $tgt->get(false);
		if(isset($err_array['community_name'])){
			$ret_str .=  '<br /><span class="text-danger">' 
			. cutil::ret2br($err_array['community_name']) 
			. '</span>';
		}
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ説明入力項目の取得
	@return	コミュニティ説明入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_community_description(){
		$tgt = new ctextarea('community_description',$_POST['community_description'],'class="form-control" rows="2"');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード入力項目の取得
	@return	招待コード入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_invite_code(){
		$tgt = new ctextbox('invite_code','','class="form-control" required');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	検索キーワード入力項目の取得
	@return	検索キーワード入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_search_keyword(){
		$tgt = new ctextbox('search_keyword',$_POST['search_keyword'],'class="form-control rounded-pill" placeholder="検索"');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	参加・作成ボタンの取得
	@return	ボタン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_action_buttons(){
		$ret_str = '<div class="team-option d-flex flex-row flex-column flex-md-row fs-5 justify-content-center mb-3">';
		$ret_str .= '<ul class="navbar-nav flex-column flex-md-row gap-3 gap-md-5 w-100 align-items-center">';
		$ret_str .= '<li class="nav-item">';
		$ret_str .= '<a class="nav-link hover-text" href="#" data-bs-toggle="modal" data-bs-target="#joinCommunityModal">コミュニティに参加</a>';
		$ret_str .= '</li>';
		if($this->user && !empty($this->user['user_is_teacher'])){
			$ret_str .= '<li class="nav-item">';
			$ret_str .= '<a class="nav-link hover-text" href="#" data-bs-toggle="modal" data-bs-target="#createCommunityModal">コミュニティを作成</a>';
			$ret_str .= '</li>';
		}
		$ret_str .= '</ul></div>';
		return $ret_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ参加モーダルの取得
	@return	参加モーダル文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_join_modal(){
		$modal_str = <<<END_BLOCK
<div class="modal fade" id="joinCommunityModal" tabindex="-1" aria-labelledby="joinCommunityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="joinCommunityModalLabel">コミュニティに参加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form name="form1" action="{$_SERVER['PHP_SELF']}" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="communityJoinCode" class="form-label">参加コード</label>
                        {$this->get_invite_code()}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    <button type="button" class="btn btn-primary" onClick="set_func_form('join_community','')">参加</button>
                </div>
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
            </form>
        </div>
    </div>
</div>
END_BLOCK;
		return $modal_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作成モーダルの取得
	@return	作成モーダル文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_create_modal(){
		if(!$this->user || empty($this->user['user_is_teacher'])){
			return '';
		}
		
		$modal_str = <<<END_BLOCK
<div class="modal fade" id="createCommunityModal" tabindex="-1" aria-labelledby="createCommunityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCommunityModalLabel">コミュニティを作成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form name="form2" action="{$_SERVER['PHP_SELF']}" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="communityName" class="form-label">コミュニティ名</label>
                        {$this->get_community_name()}
                    </div>
                    <div class="mb-3">
                        <label for="communityDesc" class="form-label">説明（任意）</label>
                        {$this->get_community_description()}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    <button type="button" class="btn btn-primary" onClick="set_func_form2('create_community','')">作成</button>
                </div>
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
            </form>
        </div>
    </div>
</div>
END_BLOCK;
		return $modal_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティカード一覧の取得
	@return	コミュニティカード一覧文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_community_cards(){
		if(!$this->user){
			return '<div class="col-12"><div class="alert alert-warning">ログインしてください。</div></div>';
		}
		
		if(count($this->communities) == 0){
			return '<div class="col-12"><div class="alert alert-info">参加中またはオーナーのコミュニティがありません。</div></div>';
		}
		
		$cards_str = '';
		foreach($this->communities as $community){
			$community_name = display($community['community_name']);
			$community_desc = display($community['community_description']);
			$community_id = $community['community_id'];
			
			$cards_str .= <<<END_BLOCK
<div class="col-12 col-md-4 mb-4 px-3">
    <a class="nav-link" href="community.php?id={$community_id}">
        <div class="class-card border rounded p-3">
            <h5 class="mb-3 d-flex justify-content-start ms-2">{$community_name}</h5>
            <div class="d-flex align-items-start gap-3">
                <div class="img-fluid rounded bg-primary community-image-placeholder"></div>
                <p class="mb-0 flex-grow-1">{$community_desc}</p>
            </div>
        </div>
    </a>
</div>
END_BLOCK;
		}
		return $cards_str;
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
<!-- コンテンツ　-->
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        
        <?= $this->get_action_buttons(); ?>

        <?= $this->get_join_modal(); ?>
        <?= $this->get_create_modal(); ?>

        <!-- 検索バー -->
        <div class="row justify-content-center w-100">
            <div class="col-12 col-md-6 px-3">
                <form name="form3" action="<?= $_SERVER['PHP_SELF']; ?>" method="post" class="mt-4">
                    <div class="input-group bg-white rounded-pill border px-3 py-1 w-100 search-bar-styles">
                        <div class="d-flex justify-content-center align-items-center">🔍</div>
                        <?= $this->get_search_keyword(); ?>
                        <input type="hidden" name="func" value="search" />
                    </div>
                </form>
            </div>
        </div>

        <!-- コミュニティカード -->
        <div class="container mt-4">
            <div class="row community-card-row">
                <div class="container mt-4">
                    <div class="row justify-content-start">
                        <?= $this->get_community_cards(); ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</div>
<!-- /コンテンツ　-->
<script>
// prefecture_detail.phpスタイルのフォーム操作関数
function set_func_form(func, param) {
    document.form1.func.value = func;
    document.form1.param.value = param;
    document.form1.submit();
}

function set_func_form2(func, param) {
    document.form2.func.value = func;
    document.form2.param.value = param;
    document.form2.submit();
}
</script>
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

//ページを作成
$page_obj = new cnode();
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//サイドバー追加
$page_obj->add_child(cutil::create('csidebar'));
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処理
$page_obj->create();
//POST変数のデフォルト値をセット
$main_obj->post_default();
//本体実行（表示前処理）
$main_obj->execute();
//ページ全体を表示
$page_obj->display();

?>
