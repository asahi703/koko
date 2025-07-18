<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

$user = get_login_user();
if (!$user) {
    header('Location: signin.php');
    exit;
}

// ユーザーが教師かどうかを判定
$is_teacher = isset($user['user_is_teacher']) && $user['user_is_teacher'] == 1;
error_log("FAQ表示: ユーザーが教師かどうか = " . ($is_teacher ? 'true' : 'false'));
error_log("FAQ表示: user_is_teacher値 = " . ($user['user_is_teacher'] ?? 'NULL'));
error_log("FAQ表示: ユーザーID = " . ($user['uuid'] ?? 'NULL'));

// データベースから質問と回答を取得
try {
    $db = new cdb();
    
    // 回答済みの質問を取得
    $stmt = $db->prepare('
        SELECT 
            faq_id,
            COALESCE(faq_title, "") as faq_title,
            faq_question,
            faq_answer,
            faq_created_at,
            COALESCE(faq_user_id, 0) as faq_user_id,
            COALESCE(users.user_name, "匿名") as questioner_name
        FROM faq 
        LEFT JOIN users ON faq.faq_user_id = users.user_id 
        WHERE faq_answer IS NOT NULL AND faq_answer != ""
        ORDER BY faq_created_at DESC
    ');
    $stmt->execute();
    $answered_faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 回答待ちの質問を取得
    $stmt = $db->prepare('
        SELECT 
            faq_id,
            COALESCE(faq_title, "") as faq_title,
            faq_question,
            faq_created_at,
            COALESCE(faq_user_id, 0) as faq_user_id,
            COALESCE(users.user_name, "匿名") as questioner_name
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

            <!-- タイトルとボタン（横並び） -->
            <div class="faq-header">
                <h2 class="faq-title">送った質問</h2>
                <div class="faq-actions">
                    <?php if ($is_teacher): ?>
                    <a href="teacher_questions.php" class="btn btn-success me-2">
                        <i class="fas fa-chalkboard-teacher me-1"></i> 質問管理ページ
                    </a>
                    <?php endif; ?>
                    <a href="faq_create.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> 質問をする
                    </a>
                </div>
            </div>
            
            <!-- 説明テキスト -->
            <div class="faq-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>使い方：</strong> 質問をクリックすると回答が表示されます。お探しの回答が見つからない場合は「質問をする」ボタンから新しい質問を投稿してください。
            </div>

            <!-- FAQアコーディオン -->
            <div class="faq-accordion" id="faqAccordion">
                
                <?php if (!empty($pending_faqs)): ?>
                    <!-- 回答待ちの質問セクション -->
                    <div class="faq-section-header pending">
                        <h4><i class="fas fa-clock text-warning me-2"></i>回答待ちの質問</h4>
                    </div>
                    
                    <?php foreach ($pending_faqs as $faq): ?>
                        <div class="faq-item pending" data-faq-id="<?php echo $faq['faq_id']; ?>">
                            <div class="faq-question-header" onclick="toggleFAQ(<?php echo $faq['faq_id']; ?>)">
                                <div class="faq-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="faq-title-content">
                                    <strong><?php echo htmlspecialchars($faq['faq_title'] ?? 'タイトルなし'); ?></strong>
                                </div>
                                <div class="faq-meta">
                                    投稿者: <?php echo htmlspecialchars($faq['questioner_name'] ?? '匿名'); ?> | 
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
                                        <strong>質問内容</strong>
                                    </div>
                                    <div class="content-text">
                                        <?php echo nl2br(htmlspecialchars($faq['faq_question'])); ?>
                                    </div>
                                </div>
                                <div class="faq-answer pending">
                                    <div class="content-header">
                                        <i class="fas fa-hourglass-half text-warning me-2"></i>
                                        <strong>回答状況</strong>
                                    </div>
                                    <div class="content-text pending">
                                        この質問はまだ回答されていません。しばらくお待ちください。
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (!empty($answered_faqs)): ?>
                    <!-- 回答済みの質問セクション -->
                    <div class="faq-section-header answered">
                        <h4><i class="fas fa-check-circle text-success me-2"></i>回答済みの質問</h4>
                    </div>
                    
                    <?php foreach ($answered_faqs as $faq): ?>
                        <div class="faq-item answered" data-faq-id="<?php echo $faq['faq_id']; ?>">
                            <div class="faq-question-header" onclick="toggleFAQ(<?php echo $faq['faq_id']; ?>)">
                                <div class="faq-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="faq-title-content">
                                    <strong><?php echo htmlspecialchars($faq['faq_title'] ?? 'タイトルなし'); ?></strong>
                                </div>
                                <div class="faq-meta">
                                    投稿者: <?php echo htmlspecialchars($faq['questioner_name'] ?? '匿名'); ?> | 
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
                                        <strong>質問内容</strong>
                                    </div>
                                    <div class="content-text">
                                        <?php echo nl2br(htmlspecialchars($faq['faq_question'])); ?>
                                    </div>
                                </div>
                                <div class="faq-answer answered">
                                    <div class="content-header">
                                        <i class="fas fa-reply text-success me-2"></i>
                                        <strong>回答</strong>
                                    </div>
                                    <div class="content-text">
                                        <?php echo nl2br(htmlspecialchars($faq['faq_answer'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <?php if (empty($pending_faqs) && empty($answered_faqs)): ?>
                    <!-- 質問が全くない場合 -->
                    <div class="faq-empty">
                        <i class="fas fa-question-circle me-2"></i>
                        まだ質問が投稿されていません。「質問をする」ボタンから最初の質問を投稿してみませんか？
                    </div>
                <?php endif; ?>

                <!-- よくある質問セクション -->
                <div class="faq-section-header common">
                    <h4><i class="fas fa-info-circle me-2"></i>よくある質問</h4>
                </div>
               
                <!-- 既存のよくある質問 -->
                <div class="faq-item common" data-faq-id="common1">
                    <div class="faq-question-header" onclick="toggleFAQ('common1')">
                        <div class="faq-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="faq-title-content">
                            <strong>行事の予定がわかりません</strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common1">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>行事予定の確認方法</strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-calendar-check text-primary me-2"></i>
                                    <span>「マイページ > 行事カレンダー」から月間予定を確認</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-bell text-info me-2"></i>
                                    <span>「お知らせ」タブで重要な行事の詳細情報を確認</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-users text-success me-2"></i>
                                    <span>各コミュニティで個別の行事予定も確認可能</span>
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
                            <strong>配布物がどこにあるか分からない</strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common2">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>配布物の確認方法</strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <span>「お知らせ」タブのPDF一覧からダウンロード</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-search text-primary me-2"></i>
                                    <span>ファイル名や日付で検索可能</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-folder text-info me-2"></i>
                                    <span>各コミュニティ内の資料フォルダも確認</span>
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
                            <strong>チャットの通知が届かない</strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common3">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>通知設定の確認方法</strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-browser text-primary me-2"></i>
                                    <span>ブラウザの通知設定を確認</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-cog text-secondary me-2"></i>
                                    <span>アプリケーションの通知設定をONに</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-mobile-alt text-info me-2"></i>
                                    <span>端末の「設定 > 通知」も確認</span>
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
                            <strong>コミュニティの作成方法が知りたい</strong>
                        </div>
                        <div class="faq-toggle">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-content" id="content-common4">
                        <div class="faq-answer common">
                            <div class="content-header">
                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                <strong>コミュニティ作成手順</strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-plus-circle text-success me-2"></i>
                                    <span>「コミュニティを作成」ボタンをクリック</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-edit text-primary me-2"></i>
                                    <span>コミュニティ名と説明を入力</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                    <span><strong>※先生のみが作成可能です</strong></span>
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
                                <strong>パスワード再設定について</strong>
                            </div>
                            <div class="answer-steps">
                                <div class="step-item">
                                    <i class="fas fa-chalkboard-teacher text-success me-2"></i>
                                    <span>担任の先生に相談</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-user-shield text-primary me-2"></i>
                                    <span>学校の管理者にお問い合わせ</span>
                                </div>
                                <div class="step-item">
                                    <i class="fas fa-id-card text-info me-2"></i>
                                    <span>生徒証明書が必要な場合があります</span>
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
/* FAQページ専用スタイル */
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

/* 情報エリア */
.faq-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #2196f3;
    border-radius: 12px;
    color: #1565c0;
    font-size: 0.95rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

/* アコーディオンコンテナ */
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

/* FAQアイテム */
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

/* タイトルコンテンツ */
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

/* メタ情報 */
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

/* FAQコンテンツ */
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

/* 質問・回答エリア */
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

/* ステップアイテム */
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

/* 空の状態 */
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

/* レスポンシブ対応 */
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
    console.log('FAQ機能を初期化しています...');
    
    // 削除ボタンの処理
    console.log('削除機能を初期化しています...');
    const deleteButtons = document.querySelectorAll('.delete-btn');
    console.log(`${deleteButtons.length}個の削除ボタンを検出しました`);
});

// FAQの開閉
function toggleFAQ(faqId) {
    const faqItem = document.querySelector(`[data-faq-id="${faqId}"]`);
    const content = document.getElementById(`content-${faqId}`);
    
    if (faqItem && content) {
        const isActive = faqItem.classList.contains('active');
        
        // 他のFAQを閉じる
        document.querySelectorAll('.faq-item.active').forEach(item => {
            item.classList.remove('active');
        });
        
        // クリックしたFAQが閉じていた場合は開く
        if (!isActive) {
            faqItem.classList.add('active');
        }
    }
}

// FAQ削除関数
function deleteFAQ(buttonElement) {
    const faqId = buttonElement.getAttribute('data-faq-id');
    const faqTitle = buttonElement.getAttribute('data-faq-title');
    
    console.log(`削除ボタンがクリックされました: ID=${faqId}, タイトル=${faqTitle}`);
    
    if (confirm(`「${faqTitle}」を削除してもよろしいですか？`)) {
        console.log(`FAQ ID=${faqId} の削除を開始します`);
        
        // ボタンを無効化
        buttonElement.disabled = true;
        buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i> 処理中...';
        
        // フォームデータ作成
        const formData = new FormData();
        formData.append('faq_id', faqId);
        
        // サーバーにリクエスト
        fetch('faq_delete.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log(`サーバーからのレスポンス: status=${response.status}`);
            
            if (!response.ok) {
                throw new Error(`サーバーエラー: ${response.status}`);
            }
            
            return response.text().then(text => {
                try {
                    console.log('サーバーからの生レスポンス:', text);
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSONパースエラー:', e);
                    throw new Error('サーバーからの応答が不正です');
                }
            });
        })
        .then(data => {
            console.log('処理結果:', data);
            
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
                        
                        // 質問が全て削除された場合はページを更新
                        const remainingItems = document.querySelectorAll('.faq-item.pending, .faq-item.answered').length;
                        if (remainingItems === 0) {
                            window.location.reload();
                        }
                    }, 300);
                } else {
                    window.location.reload();
                }
            } else {
                alert(`削除できませんでした: ${data.message || '不明なエラー'}`);
                
                buttonElement.disabled = false;
                buttonElement.innerHTML = '<i class="fas fa-trash"></i> 削除';
            }
        })
        .catch(error => {
            console.error('エラーが発生しました:', error);
            alert(`削除処理に失敗しました: ${error.message}`);
            
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
