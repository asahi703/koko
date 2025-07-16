<?php
/*!
@file template.php
@brief テンプレート管理
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
	public $user_id;
	public $db;
	public $title_box;
	public $content_box;
	public $edit_title_box;
	public $edit_content_box;
	public $templates;
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
		$this->user_id = '';
		$this->db = null;
		$this->title_box = null;
		$this->content_box = null;
		$this->edit_title_box = null;
		$this->edit_content_box = null;
		$this->templates = array();
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
		if(!isset($_POST['temprate_title'])){
			$_POST['temprate_title'] = "";
		}
		if(!isset($_POST['temprate_text'])){
			$_POST['temprate_text'] = "";
		}
		if(!isset($_POST['edit_temprate_title'])){
			$_POST['edit_temprate_title'] = "";
		}
		if(!isset($_POST['edit_temprate_text'])){
			$_POST['edit_temprate_text'] = "";
		}
		if(!isset($_POST['temprate_id'])){
			$_POST['temprate_id'] = "";
		}
		if(!isset($_POST['delete_template_id'])){
			$_POST['delete_template_id'] = "";
		}
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
			$this->user_id = $this->user['uuid'];
		}
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
		
		//フォームボックス作成
		$this->title_box = new ctextbox('temprate_title', '', 'class="form-control" required');
		
		$this->content_box = new ctextarea('temprate_text', '', 'class="form-control" required');
		
		$this->edit_title_box = new ctextbox('edit_temprate_title', '', 'class="form-control" required');
		
		$this->edit_content_box = new ctextarea('edit_temprate_text', '', 'class="form-control" required');
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
		
		try{
			// テンプレート作成処理
			if(isset($_POST['create_template'])){
				$title = trim($_POST['temprate_title']);
				$text = trim($_POST['temprate_text']);
				
				if(empty($title)){
					$this->error = 'タイトルを入力してください。';
				} elseif(empty($text)){
					$this->error = '本文を入力してください。';
				} else {
					$this->create_template();
				}
			}
			
			// テンプレート編集処理
			if(isset($_POST['edit_template'])){
				$title = trim($_POST['edit_temprate_title']);
				$text = trim($_POST['edit_temprate_text']);
				
				if(empty($title)){
					$this->error = 'タイトルを入力してください。';
				} elseif(empty($text)){
					$this->error = '本文を入力してください。';
				} else {
					$this->edit_template();
				}
			}
			
			// テンプレート削除処理
			if(isset($_POST['delete_template_id']) && !empty($_POST['delete_template_id'])){
				$this->delete_template();
			}
			
			// テンプレート一覧取得
			$this->get_templates();
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート作成処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function create_template(){
		global $err_flag;
		
		try{
			$title = $_POST['temprate_title'];
			$text = $_POST['temprate_text'];
			
			$stmt = $this->db->prepare('INSERT INTO temprates (temprate_title, temprate_text, temprate_user) VALUES (?, ?, ?)');
			$stmt->execute([$title, $text, $this->user_id]);
			$this->success = 'テンプレートを作成しました。';
			
			// フォームリセット
			$_POST['temprate_title'] = '';
			$_POST['temprate_text'] = '';
			
		} catch(exception $e){
			$this->error = 'データベースエラーが発生しました: ' . $e->getMessage();
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート編集処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function edit_template(){
		global $err_flag;
		
		try{
			$edit_id = intval($_POST['temprate_id']);
			$title = $_POST['edit_temprate_title'];
			$text = $_POST['edit_temprate_text'];
			
			if($edit_id > 0){
				$stmt = $this->db->prepare('UPDATE temprates SET temprate_title = ?, temprate_text = ? WHERE temprate_id = ? AND temprate_user = ?');
				$stmt->execute([$title, $text, $edit_id, $this->user_id]);
				$this->success = 'テンプレートを更新しました。';
			}
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート削除処理
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function delete_template(){
		global $err_flag;
		
		try{
			$delete_id = intval($_POST['delete_template_id']);
			
			if($delete_id > 0){
				$stmt = $this->db->prepare('DELETE FROM temprates WHERE temprate_id = ? AND temprate_user = ?');
				$stmt->execute([$delete_id, $this->user_id]);
				$this->success = 'テンプレートを削除しました。';
			}
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	テンプレート一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_templates(){
		try{
			$stmt = $this->db->prepare('SELECT temprate_id, temprate_title, temprate_text FROM temprates WHERE temprate_user = ? ORDER BY temprate_id DESC');
			$stmt->execute([$this->user_id]);
			$this->templates = $stmt->fetchAll();
		} catch(exception $e){
			$this->templates = array();
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
	@brief	テンプレートカード一覧表示の取得
	@return	テンプレートカード一覧表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_template_cards(){
		$cards_str = '';
		
		foreach($this->templates as $template){
			$template_id = $template['temprate_id'];
			$title = display($template['temprate_title']);
			$text = display($template['temprate_text']);
			$text_nl2br = nl2br($text);
			
			$cards_str .= <<<END_BLOCK
<div class="col-12 col-sm-6 col-md-4">
    <div class="template-card p-0 bg-white rounded shadow-sm h-100 d-flex flex-column align-items-center template-card-hover"
         data-bs-toggle="modal" data-bs-target="#templateEditModal"
         data-id="{$template_id}"
         data-title="{$title}"
         data-body="{$text}">
        <div class="template-title fs-4 mb-2 d-flex align-items-center justify-content-center w-100">
            {$title}
            <form method="post" class="ms-2 d-inline position-relative"
                  onsubmit="return confirm('本当に削除しますか？');" style="margin-bottom:0;">
                <input type="hidden" name="delete_template_id" value="{$template_id}">
                <button type="submit" class="tmp-dlt-btn btn btn-link p-0 ms-2 text-danger"
                        style="font-size:1em; opacity:0.7;" title="削除">
                    <i class="fa-solid fa-trash fa-sm"></i>
                </button>
            </form>
        </div>
        <div class="template-body w-100">
            {$text_nl2br}
        </div>
    </div>
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
<head>
    <title>テンプレート管理</title>
    <link rel="stylesheet" href="css/template.css">
</head>

<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<!--戻るボタン-->
<a href="chat.php" class="arrow-back-btn">
    <i class="fa-solid fa-arrow-left fa-2xl"></i>
</a>

<div class="container-fluid">
    <div class="row">
        <!-- メインコンテンツ -->
        <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-template"
              style="margin-top: 80px; margin-bottom: 60px; max-width: 1200px;">
            
            <!--タイトル-->
            <div class="d-flex flex-row w-100 justify-content-center">
                <div>
                    <p class="fs-3">テンプレート一覧</p>
                </div>
                <!-- 作成ボタン：右上固定 -->
                <button type="button" class="tmp-create-btn btn btn-primary mb-3" data-bs-toggle="modal"
                        data-bs-target="#templateCreateModal">
                    テンプレートを作成する
                </button>
            </div>

            <!-- テンプレート作成モーダル -->
            <div class="modal fade" id="templateCreateModal" tabindex="-1"
                 aria-labelledby="templateCreateModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                            <div class="modal-header">
                                <h5 class="modal-title" id="templateCreateModalLabel">テンプレートを作成</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="templateTitle" class="form-label">タイトル<span class="text-danger">*</span></label>
                                    <input type="text" name="temprate_title" id="templateTitle" class="form-control" value="<?= display($_POST['temprate_title']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="templateBody" class="form-label">本文<span class="text-danger">*</span></label>
                                    <textarea name="temprate_text" id="templateBody" class="form-control" rows="5" required><?= display($_POST['temprate_text']); ?></textarea>
                                </div>
                                <!--プレースホルダー入力欄-->
                                <div class="mb-3 d-flex align-items-center">
                                    <input type="text" id="placeholderName" class="form-control w-auto me-2" placeholder="プレースホルダ名">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="insertPlaceholderBtn">差し込み</button>
                                    <span class="ms-2 text-muted">例: {名前}, {日付} など</span>
                                </div>
                                <div class="mt-2" id="placeholderList"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                <button type="submit" name="create_template" class="btn btn-primary">作成</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- テンプレート編集モーダル -->
            <div class="modal fade" id="templateEditModal" tabindex="-1" aria-labelledby="templateEditModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="temprate_id" id="editTemplateId">
                            <div class="modal-header">
                                <h5 class="modal-title" id="templateEditModalLabel">テンプレート編集</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editTemplateTitle" class="form-label">タイトル<span class="text-danger">*</span></label>
                                    <input type="text" name="edit_temprate_title" id="editTemplateTitle" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="editTemplateBody" class="form-label">本文 <span class="text-danger">*</span></label>
                                    <textarea name="edit_temprate_text" id="editTemplateBody" class="form-control" rows="5" required></textarea>
                                </div>
                                <div class="row g-2 align-items-center mb-2">
                                    <div class="col">
                                        <input type="text" id="editPlaceholderName" class="form-control" placeholder="プレースホルダ名">
                                    </div>
                                    <div class="col-auto">
                                        <button type="button" class="btn btn-outline-secondary" id="insertEditPlaceholderBtn">差し込み</button>
                                    </div>
                                </div>
                                <div class="mb-3" id="editPlaceholderList"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                <button type="submit" name="edit_template" class="btn btn-primary">保存</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="template-list-container w-100 mt-3 template-list-scroll">
                <div class="row g-4">
                    <?= $this->get_template_cards(); ?>
                </div>
            </div>
        </main>
    </div>
</div>

</div>
<!-- /コンテンツ　-->

<script>
// 共通のUIセットアップ関数
function setupPlaceholderUI(config) {
    const textarea = document.getElementById(config.textareaId);
    const phInput = document.getElementById(config.inputId);
    const insertBtn = document.getElementById(config.buttonId);
    const phListDiv = document.getElementById(config.listDivId);

    // 要素が存在しない場合は処理を中止
    if (!textarea || !phInput || !insertBtn || !phListDiv) {
        console.log('Required elements not found for:', config);
        return;
    }

    // 差し込み
    insertBtn.addEventListener('click', function () {
        let ph = phInput.value.trim();
        if (!ph) return;
        insertPlaceholder(textarea, ph);
        phInput.value = '';
        updatePlaceholderList();
    });

    // テキストエリアから{〇〇}を抽出してボタンを表示
    function updatePlaceholderList() {
        const placeholders = extractPlaceholders(textarea.value);
        phListDiv.innerHTML = '';
        placeholders.forEach(ph => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-info me-2 mb-2';
            btn.textContent = `{${ph}}`;
            btn.onclick = function () {
                insertPlaceholder(textarea, ph);
                updatePlaceholderList();
            };
            phListDiv.appendChild(btn);
        });
    }

    // 入力やコピペでも反映
    textarea.addEventListener('input', updatePlaceholderList);

    // 初期化
    updatePlaceholderList();
}

// プレースホルダー抽出
function extractPlaceholders(text) {
    const regex = /{([^{}]+)}/g;
    const set = new Set();
    let match;
    while ((match = regex.exec(text)) !== null) {
        set.add(match[1]);
    }
    return Array.from(set);
}

// カーソル位置に差し込む
function insertPlaceholder(textarea, ph) {
    const phString = `{${ph}}`;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    textarea.value = textarea.value.substring(0, start) + phString + textarea.value.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + phString.length;
    textarea.focus();
}

// DOMが完全に読み込まれてから実行
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // 作成画面用
    setupPlaceholderUI({
        textareaId: 'templateBody',
        inputId: 'placeholderName',
        buttonId: 'insertPlaceholderBtn',
        listDivId: 'placeholderList'
    });

    // 編集画面用
    setupPlaceholderUI({
        textareaId: 'editTemplateBody',
        inputId: 'editPlaceholderName',
        buttonId: 'insertEditPlaceholderBtn',
        listDivId: 'editPlaceholderList'
    });

    // 編集モーダルに値セット（カード等クリック時）
    document.querySelectorAll('.template-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.getElementById('editTemplateId').value = card.getAttribute('data-id');
            document.getElementById('editTemplateTitle').value = card.getAttribute('data-title');
            document.getElementById('editTemplateBody').value = card.getAttribute('data-body');
            // プレースホルダーUIも更新
            // 再セット後にupdate
            setTimeout(() => {
                const textarea = document.getElementById('editTemplateBody');
                const event = new Event('input');
                textarea.dispatchEvent(event);
            }, 0);
        });
    });
    
    // モーダルのテスト用ログ
    const createButton = document.querySelector('[data-bs-target="#templateCreateModal"]');
    if (createButton) {
        createButton.addEventListener('click', function() {
            console.log('Create button clicked');
        });
    } else {
        console.log('Create button not found');
    }
    
    // Bootstrapモーダルの手動初期化
    const createModal = document.getElementById('templateCreateModal');
    const editModal = document.getElementById('templateEditModal');
    
    if (createModal) {
        console.log('Create modal found');
        // Bootstrap 5のモーダル初期化
        const bootstrapCreateModal = new bootstrap.Modal(createModal);
        
        // 作成ボタンのクリックイベントを手動で設定
        if (createButton) {
            createButton.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Manually opening create modal');
                bootstrapCreateModal.show();
            });
        }
    } else {
        console.log('Create modal not found');
    }
    
    if (editModal) {
        console.log('Edit modal found');
        const bootstrapEditModal = new bootstrap.Modal(editModal);
    } else {
        console.log('Edit modal not found');
    }
});
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