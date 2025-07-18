<?php
/*!
@file chat.php
@brief チャット画面
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
	public $group_list;
	public $all_users;
	public $selected_group_id;
	public $chats;
	public $templates;
	public $error;
	pub                <!-- 入力欄（ページ下部に固定） -->
                <form name="form1" action="<?= $this->get_form_action(); ?>" method="post" class="input-form" onsubmit="return submitMessageForm();"">c $success;
	public $target_user_id;
	public $target_user_name;
	
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
		$this->user = null;
		$this->group_list = array();
		$this->all_users = array();
		$this->selected_group_id = 0;
		$this->chats = array();
		$this->templates = array();
		$this->error = '';
		$this->success = '';
		$this->target_user_id = 0;
		$this->target_user_name = '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
		cutil::post_default("group_name",'');
		cutil::post_default("message",'');
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
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
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
		
		// 選択中グループIDを取得
		$this->selected_group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;
		
		// 個人チャット用パラメータ取得
		$this->target_user_id = isset($_GET['user']) ? intval($_GET['user']) : 0;
		$this->target_user_name = isset($_GET['name']) ? $_GET['name'] : '';
		
		// ターゲットユーザー名がない場合はDBから取得
		if (!$this->target_user_name && $this->target_user_id) {
			$this->get_target_user_name();
		}
		
		if(isset($_POST['func'])){
			switch($_POST['func']){
				case 'create_group':
					$this->create_group();
				break;
				case 'send_message':
					$this->send_message();
				break;
				default:
					//通常はありえない
					echo '原因不明のエラーです。';
					exit;
				break;
			}
		}
		
		// データ取得
		$this->get_group_list();
		$this->get_all_users();
		$this->get_chat_history();
		$this->get_templates();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	グループ作成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function create_group(){
		$group_name = trim($_POST['group_name']);
		$group_members = $_POST['group_members'] ?? array();
		
		if(empty($group_name)){
			$this->error = 'グループ名を入力してください。';
			return;
		}
		
		if(!is_array($group_members) || count($group_members) == 0){
			$this->error = '参加ユーザーを選択してください。';
			return;
		}
		
		try {
			// グループ作成
			$group_obj = new crecord();
			$dataarr = array('group_name' => $group_name);
			$group_id = $group_obj->insert_core(false, 'group_chats', $dataarr);
			if($group_id){
				// メンバー追加（自分も含める）
				$members = array_unique(array_merge(array($this->user['uuid']), $group_members));
				$member_obj = new crecord();
				foreach($members as $uid){
					$member_data = array('group_id' => $group_id, 'user_id' => $uid);
					$member_obj->insert_core(false, 'group_chat_members', $member_data);
				}
				
				cutil::redirect_exit($_SERVER['PHP_SELF'] . '?group_id=' . $group_id);
			} else {
				$this->error = 'グループ作成に失敗しました。';
			}
		} catch (Exception $e) {
			$this->error = 'グループ作成に失敗しました。';
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メッセージ送信処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function send_message(){
		$message = trim($_POST['message']);
		
		if(empty($message)){
			// チャット画面ではエラーメッセージを表示しない
			return;
		}
		
		if($this->selected_group_id){
			// グループチャットの場合
			try {
				$message_obj = new crecord();
				$dataarr = array(
					'group_id' => $this->selected_group_id,
					'user_id' => $this->user['uuid'],
					'message' => $message
				);
				$result = $message_obj->insert_core(false, 'group_chat_messages', $dataarr);
				if($result){
					// リダイレクトを削除して同じページで処理完了
					$_POST['message'] = ''; // 入力欄をクリア
				}
			} catch (Exception $e) {
				// チャット画面ではエラーメッセージを表示しない
			}
		} else if($this->target_user_id) {
			// 個人チャットの場合
			try {
				$db = new cdb();
				$stmt = $db->prepare('INSERT INTO chats (from_chat, to_chat, text_chat) VALUES (?, ?, ?)');
				$stmt->execute([$this->user['uuid'], $this->target_user_id, $message]);
				$_POST['message'] = ''; // 入力欄をクリア
			} catch (Exception $e) {
				// チャット画面ではエラーメッセージを表示しない
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	グループ一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_group_list(){
		try {
			$groups_obj = new cgroup_chats();
			$this->group_list = $groups_obj->get_all_groups(false);
		} catch (Exception $e) {
			$this->error = 'グループ一覧の取得に失敗しました。';
			$this->group_list = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	全ユーザー取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_all_users(){
		try {
			$users_obj = new cusers();
			$this->all_users = $users_obj->get_all_except_user(false, $this->user['uuid']);
		} catch (Exception $e) {
			$this->error = 'ユーザー一覧の取得に失敗しました。';
			$this->all_users = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	チャット履歴取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_chat_history(){
		if($this->selected_group_id){
			// グループチャットの場合
			try {
				$messages_obj = new cgroup_chat_messages();
				$this->chats = $messages_obj->get_group_messages(false, $this->selected_group_id);
			} catch (Exception $e) {
				$this->error = 'チャット履歴の取得に失敗しました。';
				$this->chats = array();
			}
		} else if($this->target_user_id) {
			// 1対1チャットの場合
			try {
				$db = new cdb();
				$stmt = $db->prepare('
					SELECT c.*, u.user_name AS from_user_name
					FROM chats c
					JOIN users u ON c.from_chat = u.user_id
					WHERE (c.from_chat = ? AND c.to_chat = ?) OR (c.from_chat = ? AND c.to_chat = ?)
					ORDER BY c.sent_at ASC
				');
				$stmt->execute([$this->user['uuid'], $this->target_user_id, $this->target_user_id, $this->user['uuid']]);
				$this->chats = $stmt->fetchAll();
			} catch (Exception $e) {
				$this->error = 'チャット履歴の取得に失敗しました。';
				$this->chats = array();
			}
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_templates(){
		try {
			$templates_obj = new ctemplates();
			$this->templates = $templates_obj->get_user_templates(false, $this->user['uuid']);
		} catch (Exception $e) {
			$this->templates = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ターゲットユーザー名取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_target_user_name(){
		try {
			$db = new cdb();
			$stmt = $db->prepare('SELECT user_name FROM users WHERE user_id = ?');
			$stmt->execute([$this->target_user_id]);
			$user_data = $stmt->fetch();
			if ($user_data) {
				$this->target_user_name = $user_data['user_name'];
			}
		} catch (Exception $e) {
			$this->target_user_name = 'ユーザー';
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
	@brief	グループ名入力項目の取得
	@return	グループ名入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_group_name(){
		$tgt = new ctextbox('group_name','','class="form-control" placeholder="グループ名" required');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	メッセージ入力項目の取得
	@return	メッセージ入力項目文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_message(){
		$tgt = new ctextbox('message','','class="form-control" placeholder="メッセージを入力" id="chatInput"');
		return $tgt->get(false);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	グループ一覧HTMLの取得
	@return	グループ一覧文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_group_list_html(){
		$list_str = '';
		foreach($this->group_list as $group){
			$group_name = display($group['group_name']);
			$group_id = $group['group_id'];
			$active_class = ($this->selected_group_id == $group_id) ? ' active' : '';
			
			$list_str .= <<<END_BLOCK
<li class="list-group-item list-group-item-action border-0 rounded my-1{$active_class}"
    onclick="location.href='chat.php?group_id={$group_id}'">
    {$group_name}
</li>
END_BLOCK;
		}
		return $list_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	ユーザーチェックボックス一覧の取得
	@return	ユーザーチェックボックス一覧文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_user_checkboxes(){
		$checkbox_str = '';
		// 自分のチェックボックス（チェック済み・無効）
		$checkbox_str .= <<<END_BLOCK
<div class="form-check">
    <input class="form-check-input" type="checkbox" value="{$this->user['uuid']}" id="user_self" checked disabled>
    <label class="form-check-label" for="user_self">自分</label>
</div>
END_BLOCK;
		
		// 他のユーザーのチェックボックス
		foreach($this->all_users as $user){
			$user_name = display($user['user_name']);
			$user_id = $user['user_id'];
			
			$checkbox_str .= <<<END_BLOCK
<div class="form-check">
    <input class="form-check-input" type="checkbox" name="group_members[]" value="{$user_id}" id="user_{$user_id}">
    <label class="form-check-label" for="user_{$user_id}">{$user_name}</label>
</div>
END_BLOCK;
		}
		return $checkbox_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	チャット履歴の取得
	@return	チャット履歴文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_chat_history_html(){
		$history_str = '';
		foreach($this->chats as $chat){
			// グループチャットか個人チャットかで処理を分ける
			if($this->selected_group_id) {
				// グループチャットの場合
				$user_name = display($chat['user_name']);
				$message_text = display($chat['message']);
				$from_id = $chat['user_id'];
				$sent_at = $chat['sent_at'];
			} else {
				// 個人チャットの場合
				$user_name = display($chat['from_user_name']);
				$message_text = display($chat['text_chat']);
				$from_id = $chat['from_chat'];
				$sent_at = $chat['sent_at'];
			}
			
			$time_display = date('H:i', strtotime($sent_at));
			$align_class = ($from_id == $this->user['uuid']) ? 'justify-content-end' : 'justify-content-start';
			$msg_class = ($from_id == $this->user['uuid']) ? 'chat-msg-sbm' : 'bg-white';
			
			$history_str .= <<<END_BLOCK
<div class="d-flex {$align_class} mb-2">
    <div class="chat-msg {$msg_class} border rounded p-2">
        <div>
            <span class="fw-bold">{$user_name}</span><br>
            {$message_text}
        </div>
        <div class="opacity-50 small mt-1 text-end">
            {$time_display}
        </div>
    </div>
</div>
END_BLOCK;
		}
		return $history_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレートドロップダウンの取得
	@return	テンプレートドロップダウン文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_template_dropdown(){
		$dropdown_str = '';
		foreach($this->templates as $template){
			$title = display($template['temprate_title']);
			$text = display($template['temprate_text']);
			
			$dropdown_str .= <<<END_BLOCK
<li>
    <a class="dropdown-item template-insert-btn" href="#" data-body="{$text}">
        {$title}
    </a>
</li>
END_BLOCK;
		}
		return $dropdown_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	フォームアクションURLの取得
	@return	フォームアクションURL文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_form_action(){
		if($this->selected_group_id) {
			return $_SERVER['PHP_SELF'] . '?group_id=' . $this->selected_group_id;
		} else if($this->target_user_id) {
			return $_SERVER['PHP_SELF'] . '?user=' . $this->target_user_id . '&name=' . urlencode($this->target_user_name);
		}
		return $_SERVER['PHP_SELF'];
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
    <title>チャット</title>
    <link rel="stylesheet" href="css/chat.css">
</head>
<div class="contents">
<?php // チャット画面ではメッセージ表示を無効化 ?>
<?php // <?= $this->get_err_flag(); ?> ?>
<?php // <?= $this->get_error_display(); ?> ?>
<?php // <?= $this->get_success_display(); ?> ?>

<div class="main-content-wrapper">
    <div class="container-fluid h-100">
        <div class="row vh-100 gx-0">
            <!-- グループ選択サイドバー -->
            <nav class="col-12 col-md-3 col-lg-3 px-0 group-sidebar d-flex flex-column bg-light border-end">
                <!-- テンプレート編集ボタン -->
                <div class="p-3 border-bottom bg-white">
                    <button class="btn btn-success w-100 mb-2" onclick="location.href='template.php'">
                        テンプレート編集
                    </button>
                </div>

                <div class="p-3 border-bottom bg-white">
                    <h5 class="mb-3 text-primary">グループ</h5>
                    <!-- 新規グループ作成ボタン -->
                    <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                        ＋ 新規グループ
                    </button>
                </div>
                <ul class="list-group list-group-flush flex-grow-1 overflow-auto group-list-scroll bg-light px-2">
                    <?= $this->get_group_list_html(); ?>
                </ul>
                
                <!-- 個人チャット相手表示 -->
                <?php if (!$this->selected_group_id && $this->target_user_name): ?>
                <div class="p-3 border-top bg-white">
                    <h6 class="mb-2 text-success">
                        <i class="bi bi-person-circle me-2"></i>個人チャット
                    </h6>
                    <div class="d-flex align-items-center p-2 bg-light rounded">
                        <img src="../main/img/headerImg/account.png" 
                             style="width: 32px; height: 32px; border-radius: 50%;" 
                             alt="プロフィール画像">
                        <div class="ms-2">
                            <div class="fw-bold text-primary" style="font-size: 0.9rem;">
                                <?php echo display($this->target_user_name); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </nav>

            <!-- チャット画面 -->
            <main class="col-12 col-md-9 col-lg-9 px-0 d-flex flex-column chat-main-area position-relative bg-white">
                <!-- チャット履歴 -->
                <div class="flex-grow-1 overflow-auto chat-history p-4 chat-history-scroll bg-light">
                    <?= $this->get_chat_history_html(); ?>
                </div>

                <!-- 入力欄（ページ下部に固定） -->
                <form name="form1" action="<?= $_SERVER['PHP_SELF'] . '?group_id=' . $this->selected_group_id; ?>" method="post" class="input-form" onsubmit="return submitMessageForm();">
                    <div class="mb-3 input-group" style="position: fixed !important; bottom: 2% !important; left: 33% !important; width: 65% !important;">
                        <?= $this->get_message(); ?>
                        <!-- +アイコンとドロップダウン -->
                        <div class="dropdown">
                            <button class="btn btn-link px-2" type="button" id="templateDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-plus fa-lg"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="templateDropdownBtn" style="max-height:200px;overflow-y:auto;">
                                <?= $this->get_template_dropdown(); ?>
                            </ul>
                        </div>
                        <button type="submit" class="btn btn-primary">送信</button>
                    </div>
                    <input type="hidden" name="func" value="send_message" />
                    <input type="hidden" name="param" value="" />
                </form>
            </main>
        </div>
    </div>
</div>

<!-- 新規グループ作成モーダル -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGroupModalLabel">新規グループ作成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <form name="form2" action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">グループ名</label>
                        <?= $this->get_group_name(); ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">参加ユーザー</label>
                        <?= $this->get_user_checkboxes(); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onClick="set_func_form2('create_group','')">作成</button>
                </div>
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
            </form>
        </div>
    </div>
</div>

                    <!--テンプレートモーダル-->
<div class="modal fade" id="myModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">テンプレート編集・差し替え</h5>
            </div>
            <div class="modal-body" id="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
            </div>
        </div>
    </div>
</div>
</div>
<!-- /コンテンツ　-->

<script>
// プレースホルダを全部抜き出す
function extractPlaceholders(template) {
    const regex = /{([^{}]+)}/g;
    let match;
    const results = new Set();
    while ((match = regex.exec(template)) !== null) {
        results.add(match[1]);
    }
    return Array.from(results);
}

// 置換
function fillTemplate(template, values) {
    return template.replace(/{([^{}]+)}/g, (m, key) => values[key] ?? m);
}

// ページロード時にモーダルの背景やbodyクラスが残っていたら消す
document.addEventListener('DOMContentLoaded', function () {
    document.body.classList.remove('modal-open');
    var backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(function (bd) { bd.parentNode.removeChild(bd); });

    // テンプレートを入力欄に挿入する
    document.querySelectorAll('.template-insert-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const originalTemplate = btn.getAttribute('data-body');
            // プレースホルダ重複排除（SetでOK）
            const regex = /{([^{}]+)}/g;
            let match;
            const placeholderSet = new Set();
            while ((match = regex.exec(originalTemplate)) !== null) {
                placeholderSet.add(match[1]);
            }
            const placeholders = Array.from(placeholderSet);

            // 編集可能本文
            let formHtml = `
        <div class="mb-3">
            <label>本文（書き換え可）</label>
            <textarea id="modalTemplateText" class="form-control" rows="4">${originalTemplate}</textarea>
        </div>
        <form id="placeholderForm">
            <table class="table table-sm">
              <thead><tr><th>項目</th><th>値</th></tr></thead><tbody>
    `;
            placeholders.forEach(ph => {
                formHtml += `
            <tr>
              <td>${ph}</td>
              <td><input type="text" class="form-control" name="${ph}"></td>
            </tr>
        `;
            });
            formHtml += `
              </tbody>
            </table>
            <button type="submit" class="btn btn-primary mt-2">反映</button>
        </form>
    `;
            document.getElementById('modal-body').innerHTML = formHtml;
            const modal = new bootstrap.Modal(document.getElementById('myModal'));
            modal.show();

            // サブミット時
            document.getElementById('placeholderForm').onsubmit = function (e) {
                e.preventDefault();
                const values = {};
                placeholders.forEach(ph => values[ph] = this.elements[ph].value);
                // 最新の本文で置換
                const currentTemplate = document.getElementById('modalTemplateText').value;
                document.getElementById('chatInput').value = fillTemplate(currentTemplate, values);
                modal.hide();
            };
        });
    });
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

// メッセージフォーム送信処理
function submitMessageForm() {
    // 空のメッセージは送信しない
    var messageInput = document.getElementById('chatInput');
    if (messageInput && messageInput.value.trim() === '') {
        return false;
    }
    // funcの値を確実にsend_messageに設定
    document.form1.func.value = 'send_message';
    return true;
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