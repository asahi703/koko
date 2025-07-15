<?php
/*!
@file community.php
@brief コミュニティ選択
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
			// insert_coreは成功時に新しいIDを返す
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
					// insert_coreは成功時に新しいIDまたはtrueを返す
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
		$tgt = new ctextbox('community_name',$_POST['community_name'],'class="form-control shadow" required');
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
		$tgt = new ctextarea('community_description',$_POST['community_description'],'class="form-control shadow"');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招待コード入力項目の取得
	@return	招待コード入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_invite_code(){
		$tgt = new ctextbox('invite_code','','class="form-control shadow" required');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作成ボタンの取得
	@return	作成ボタン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_create_button(){
		if($this->user && !empty($this->user['user_is_teacher'])){
			return '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommunityModal">コミュニティ作成</button>';
		}
		return '';
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
                <h5 class="modal-title" id="createCommunityModalLabel">コミュニティ作成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="form1" action="{$_SERVER['PHP_SELF']}" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="communityName" class="form-label">コミュニティ名<span class="text-danger">*</span></label>
                        {$this->get_community_name()}
                    </div>
                    <div class="mb-3">
                        <label for="communityDesc" class="form-label">説明</label>
                        {$this->get_community_description()}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    <button type="button" class="btn btn-primary px-5" onClick="set_func_form('create_community','')">作成</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="form2" action="{$_SERVER['PHP_SELF']}" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inviteCode" class="form-label">招待コード<span class="text-danger">*</span></label>
                        {$this->get_invite_code()}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    <button type="button" class="btn btn-success px-5" onClick="set_func_form2('join_community','')">参加</button>
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
	@brief	コミュニティ一覧の取得
	@return	コミュニティ一覧文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_community_list(){
		if(!$this->user){
			return '<div class="col-12"><div class="alert alert-warning">ログインしてください。</div></div>';
		}
		
		if(count($this->communities) == 0){
			return '<div class="col-12"><div class="alert alert-info">参加中またはオーナーのコミュニティがありません。</div></div>';
		}
		
		$list_str = '';
		foreach($this->communities as $community){
			$community_name = display($community['community_name']);
			$community_desc = display($community['community_description']);
			$community_id = $community['community_id'];
			$owner_badge = '';
			
			if($community['community_owner'] == $this->user['uuid']){
				$owner_badge = '<span class="badge bg-primary ms-2">オーナー</span>';
			}
			
			$list_str .= <<<END_BLOCK
<div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
    <a href="class_select.php?id={$community_id}" class="nav-link">
        <div class="class-card rounded d-flex align-items-center p-3 shadow-sm w-100 class-card-style">
            <div class="rounded me-3 class-card-image-placeholder"></div>
            <div class="flex-grow-1 text-start">
                <p class="mb-0 fw-bold">{$community_name}</p>
                <small class="text-muted">{$community_desc}</small>
                {$owner_badge}
            </div>
        </div>
    </a>
</div>
END_BLOCK;
		}
		return $list_str;
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
<head>
    <title>コミュニティ選択</title>
    <link rel="stylesheet" href="css/community.css">
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">

        <div class="d-flex justify-content-center gap-3 position-fixed class-create-button" style="top: 150px; right: 20px;">
            <?= $this->get_create_button(); ?>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#joinCommunityModal">
                コミュニティに参加
            </button>
        </div>

        <?= $this->get_create_modal(); ?>
        <?= $this->get_join_modal(); ?>

        <div class="w-100 d-flex justify-content-start align-items-start mb-3 community-name-display">
            <p class="mb-0 fs-3">参加中またはオーナーのコミュニティ一覧</p>
        </div>

        <div class="container mt-4 class-card-container-styles">
            <div class="row">
                <?= $this->get_community_list(); ?>
            </div>
        </div>
    </main>
</div>
</div>
<!-- /コンテンツ　-->
<script>
// ページロード時にモーダルの背景やbodyクラスが残っていたら消す
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.remove('modal-open');
    var backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function(bd){ bd.parentNode.removeChild(bd); });
});

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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