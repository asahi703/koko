<?php
/*!
@file faq.php
@brief よくある質問一覧
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
	public $faqs;
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
		$this->faqs = array();
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
		
		// FAQ一覧を取征E
		$this->get_faqs();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	FAQ一覧取征E
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	function get_faqs(){
		// サンプルFAQチE�Eタ
		$this->faqs = array(
			array(
				'faq_id' => 1,
				'question' => '行事�E予定がわかりません',
				'answer' => '行事�E予定�E「�Eイペ�Eジ > 行事カレンダー」からご確認いただけます、E
			),
			array(
				'faq_id' => 2,
				'question' => '配币E��がどこにあるか�EからなぁE,
				'answer' => '配币E��は「お知らせ」タブにあるPDF一覧からダウンロードできます、E
			),
			array(
				'faq_id' => 3,
				'question' => 'チャチE��の通知が届かなぁE,
				'answer' => '通知設定がOFFになってぁE��可能性があります。アプリの設定をご確認ください、E
			),
			array(
				'faq_id' => 4,
				'question' => 'コミュニティの作�E方法が知りたぁE,
				'answer' => '「コミュニティを作�E」�Eタンを押し、忁E��事頁E��入力して作�Eできます、E
			)
		);
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
	@brief	FAQアコーチE��オンの取征E
	@return	FAQアコーチE��オン斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_faq_accordion(){
		$accordion_str = '';
		$first = true;
		
		foreach($this->faqs as $faq){
			$question = display($faq['question']);
			$answer = display($faq['answer']);
			$faq_id = $faq['faq_id'];
			$show_class = $first ? 'show' : '';
			$expanded = $first ? 'true' : 'false';
			$collapsed = $first ? '' : 'collapsed';
			
			$accordion_str .= <<<END_BLOCK
<div class="accordion-item">
    <h2 class="accordion-header" id="heading{$faq_id}">
        <button class="accordion-button {$collapsed}" type="button" data-bs-toggle="collapse"
                data-bs-target="#collapse{$faq_id}" aria-expanded="{$expanded}" aria-controls="collapse{$faq_id}">
            {$question}
        </button>
    </h2>
    <div id="collapse{$faq_id}" class="accordion-collapse collapse {$show_class}" aria-labelledby="heading{$faq_id}"
         data-bs-parent="#faqAccordion">
        <div class="accordion-body">
            {$answer}
        </div>
    </div>
</div>
END_BLOCK;
			$first = false;
		}
		
		return $accordion_str;
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
    <title>よくある質啁E/title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>
require_once('common/session.php');
require_once('common/dbmanager.php');

$user = get_login_user();
if (!$user) {
    header('Location: signin.php');
    exit;
}

// ユーザーが教師かどぁE��を判宁E
$is_teacher = isset($user['user_is_teacher']) && $user['user_is_teacher'] == 1;
error_log("FAQ表示: ユーザーが教師かどぁE�� = " . ($is_teacher ? 'true' : 'false'));
error_log("FAQ表示: user_is_teacher値 = " . ($user['user_is_teacher'] ?? 'NULL'));
error_log("FAQ表示: ユーザーID = " . ($user['uuid'] ?? 'NULL'));

// チE�Eタベ�Eスから質問と回答を取征E
try {
    $db = new cdb();
    
    // 回答済みの質問を取征E
    $stmt = $db->prepare('
        SELECT 
            faq_id,
            COALESCE(faq_title, "") as faq_title,
            faq_question,
            faq_answer,
            faq_created_at,
            COALESCE(faq_user_id, 0) as faq_user_id,
            COALESCE(users.user_name, "匿吁E) as questioner_name
        FROM faq 
        LEFT JOIN users ON faq.faq_user_id = users.user_id 
        WHERE faq_answer IS NOT NULL AND faq_answer != ""
        ORDER BY faq_created_at DESC
    ');
    $stmt->execute();
    $answered_faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 回答征E��の質問を取征E
    $stmt = $db->prepare('
        SELECT 
            faq_id,
            COALESCE(faq_title, "") as faq_title,
            faq_question,
            faq_created_at,
            COALESCE(faq_user_id, 0) as faq_user_id,
            COALESCE(users.user_name, "匿吁E) as questioner_name
        FROM faq 
        LEFT JOIN users ON faq.faq_user_id = users.user_id 
        WHERE faq_answer IS NULL OR faq_answer = ""
        ORDER BY faq_created_at DESC
    ');
    $stmt->execute();
    $pending_faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("FAQ取得エラー: " . $e->getMessage());
    $answered_faqs = [];
    $pending_faqs = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content-wrapper">
    <main class="main-content-styles">
        <div class="container-fluid" style="padding-top: 20px; padding-bottom: 0;">

            <!-- タイトルとボタン�E�横並び�E�E-->
            <div class="faq-header">
                <h2 class="faq-title">送った質啁E/h2>
                <div class="faq-actions">
                    <?php if ($is_teacher): ?>
                    <a href="teacher_questions.php" class="btn btn-success me-2">
                        <i class="fas fa-chalkboard-teacher me-1"></i> 質問管琁E�Eージ
                    </a>
                    <?php endif; ?>
                    <a href="faq_create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> 質問をする
                    </a>
                </div>
            </div>
            
            <!-- 説明テキスチE-->
            <div class="faq-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>使ぁE���E�E/strong> 質問をクリチE��すると回答が表示されます。お探し�E回答が見つからなぁE��合�E「質問をする」�Eタンから新しい質問を投稿してください、E
            </div>

            <!-- FAQアコーチE��オン -->
            <div class="accordion shadow" id="faqAccordion">
                <?= $this->get_faq_accordion(); ?>
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
            <div class="faq-accordion" id="faqAccordion">
                
                <?php if (!empty($pending_faqs)): ?>
                    <!-- 回答征E��の質問セクション -->
                    <div class="faq-section-header pending">
                        <h4><i class="fas fa-clock text-warning me-2"></i>回答征E��の質啁E/h4>
                    </div>
                    
                    <?php foreach ($pending_faqs as $faq): ?>
                        <div class="faq-item pending" data-faq-id="<?php echo $faq['faq_id']; ?>">
                            <div class="faq-question-header" onclick="toggleFAQ(<?php echo $faq['faq_id']; ?>)">
                                <div class="faq-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="faq-title-content">
                                    <strong><?php echo htmlspecialchars($faq['faq_title'] ?? 'タイトルなぁE); ?></strong>
                                </div>
                                <div class="faq-meta">
                                    投稿老E <?php echo htmlspecialchars($faq['questioner_name'] ?? '匿吁E); ?> | 
                                    <?php echo date('Y/m/d', strtotime($faq['faq_created_at'])); ?>
                                </div>
                                <?php if ($is_teacher || (string)$faq['faq_user_id'] === (string)$user['uuid']): ?>
                                <div class="faq-actions">
                                    <button class="delete-btn" 
                                            data-faq-id="<?php echo $faq['faq_id']; ?>"
                                            data-faq-title="<?php echo htmlspecialchars($faq['faq_title'] ?? ''); ?>"
                                            onclick="event.stopPropagation(); deleteFAQ(this);">
                                        <i class="fas fa-trash"></i> 削除
                                    </button>
                                </div>
                                <?php endif; ?>
                                <div class="faq-toggle">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="faq-content" id="content-<?php echo $faq['faq_id']; ?>">
                                <div class="faq-question">
                                    <div class="content-header">
                                        <i class="fas fa-question-circle text-primary me-2"></i>
                                        <strong>質問�E容</strong>
                                    </div>
                                    <div class="content-text">
                                        <?php echo nl2br(htmlspecialchars($faq['faq_question'])); ?>
                                    </div>
                                </div>
                                <div class="faq-answer pending">
                                    <div class="content-header">
                                        <i class="fas fa-hourglass-half text-warning me-2"></i>
                                        <strong>回答状況E/strong>
                                    </div>
                                    <div class="content-text pending">
                                        こ�E質問�Eまだ回答されてぁE��せん。しばらくお征E��ください、E
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($answered_faqs)): ?>
                    <!-- 回答済みの質問セクション -->
                    <div class="faq-section-header answered">
                        <h4><i class="fas fa-check-circle text-success me-2"></i>回答済みの質啁E/h4>
                    </div>
                    
                    <?php foreach ($answered_faqs as $faq): ?>
                        <div class="faq-item answered" data-faq-id="<?php echo $faq['faq_id']; ?>">
                            <div class="faq-question-header" onclick="toggleFAQ(<?php echo $faq['faq_id']; ?>)">
                                <div class="faq-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="faq-title-content">
                                    <strong><?php echo htmlspecialchars($faq['faq_title'] ?? 'タイトルなぁE); ?></strong>
                                </div>
                                <div class="faq-meta">
                                    投稿老E <?php echo htmlspecialchars($faq['questioner_name'] ?? '匿吁E); ?> | 
                                    <?php echo date('Y/m/d', strtotime($faq['faq_created_at'])); ?>
                                </div>
                                <?php if ($is_teacher || (string)$faq['faq_user_id'] === (string)$user['uuid']): ?>
                                <div class="faq-actions">
                                    <button class="delete-btn" 
                                            data-faq-id="<?php echo $faq['faq_id']; ?>"
                                            data-faq-title="<?php echo htmlspecialchars($faq['faq_title'] ?? ''); ?>"
                                            onclick="event.stopPropagation(); deleteFAQ(this);">
                                        <i class="fas fa-trash"></i> 削除
                                    </button>
                                </div>
                                <?php endif; ?>
                                <div class="faq-toggle">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="faq-content" id="content-<?php echo $faq['faq_id']; ?>">
                                <div class="faq-question">
                                    <div class="content-header">
                                        <i class="fas fa-question-circle text-primary me-2"></i>
                                        <strong>質問�E容</strong>
                                    </div>
                                    <div class="content-text">
                                        <?php echo nl2br(htmlspecialchars($faq['faq_question'])); ?>
                                    </div>
                                </div>
                                <div class="faq-answer answered">
                                    <div class="content-header">
                                        <i class="fas fa-reply text-success me-2"></i>
                                        <strong>回筁E/strong>
                                    </div>
                                    <div class="content-text">
                                        <?php echo nl2br(htmlspecialchars($faq['faq_answer'])); ?>
                                    </div>
                                </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (empty($pending_faqs) && empty($answered_faqs)): ?>
                    <!-- 質問が全くなぁE��吁E-->
                    <div class="faq-empty">
                        <i class="fas fa-question-circle me-2"></i>
                        まだ質問が投稿されてぁE��せん。「質問をする」�Eタンから最初�E質問を投稿してみませんか！E
                    </div>
                <?php endif; ?>

                <!-- よくある質問セクション -->
                <div class="faq-section-header common">
                    <h4><i class="fas fa-info-circle me-2"></i>よくある質啁E/h4>
                </div>
               
                <!-- 既存�Eよくある質啁E-->
                <div class="faq-item common" data-faq-id="common1">
                    <div class="faq-question-header" onclick="toggleFAQ('common1')">
                        <div class="faq-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="faq-title-content">
                            <strong>行事�E予定がわかりません</strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common1">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>行事予定�E確認方況E/strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                    <span>「�Eイペ�Eジ > 行事カレンダー」から月間予定を確誁E/span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-bell text-info me-2"></i>
                                    <span>「お知らせ」タブで重要な行事�E詳細惁E��を確誁E/span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-users text-success me-2"></i>
                                    <span>吁E��ミュニティで個別の行事予定も確認可能</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item common" data-faq-id="common2">
                    <div class="faq-question-header" onclick="toggleFAQ('common2')">
                        <div class="faq-icon">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <div class="faq-title-content">
                            <strong>配币E��がどこにあるか�EからなぁE/strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common2">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>配币E��の確認方況E/strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <span>「お知らせ」タブ�EPDF一覧からダウンローチE/span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-search text-primary me-2"></i>
                                    <span>ファイル名や日付で検索可能</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-folder text-info me-2"></i>
                                    <span>吁E��ミュニティ冁E�E賁E��フォルダも確誁E/span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item common" data-faq-id="common3">
                    <div class="faq-question-header" onclick="toggleFAQ('common3')">
                        <div class="faq-icon">
                            <i class="fas fa-bell-slash"></i>
                        </div>
                        <div class="faq-title-content">
                            <strong>チャチE��の通知が届かなぁE/strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common3">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>通知設定�E確認方況E/strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-browser text-primary me-2"></i>
                                    <span>ブラウザの通知設定を確誁E/span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-cog text-secondary me-2"></i>
                                    <span>アプリケーションの通知設定をONに</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-mobile-alt text-info me-2"></i>
                                    <span>端末の「設宁E> 通知」も確誁E/span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item common" data-faq-id="common4">
                    <div class="faq-question-header" onclick="toggleFAQ('common4')">
                        <div class="faq-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="faq-title-content">
                            <strong>コミュニティの作�E方法が知りたぁE/strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common4">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>コミュニティ作�E手頁E/strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-plus-circle text-success me-2"></i>
                                    <span>「コミュニティを作�E」�EタンをクリチE��</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-edit text-primary me-2"></i>
                                    <span>コミュニティ名と説明を入劁E/span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <span><strong>※先生のみが作�E可能でぁE/strong></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="faq-item common" data-faq-id="common5">
                    <div class="faq-question-header" onclick="toggleFAQ('common5')">
                        <div class="faq-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="faq-title-content">
                            <strong>パスワードを忘れました</strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common5">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>パスワード�E設定につぁE��</strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                                    <span>拁E��の先生に相諁E/span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-user-shield text-primary me-2"></i>
                                    <span>学校の管琁E��E��お問ぁE��わせ</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-id-card text-info me-2"></i>
                                    <span>生徒証明書が忁E��な場合がありまぁE/span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- #faqAccordion -->
        </div> <!-- .container-fluid -->
    </main>
</div>

<!-- CSS -->
<style>
/* FAQペ�Eジ専用スタイル */
.main-content-wrapper {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding-left: 90px;
    padding-right: 10px;
}

.main-content-styles {
    margin-top: 60px;
    margin-bottom: 20px;
    padding: 0;
    max-width: none;
}

.container-fluid {
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin: 0 !important;
}

/* ヘッダー */
.faq-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.faq-title {
    color: #2c3e50;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 0;
}

.faq-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

/* 惁E��エリア */
.faq-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #2196f3;
    border-radius: 12px;
    color: #1565c0;
    font-size: 0.95rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

/* アコーチE��オンコンチE�� */
.faq-accordion {
    max-width: 900px;
    margin: 0 auto;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* セクションヘッダー */
.faq-section-header {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    padding: 1rem 1.5rem;
    text-align: center;
    border-bottom: 1px solid rgba(0,0,0,.125);
}

.faq-section-header h4 {
    margin: 0;
    color: #856404;
    font-weight: 700;
}

/* FAQアイチE�� */
.faq-item {
    border-bottom: 1px solid rgba(0,0,0,.125);
    transition: all 0.3s ease;
}

.faq-item:last-child {
    border-bottom: none;
}

/* FAQクエスチョンヘッダー */
.faq-question-header {
    display: grid;
    grid-template-columns: auto 1fr auto auto auto;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 60px;
}

.faq-item.pending .faq-question-header {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
}

.faq-item.answered .faq-question-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.faq-item.common .faq-question-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.faq-question-header:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

/* アイコン */
.faq-icon {
    display: flex;
    align-items: center;
    font-size: 1.1rem;
    width: 24px;
    justify-content: center;
}

/* タイトルコンチE��チE*/
.faq-title-content {
    min-width: 0;
    flex: 1;
}

.faq-title-content strong {
    display: block;
    font-weight: 600;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* メタ惁E�� */
.faq-meta {
    font-size: 0.8rem;
    opacity: 0.9;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}

/* アクション */
.faq-actions {
    display: flex;
    gap: 0.5rem;
}

.delete-btn {
    background: rgba(220, 53, 69, 0.9);
    border: none;
    color: white;
    border-radius: 6px;
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.delete-btn:hover {
    background: #dc3545;
    transform: scale(1.05);
}

/* トグル */
.faq-toggle {
    font-size: 0.9rem;
    transition: transform 0.3s ease;
}

.faq-item.active .faq-toggle {
    transform: rotate(180deg);
}

/* FAQコンチE��チE*/
.faq-content {
    display: none;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.95);
    border-top: 3px solid #667eea;
}

.faq-item.answered .faq-content {
    border-top-color: #28a745;
}

.faq-item.pending .faq-content {
    border-top-color: #ffc107;
}

.faq-item.active .faq-content {
    display: block;
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 500px;
    }
}

/* 質問�E回答エリア */
.faq-question, .faq-answer {
    margin-bottom: 1.5rem;
}

.faq-question:last-child, .faq-answer:last-child {
    margin-bottom: 0;
}

.content-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

.content-header strong {
    color: #2d3748;
    font-size: 1.1rem;
    font-weight: 700;
}

.content-text {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    padding: 1rem;
    line-height: 1.6;
    color: #2d3748;
}

.faq-question .content-text {
    border-left: 3px solid #007bff;
}

.faq-answer.answered .content-text {
    border-left: 3px solid #28a745;
}

.faq-answer.pending .content-text {
    border-left: 3px solid #17a2b8;
    font-style: italic;
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
}

/* スチE��プアイチE�� */
.answer-steps {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.step-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
    border-left: 3px solid transparent;
    transition: all 0.3s ease;
}

.step-item:hover {
    background: rgba(255, 255, 255, 0.95);
    border-left-color: #667eea;
    transform: translateX(5px);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.step-item i {
    margin-right: 0.75rem;
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

.step-item span {
    color: #4a5568;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* 空の状慁E*/
.faq-empty {
    text-align: center;
    padding: 3rem 1rem;
    color: #1565c0;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #2196f3;
    border-radius: 12px;
}

/* ボタンスタイル */
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #17a085 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

/* レスポンシブ対忁E*/
@media (max-width: 768px) {
    .main-content-wrapper {
        padding-left: 0;
        padding-right: 0;
    }
    
    .main-content-styles {
        padding: 0 0.5rem;
        margin-top: 40px;
    }
    
    .faq-header {
        flex-direction: column;
        text-align: center;
    }
    
    .faq-accordion {
        margin: 0 0.5rem;
    }
    
    .faq-question-header {
        grid-template-columns: auto 1fr auto;
        grid-template-rows: auto auto;
        gap: 0.5rem;
        padding: 0.75rem;
    }
    
    .faq-title-content {
        grid-column: 1 / -1;
        grid-row: 1;
    }
    
    .faq-meta {
        grid-column: 1 / 2;
        grid-row: 2;
        font-size: 0.7rem;
        max-width: none;
    }
    
    .faq-actions {
        grid-column: 2;
        grid-row: 2;
        justify-self: end;
    }
    
    .faq-toggle {
        grid-column: 3;
        grid-row: 1;
    }
    
    .delete-btn {
        padding: 0.3rem 0.6rem;
        font-size: 0.75rem;
    }
    
    .faq-content {
        padding: 1rem;
    }
}

@media (min-width: 769px) and (max-width: 1199px) {
    .main-content-wrapper {
        padding-left: 85px;
        padding-right: 10px;
    }
    
    .main-content-styles {
        padding: 0 1rem;
        margin-top: 50px;
    }
}

@media (min-width: 1200px) {
    .main-content-wrapper {
        padding-left: 90px;
        padding-right: 10px;
    }
    
    .main-content-styles {
        padding: 0 2rem;
        margin-top: 60px;
    }
}

html {
    scroll-behavior: smooth;
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('FAQ機�Eを�E期化してぁE��ぁE..');
    
    // 削除ボタンの処琁E
    console.log('削除機�Eを�E期化してぁE��ぁE..');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    console.log(`${deleteButtons.length}個�E削除ボタンを検�Eしました`);
});

// FAQの開閉
function toggleFAQ(faqId) {
    const faqItem = document.querySelector(`[data-faq-id="${faqId}"]`);
    const content = document.getElementById(`content-${faqId}`);
    
    if (faqItem && content) {
        const isActive = faqItem.classList.contains('active');
        
        // 他�EFAQを閉じる
        document.querySelectorAll('.faq-item.active').forEach(item => {
            item.classList.remove('active');
        });
        
        // クリチE��したFAQが閉じてぁE��場合�E開く
        if (!isActive) {
            faqItem.classList.add('active');
        }
    }
}

// FAQ削除関数
function deleteFAQ(buttonElement) {
    const faqId = buttonElement.getAttribute('data-faq-id');
    const faqTitle = buttonElement.getAttribute('data-faq-title');
    
    console.log(`削除ボタンがクリチE��されました: ID=${faqId}, タイトル=${faqTitle}`);
    
    if (confirm(`、E{faqTitle}」を削除してもよろしぁE��すか�E�`)) {
        console.log(`FAQ ID=${faqId} の削除を開始します`);
        
        // ボタンを無効匁E
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 処琁E��...';
        
        // フォームチE�Eタ作�E
        const formData = new FormData();
        formData.append('faq_id', faqId);
        
        // サーバ�EにリクエスチE
        fetch('faq_delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log(`サーバ�Eからのレスポンス: status=${response.status}`);
            
            if (!response.ok) {
                throw new Error(`サーバ�Eエラー: ${response.status}`);
            }
            
            return response.text().then(text => {
                try {
                    console.log('サーバ�Eからの生レスポンス:', text);
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSONパ�Eスエラー:', e);
                    throw new Error('サーバ�Eからの応答が不正でぁE);
                }
            });
        })
        .then(data => {
            console.log('処琁E��果:', data);
            
            if (data.success) {
                alert('削除しました');
                
                // 要素をアニメーションで削除
                const faqItem = buttonElement.closest('.faq-item');
                if (faqItem) {
                    faqItem.style.transition = 'all 0.3s ease-out';
                    faqItem.style.opacity = '0';
                    faqItem.style.transform = 'translateX(-100%)';
                    
                    setTimeout(() => {
                        faqItem.remove();
                        
                        // 質問が全て削除された場合�Eペ�Eジを更新
                        const remainingItems = document.querySelectorAll('.faq-item.pending, .faq-item.answered').length;
                        if (remainingItems === 0) {
                            window.location.reload();
                        }
                    }, 300);
                } else {
                    window.location.reload();
                }
            } else {
                alert(`削除できませんでした: ${data.message || '不�Eなエラー'}`);
                
                buttonElement.disabled = false;
                buttonElement.innerHTML = '<i class="fas fa-trash"></i> 削除';
            }
        })
        .catch(error => {
            console.error('エラーが発生しました:', error);
            alert(`削除処琁E��失敗しました: ${error.message}`);
            
            buttonElement.disabled = false;
            buttonElement.innerHTML = '<i class="fas fa-trash"></i> 削除';
        });
    }
}
</script>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
