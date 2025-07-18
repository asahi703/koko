<?php
/*!
@file community.php
@brief コミュニティ選抁E
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
	@brief  POST変数のチE��ォルト値をセチE��
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("community_name",'');
		cutil::post_default("community_description",'');
		cutil::post_default("invite_code",'');
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
		
		// GET パラメータでの成功メチE��ージ
		if(isset($_GET['created'])){
			$this->success = 'コミュニティを作�Eしました、E;
		}
		if(isset($_GET['joined'])){
			$this->success = 'コミュニティに参加しました、E;
		}
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'create_community':
					//パラメータのチェチE��
					$this->paramchk_create();
					if($err_flag != 0){
						$this->error = '入力エラーがあります、E;
					}
					else{
						$this->create_community();
					}
				break;
				case 'join_community':
					$this->join_community();
				break;
				default:
					//通常はありえなぁE
					echo '原因不�Eのエラーです、E;
					exit;
				break;
			}
		}
		
		// コミュニティ一覧取征E
		$this->get_communities();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作�EのパラメータチェチE��
	@return	エラーの場合�Efalseを返す
	*/
	//--------------------------------------------------------------------------------------
	function paramchk_create(){
		global $err_array;
		global $err_flag;
		
		if(!$this->user){
			$this->error = 'ログインしてください、E;
			$err_flag = 1;
			return;
		}
		
		/// コミュニティ名�E存在と空白チェチE��
		if(cutil_ex::chkset_err_field($err_array,'community_name','コミュニティ吁E,'isset_nl')){
			$err_flag = 1;
		}
		
		if(empty($this->user['user_is_teacher'])){
			$this->error = 'コミュニティ作�E権限がありません、E;
			$err_flag = 1;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作�E処琁E
	@return	なぁE
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
			$this->error = 'コミュニティ作�Eに失敗しました、E;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ参加処琁E
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	function join_community(){
		if(!$this->user){
			$this->error = 'ログインしてください、E;
			return;
		}
		
		$invite_code = trim($_POST['invite_code']);
		if(empty($invite_code)){
			$this->error = '招征E��ードを入力してください、E;
			return;
		}
		
		// 招征E��ード�E確誁E
		$invite_obj = new ccommunity_invite_codes();
		$community_id = $invite_obj->get_community_by_code(false, $invite_code);
		
		if($community_id){
			// 既に参加してぁE��ぁE��確誁E
			$community_users_obj = new ccommunity_users();
			$is_member = $community_users_obj->is_member(false, $this->user['uuid'], $community_id);
			
			if(!$is_member){
				// 参加処琁E��実衁E
				try {
					$result = $community_users_obj->add_user(false, $this->user['uuid'], $community_id);
					// insert_coreは成功時に新しいIDまた�Etrueを返す
					cutil::redirect_exit($_SERVER['PHP_SELF'] . '?joined=1');
				} catch (Exception $e) {
					$this->error = 'コミュニティ参加に失敗しました、E;
				}
			} else {
				$this->error = 'すでにこ�Eコミュニティに参加してぁE��す、E;
			}
		} else {
			$this->error = '招征E��ードが無効です、E;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ一覧取征E
	@return	なぁE
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
	@brief	コミュニティ名�E力頁E��の取征E
	@return	コミュニティ名�E力頁E��斁E���E
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
	@brief	コミュニティ説明�E力頁E��の取征E
	@return	コミュニティ説明�E力頁E��斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_community_description(){
		$tgt = new ctextarea('community_description',$_POST['community_description'],'class="form-control shadow"');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	招征E��ード�E力頁E��の取征E
	@return	招征E��ード�E力頁E��斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_invite_code(){
		$tgt = new ctextbox('invite_code','','class="form-control shadow" required');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作�Eボタンの取征E
	@return	作�Eボタン斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_create_button(){
		if($this->user && !empty($this->user['user_is_teacher'])){
			return '<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommunityModal">コミュニティ作�E</button>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コミュニティ作�Eモーダルの取征E
	@return	作�Eモーダル斁E���E
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
                <h5 class="modal-title" id="createCommunityModalLabel">コミュニティ作�E</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form name="form1" action="{$_SERVER['PHP_SELF']}" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="communityName" class="form-label">コミュニティ吁Espan class="text-danger">*</span></label>
                        {$this->get_community_name()}
                    </div>
                    <div class="mb-3">
                        <label for="communityDesc" class="form-label">説昁E/label>
                        {$this->get_community_description()}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じめE/button>
                    <button type="button" class="btn btn-primary px-5" onClick="set_func_form('create_community','')">作�E</button>
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
	@brief	コミュニティ参加モーダルの取征E
	@return	参加モーダル斁E���E
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
                        <label for="inviteCode" class="form-label">招征E��ーチEspan class="text-danger">*</span></label>
                        {$this->get_invite_code()}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じめE/button>
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
	@brief	コミュニティ一覧の取征E
	@return	コミュニティ一覧斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_community_list(){
		if(!$this->user){
			return '<div class="col-12"><div class="alert alert-warning">ログインしてください、E/div></div>';
		}
		
		if(count($this->communities) == 0){
			return '<div class="col-12"><div class="alert alert-info">参加中また�Eオーナ�Eのコミュニティがありません、E/div></div>';
		}
		
		$list_str = '';
		foreach($this->communities as $community){
			$community_name = display($community['community_name']);
			$community_desc = display($community['community_description']);
			$community_id = $community['community_id'];
			$owner_badge = '';
			
			if($community['community_owner'] == $this->user['uuid']){
				$owner_badge = '<span class="badge bg-primary ms-2">オーナ�E</span>';
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
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロチE��終亁E
?>
<!-- コンチE��チE��-->
<head>
    <title>コミュニティ選抁E/title>
    <link rel="stylesheet" href="css/community.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        <?php if ($user && !empty($user['user_is_teacher'])): ?>
        <div class="modal fade" id="createCommunityModal" tabindex="-1" aria-labelledby="createCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCommunityModalLabel">コミュニティ作�E</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="community.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="communityName" class="form-label">コミュニティ吁Espan class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow" id="communityName" name="community_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="communityDesc" class="form-label">説昁E/label>
                                <textarea class="form-control shadow" id="communityDesc" name="community_description"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じめE/button>
                            <button type="submit" class="btn btn-primary px-5">作�E</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="modal fade" id="joinCommunityModal" tabindex="-1" aria-labelledby="joinCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinCommunityModalLabel">コミュニティに参加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="community.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="inviteCode" class="form-label">招征E��ーチEspan class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow" id="inviteCode" name="invite_code" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じめE/button>
                            <button type="submit" class="btn btn-success px-5">参加</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- メンバ�E表示モーダル -->
        <div class="modal fade" id="membersModal" tabindex="-1" aria-labelledby="membersModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="membersModalLabel">コミュニティメンバ�E</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="membersContent">
                            <!-- メンバ�E惁E��がここに動的に読み込まれまぁE-->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じめE/button>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="w-100 d-flex justify-content-start align-items-start mb-3 community-name-display">
            <p class="mb-0 fs-3">参加中また�Eオーナ�Eのコミュニティ一覧</p>
        </div>

        <div class="container mt-4 class-card-container-styles">
            <div class="row">
                <?= $this->get_community_list(); ?>
                                        onclick="loadMembers(<?php echo $community['community_id']; ?>, '<?php echo htmlspecialchars($community['community_name']); ?>')"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#membersModal">
                                    <i class="bi bi-people"></i> メンバ�E
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($user): ?>
                    <div class="col-12">
                        <div class="alert alert-info">参加中また�Eオーナ�Eのコミュニティがありません、E/div>
                    </div>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning">ログインしてください、E/div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
</div>
<!-- /コンチE��チE��-->
<script>
// ペ�Eジロード時にモーダルの背景めEodyクラスが残ってぁE��ら消す
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

// メンバ�E惁E��を読み込む関数
function loadMembers(communityId, communityName) {
    // モーダルタイトルを更新
    document.getElementById('membersModalLabel').textContent = communityName + ' のメンバ�E';
    
    // ローチE��ング表示
    document.getElementById('membersContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">メンバ�E惁E��を読み込み中...</p>
        </div>
    `;
    
    // Ajax でメンバ�E惁E��を取征E
    fetch('get_community_members.php?community_id=' + communityId)
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('API Response:', data); // チE��チE��用
            
            if (data.success) {
                if (data.members && data.members.length > 0) {
                    let html = '<div class="row">';
                    data.members.forEach((member, index) => {
                        // アイコンパスの処琁E
                        let iconSrc;
                        if (member.user_icon && member.user_icon.trim() !== '') {
                            if (member.user_icon.startsWith('img/user_icons/')) {
                                iconSrc = '../' + member.user_icon;
                            } else {
                                iconSrc = '../img/user_icons/' + member.user_icon;
                            }
                        } else {
                            iconSrc = '../main/img/headerImg/account.png';
                        }
                        
                        html += `
                            <div class="col-12 col-md-6 mb-3">
                                <div class="d-flex align-items-center p-3 border rounded member-card">
                                    <!-- プロフィール画像（ドロチE�Eダウン付き�E�E-->
                                    <div class="dropdown position-relative">
                                        <img src="${iconSrc}" 
                                             style="width: 50px; height: 50px; border-radius: 50%; cursor: pointer; object-fit: cover;" 
                                             alt="プロフィール画僁E
                                             id="memberProfile${index}"
                                             data-bs-toggle="dropdown" 
                                             aria-expanded="false"
                                             class="member-profile-img">
                                        
                                        <!-- ミニプロフィールドロチE�Eダウン -->
                                        <ul class="dropdown-menu dropdown-menu-start p-3" 
                                            aria-labelledby="memberProfile${index}" 
                                            style="min-width: 280px;">
                                            <li class="d-flex align-items-center mb-3">
                                                <img src="${iconSrc}" 
                                                     style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;" 
                                                     alt="プロフィール画僁E>
                                                <div class="ms-3">
                                                    <h6 class="mb-1 fw-bold">${member.user_name || 'ユーザー名なぁE}</h6>
                                                    <small class="text-muted">${member.user_email || 'メールアドレスなぁE}</small>
                                                    <div class="mt-1">
                                                        <span class="badge ${member.user_is_teacher == '1' ? 'bg-success' : 'bg-primary'}">
                                                            ${member.user_is_teacher == '1' ? '先生' : '生征E}
                                                        </span>
                                                        ${member.is_owner ? '<span class="badge bg-warning text-dark ms-1">オーナ�E</span>' : ''}
                                                    </div>
                                                </div>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li class="mb-2">
                                                <small class="text-muted">最近�E活勁E/small>
                                                <div class="mt-1">
                                                    <small>最終ログイン: 2時間剁E/small><br>
                                                    <small>投稿数: 12件</small>
                                                </div>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item d-flex align-items-center" href="profile.php?user=${member.user_id}">
                                                <i class="bi bi-person me-2"></i>プロフィールを見る
                                            </a></li>
                                            <li><a class="dropdown-item d-flex align-items-center" href="chat.php?user=${member.user_id}&name=${encodeURIComponent(member.user_name || 'ユーザー')}">
                                                <i class="bi bi-chat-dots me-2"></i>メチE��ージを送る
                                            </a></li>
                                        </ul>
                                    </div>
                                    
                                    <div class="ms-3 flex-grow-1">
                                        <h6 class="mb-1">${member.user_name || 'ユーザー名なぁE}</h6>
                                        <small class="text-muted d-block">${member.user_email || 'メールアドレスなぁE}</small>
                                        <div class="mt-2 d-flex align-items-center justify-content-between">
                                            <div>
                                                <span class="badge ${member.user_is_teacher == '1' ? 'bg-success' : 'bg-primary'}">
                                                    ${member.user_is_teacher == '1' ? '先生' : '生征E}
                                                </span>
                                                ${member.is_owner ? '<span class="badge bg-warning text-dark ms-1">オーナ�E</span>' : ''}
                                            </div>
                                            <div>
                                                <a href="chat.php?user=${member.user_id}&name=${encodeURIComponent(member.user_name || 'ユーザー')}" 
                                                   class="btn btn-outline-primary btn-sm me-1" 
                                                   title="チャチE��">
                                                    <i class="bi bi-chat-dots"></i>
                                                </a>
                                                <a href="profile.php?user=${member.user_id}" 
                                                   class="btn btn-outline-secondary btn-sm" 
                                                   title="プロフィール">
                                                    <i class="bi bi-person"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    document.getElementById('membersContent').innerHTML = html;
                } else {
                    document.getElementById('membersContent').innerHTML = `
                        <div class="alert alert-info">
                            こ�Eコミュニティにはメンバ�Eがいません、E
                        </div>
                    `;
                }
            } else {
                document.getElementById('membersContent').innerHTML = `
                    <div class="alert alert-danger">
                        エラー: ${data.error || 'メンバ�E惁E��の取得に失敗しました、E}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('membersContent').innerHTML = `
                <div class="alert alert-danger">
                    ネットワークエラーが発生しました: ${error.message}
                </div>
            `;
        });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
