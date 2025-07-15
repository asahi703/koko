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
error_log("FAQ表示: ユーザーID = " . ($user['uuid'] ?? 'NULL')); // ユーザーIDをログ出力

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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0 text-start">送った質問</h2>
                <div class="d-flex">
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
            <div class="alert alert-info mb-3 text-start" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <strong>使い方：</strong> 質問をクリックすると回答が表示されます。お探しの回答が見つからない場合は「質問をする」ボタンから新しい質問を投稿してください。
            </div>

            <!-- FAQアコーディオン -->
            <div class="accordion shadow" id="faqAccordion">
                
                <?php if (!empty($pending_faqs)): ?>
                    <!-- 回答待ちの質問セクション -->
                    <div class="faq-section-header">
                        <h4 class="text-center mb-3">
                            <i class="fas fa-clock text-warning me-2"></i>回答待ちの質問
                        </h4>
                    </div>
                    
                    <?php foreach ($pending_faqs as $index => $faq): ?>
                        <div class="accordion-item pending-faq-item">
                            <h2 class="accordion-header" id="headingPendingFaq<?php echo $faq['faq_id']; ?>">
                                <button class="accordion-button collapsed pending-faq-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapsePendingFaq<?php echo $faq['faq_id']; ?>" aria-expanded="false" aria-controls="collapsePendingFaq<?php echo $faq['faq_id']; ?>">
                                    <div class="w-100">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock me-2"></i>
                                            <strong><?php echo htmlspecialchars($faq['faq_title'] ?? 'タイトルなし'); ?></strong>
                                            <small class="text-muted ms-auto me-3">
                                                投稿者: <?php echo htmlspecialchars($faq['questioner_name'] ?? '匿名'); ?> | 
                                                <?php echo date('Y/m/d', strtotime($faq['faq_created_at'])); ?>
                                            </small>
                                            <!-- 教師または投稿者本人が削除可能 -->
                                            <?php if ($is_teacher || (string)$faq['faq_user_id'] === (string)$user['uuid']): ?>
                                                <button class="btn btn-danger delete-faq-btn" 
                                                        data-faq-id="<?php echo $faq['faq_id']; ?>"
                                                        data-faq-title="<?php echo htmlspecialchars($faq['faq_title'] ?? ''); ?>"
                                                        onclick="event.stopPropagation();">
                                                    <i class="fas fa-trash me-1"></i>削除
                                                </button>
                                            <?php else: ?>
                                                <small class="text-muted">(教師または投稿者のみ削除可能)</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapsePendingFaq<?php echo $faq['faq_id']; ?>" class="accordion-collapse collapse" aria-labelledby="headingPendingFaq<?php echo $faq['faq_id']; ?>"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <div class="answer-content pending-answer">
                                        <div class="answer-header">
                                            <i class="fas fa-question-circle text-primary me-2"></i>
                                            <strong>質問内容</strong>
                                        </div>
                                        <div class="question-content">
                                            <?php echo nl2br(htmlspecialchars($faq['faq_question'])); ?>
                                        </div>
                                        <div class="answer-header mt-3">
                                            <i class="fas fa-hourglass-half text-warning me-2"></i>
                                            <strong>回答状況</strong>
                                        </div>
                                        <div class="pending-message">
                                            この質問はまだ回答されていません。しばらくお待ちください。
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- セパレーター -->
                    <div class="faq-separator">
                        <hr class="my-4">
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($answered_faqs)): ?>
                    <!-- 回答済みの質問のみ表示 -->
                    <div class="faq-section-header">
                        <h4 class="text-center mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>回答済みの質問
                        </h4>
                    </div>
                    
                    <?php foreach ($answered_faqs as $index => $faq): ?>
                        <div class="accordion-item user-faq-item">
                            <h2 class="accordion-header" id="headingUserFaq<?php echo $faq['faq_id']; ?>">
                                <button class="accordion-button collapsed user-faq-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseUserFaq<?php echo $faq['faq_id']; ?>" aria-expanded="false" aria-controls="collapseUserFaq<?php echo $faq['faq_id']; ?>">
                                    <div class="w-100">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong><?php echo htmlspecialchars($faq['faq_title'] ?? 'タイトルなし'); ?></strong>
                                            <small class="text-muted ms-auto me-3">
                                                投稿者: <?php echo htmlspecialchars($faq['questioner_name'] ?? '匿名'); ?> | 
                                                <?php echo date('Y/m/d', strtotime($faq['faq_created_at'])); ?>
                                            </small>
                                            <!-- 教師または投稿者本人が削除可能 -->
                                            <?php if ($is_teacher || (string)$faq['faq_user_id'] === (string)$user['uuid']): ?>
                                                <button class="btn btn-danger delete-faq-btn" 
                                                        data-faq-id="<?php echo $faq['faq_id']; ?>"
                                                        data-faq-title="<?php echo htmlspecialchars($faq['faq_title'] ?? ''); ?>"
                                                        onclick="event.stopPropagation();">
                                                    <i class="fas fa-trash me-1"></i>削除
                                                </button>
                                            <?php else: ?>
                                                <small class="text-muted">(教師または投稿者のみ削除可能)</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapseUserFaq<?php echo $faq['faq_id']; ?>" class="accordion-collapse collapse" aria-labelledby="headingUserFaq<?php echo $faq['faq_id']; ?>"
                                 data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <div class="answer-content">
                                        <div class="answer-header">
                                            <i class="fas fa-question-circle text-primary me-2"></i>
                                            <strong>質問内容</strong>
                                        </div>
                                        <div class="question-content">
                                            <?php echo nl2br(htmlspecialchars($faq['faq_question'])); ?>
                                        </div>
                                        <div class="answer-header mt-3">
                                            <i class="fas fa-reply text-success me-2"></i>
                                            <strong>回答</strong>
                                        </div>
                                        <div class="user-answer-content">
                                            <?php echo nl2br(htmlspecialchars($faq['faq_answer'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <?php endif; ?>
                
                <?php if (empty($pending_faqs) && empty($answered_faqs)): ?>
                    <!-- 質問が全くない場合 -->
                    <div class="alert alert-info text-center">
                        <i class="fas fa-question-circle me-2"></i>
                        まだ質問が投稿されていません。「質問をする」ボタンから最初の質問を投稿してみませんか？
                    </div>
                <?php endif; ?>

        </div> <!-- .container-fluid -->
    </main>
</div>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* FAQページ専用スタイル */
.main-content-wrapper {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding-left: 90px; /* サイドバーとの間隔をさらに削減 */
    padding-right: 10px; /* 右の余白も削減 */
}

.main-content-styles {
    margin-top: 60px; /* 上の余白を削減 */
    margin-bottom: 20px; /* 下の余白を削減 */
    padding: 0;
    max-width: none;
}

.container-fluid {
    max-width: 100% !important;
    padding-left: 0 !important;
    padding-right: 0 !important;
    margin: 0 !important;
}

.accordion {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin: 0 1rem 0 1rem; /* 左右に余白を追加 */
    max-width: 900px;
    box-sizing: border-box;
}

.accordion-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,.125);
    margin-bottom: 0; /* アイテム間の余白を削除 */
}

.accordion-item:last-child {
    border-bottom: none;
}

.accordion-collapse {
    transition: all 0.3s ease-in-out;
}

.accordion-collapse:not(.show) {
    display: none;
}

.accordion-collapse.show {
    display: block;
    animation: slideDown 0.3s ease-in-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
    }
    to {
        opacity: 1;
        max-height: 200px;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
}

.accordion-button {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem 1.25rem; /* パディングを削減 */
    border-radius: 0;
    box-shadow: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
    justify-content: flex-start;
}

.accordion-button:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    transform: translateY(-1px);
}

.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    color: white;
    box-shadow: none;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

.accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.accordion-body {
    background: rgba(255, 255, 255, 0.95);
    padding: 1rem; /* パディングを削減 */
    font-size: 1.05rem;
    line-height: 1.6;
    border-top: 3px solid #667eea;
}

.answer-content {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
    border-radius: 12px;
    padding: 1rem; /* パディングを削減 */
    border-left: 4px solid #667eea;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.answer-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem; /* マージンを削減 */
    padding-bottom: 0.5rem; /* パディングを削減 */
    border-bottom: 2px solid rgba(102, 126, 234, 0.2);
}

.answer-header strong {
    color: #2d3748;
    font-size: 1.1rem;
    font-weight: 700;
}

.answer-steps {
    display: flex;
    flex-direction: column;
    gap: 0.5rem; /* ギャップを削減 */
}

.step-item {
    display: flex;
    align-items: center;
    padding: 0.5rem 0.75rem; /* パディングを削減 */
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

.step-item span {
    color: #4a5568;
    font-size: 0.95rem;
    line-height: 1.5;
}

.step-item i {
    margin-right: 0.75rem;
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

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

h2 {
    color: #2c3e50;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #2196f3;
    border-radius: 12px;
    color: #1565c0;
    font-size: 0.95rem;
}

.alert-info .fas {
    color: #2196f3;
}

/* ユーザー投稿質問のスタイル */
.user-faq-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,.125);
    margin-bottom: 0;
}

.user-faq-button {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem 1.25rem;
    border-radius: 0;
    box-shadow: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
    justify-content: flex-start;
    position: relative;
}

.user-faq-button:hover {
    background: linear-gradient(135deg, #218838 0%, #17a085 100%);
    transform: translateY(-1px);
}

.user-faq-button:not(.collapsed) {
    background: linear-gradient(135deg, #218838 0%, #17a085 100%);
    color: white;
    box-shadow: none;
}

.user-faq-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.user-faq-button small {
    font-size: 0.8rem;
    opacity: 0.9;
}

.user-answer-content {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    padding: 1rem;
    border-left: 3px solid #28a745;
    margin-top: 0.75rem;
    line-height: 1.6;
    color: #2d3748;
}

.question-content {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    padding: 1rem;
    border-left: 3px solid #007bff;
    margin-top: 0.75rem;
    line-height: 1.6;
    color: #2d3748;
    font-style: normal;
}

.faq-separator {
    margin: 1.5rem 0;
    text-align: center;
}

.faq-separator hr {
    border-top: 2px solid rgba(102, 126, 234, 0.3);
    margin: 1rem 0;
}

.faq-separator h4 {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    display: inline-block;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* 回答待ち質問のスタイル */
.faq-section-header h4 {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    display: inline-block;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    color: #856404;
}

.pending-faq-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,.125);
    margin-bottom: 0;
}

.pending-faq-button {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem 1.25rem;
    border-radius: 0;
    box-shadow: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: left;
    justify-content: flex-start;
    position: relative;
}

.pending-faq-button:hover {
    background: linear-gradient(135deg, #e0a800 0%, #e8590c 100%);
    transform: translateY(-1px);
}

.pending-faq-button:not(.collapsed) {
    background: linear-gradient(135deg, #e0a800 0%, #e8590c 100%);
    color: white;
    box-shadow: none;
}

.pending-faq-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}

.pending-faq-button small {
    font-size: 0.8rem;
    opacity: 0.9;
}

.pending-answer {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border-left: 4px solid #ffc107;
}

.pending-message {
    background: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    padding: 1rem;
    border-left: 3px solid #17a2b8;
    margin-top: 0.75rem;
    line-height: 1.6;
    color: #2d3748;
    font-style: italic;
}

/* 削除ボタンのスタイル - 大きくてわかりやすく */
.delete-faq-btn {
    border: 2px solid #dc3545;
    color: #dc3545;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
}

.delete-faq-btn:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.delete-faq-btn:focus {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

/* 削除アニメーション */
.accordion-item {
    transition: all 0.3s ease;
}

.accordion-item.deleting {
    opacity: 0;
    transform: translateX(-100%);
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .main-content-wrapper {
        padding-left: 0;
        padding-right: 0;
    }
    
    .main-content-styles {
        padding: 0 0.5rem; /* モバイルでのパディングを削減 */
        margin-top: 40px; /* マージンを削減 */
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 0.5rem; /* ギャップを削減 */
        text-align: center;
    }
    
    .accordion-button {
        padding: 0.75rem; /* パディングを削減 */
        font-size: 0.9rem;
    }
    
    .answer-content {
        padding: 0.75rem; /* パディングを削減 */
    }
}

@media (min-width: 769px) and (max-width: 1199px) {
    .main-content-wrapper {
        padding-left: 85px; /* タブレットサイズでの間隔をさらに調整 */
        padding-right: 10px;
    }
    
    .main-content-styles {
        padding: 0;
        margin-top: 50px; /* マージンを削減 */
    }
}

@media (min-width: 1200px) {
    .main-content-wrapper {
        padding-left: 90px; /* デスクトップでの間隔を調整 */
        padding-right: 10px;
    }
    
    .main-content-styles {
        padding: 0;
        margin-top: 60px; /* マージンを削減 */
    }
    
    .accordion-item {
        margin-bottom: 0; /* アイテム間のマージンを削除 */
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    
    .accordion-item:last-child {
        margin-bottom: 0;
    }
}

/* スクロール時のスムーズな動作 */
html {
    scroll-behavior: smooth;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('FAQ機能を初期化しています...');
    
    // 全てのアコーディオンを閉じる関数
    function closeAllAccordions() {
        document.querySelectorAll('.accordion-collapse').forEach(collapse => {
            collapse.classList.remove('show');
        });
        
        document.querySelectorAll('.accordion-button').forEach(btn => {
            btn.classList.add('collapsed');
            btn.setAttribute('aria-expanded', 'false');
        });
    }
    
    // シンプルなアコーディオン実装
    const accordionButtons = document.querySelectorAll('.accordion-button');
    accordionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = this.getAttribute('data-bs-target');
            const targetElement = document.querySelector(target);
            const accordionItem = this.closest('.accordion-item');
            
            // 現在開いているアコーディオンかどうかを判定
            const isCurrentlyOpen = targetElement.classList.contains('show');
            

    // 初期状態では全てのアコーディオンを閉じる
    closeAllAccordions();
            // 既に開いていた場合は閉じるだけ、そうでなければ開く
            if (!isCurrentlyOpen) {
                targetElement.classList.add('show');
                this.classList.remove('collapsed');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
    
    // 削除ボタンの処理
    console.log('削除機能を初期化しています...');
    const deleteButtons = document.querySelectorAll('.delete-faq-btn');
    console.log(`${deleteButtons.length}個の削除ボタンを検出しました`);
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const faqId = this.getAttribute('data-faq-id');
            const faqTitle = this.getAttribute('data-faq-title');
            
            console.log(`削除ボタンがクリックされました: ID=${faqId}, タイトル=${faqTitle}`);
            
            if (confirm(`「${faqTitle}」を削除してもよろしいですか？`)) {
                deleteFAQ(faqId, this);
            }
        });
    });
    
    // FAQ削除関数
    function deleteFAQ(faqId, buttonElement) {
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
            
            // レスポンスが正常でない場合はエラー
            if (!response.ok) {
                throw new Error(`サーバーエラー: ${response.status}`);
            }
            
            // テキストとして読み込んでからJSONに変換（エラー処理のため）
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
                // 成功したらUI更新
                alert('削除しました');
                
                // 要素をアニメーションで削除
                const accordionItem = buttonElement.closest('.accordion-item');
                if (accordionItem) {
                    accordionItem.style.transition = 'all 0.3s ease-out';
                    accordionItem.style.opacity = '0';
                    accordionItem.style.transform = 'translateX(-100%)';
                    
                    setTimeout(() => {
                        accordionItem.remove();
                        
                        // 質問が全て削除された場合はページを更新
                        const remainingItems = document.querySelectorAll('.user-faq-item, .pending-faq-item').length;
                        if (remainingItems === 0) {
                            window.location.reload();
                        }
                    }, 300);
                } else {
                    // 要素が見つからない場合はリロード
                    window.location.reload();
                }
            } else {
                // エラーメッセージを表示
                alert(`削除できませんでした: ${data.message || '不明なエラー'}`);
                
                // ボタンを元に戻す
                buttonElement.disabled = false;
                buttonElement.innerHTML = '<i class="fas fa-trash me-1"></i>削除';
            }
        })
        .catch(error => {
            console.error('エラーが発生しました:', error);
            alert(`削除処理に失敗しました: ${error.message}`);
            
            // ボタンを元に戻す
            buttonElement.disabled = false;
            buttonElement.innerHTML = '<i class="fas fa-trash me-1"></i>削除';
        });
    }
    // 初期状態では全てのアコーディオンを閉じる
    closeAllAccordions();
});
</script>