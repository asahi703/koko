<?php
require_once('common/session.php');
require_once('common/dbmanager.php');
require_once('common/notification_helper.php');

// 教師権限チェック
$user = get_login_user();
if (!$user || $user['user_is_teacher'] != 1) {
    header('Location: index.php');
    exit;
}

$success = '';
$error = '';

// 返信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_submit'])) {
    $question_id = $_POST['question_id'] ?? 0;
    $reply_text = $_POST['reply_text'] ?? '';
    
    if (empty($reply_text)) {
        $error = '返信内容を入力してください。';
    } else {
        try {
            $db = new cdb();
            
            // 質問者の情報を取得
            $question_stmt = $db->prepare('SELECT faq_user_id, faq_title FROM faq WHERE faq_id = ?');
            $question_stmt->execute([$question_id]);
            $question_data = $question_stmt->fetch();
            
            if ($question_data) {
                $stmt = $db->prepare('UPDATE faq SET 
                    faq_answer = ?,
                    faq_answered_at = NOW(),
                    faq_answered_by = ?
                    WHERE faq_id = ?');
                $stmt->execute([$reply_text, $user['user_id'], $question_id]);
                
                // FAQ回答通知を送信
                $responder_name = $user['user_name'] ?? $user['name'] ?? '先生';
                notify_faq_answer(
                    $question_id, 
                    $question_data['faq_user_id'], 
                    $user['user_id'], 
                    $responder_name, 
                    $question_data['faq_title']
                );
                
                $success = '返信を送信しました。';
            } else {
                $error = '質問が見つかりません。';
            }
        } catch (PDOException $e) {
            $error = '返信の送信に失敗しました: ' . $e->getMessage();
            error_log('返信送信エラー: ' . $e->getMessage());
        }
    }
}

// 質問一覧を取得
try {
    $db = new cdb();
    // テーブルが存在するか確認
    $check_table = $db->prepare("SHOW TABLES LIKE 'faq'");
    $check_table->execute();
    
    if ($check_table->rowCount() == 0) {
        throw new Exception("質問テーブル(faq)が存在しません。テーブルを作成してください。");
    }
    
    // faqテーブルを使用
    $stmt = $db->prepare('SELECT f.*, u.user_name, u.user_icon FROM faq f
                         LEFT JOIN users u ON f.faq_user_id = u.user_id
                         ORDER BY f.faq_answer IS NULL DESC, f.faq_created_at DESC');
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = '質問データの取得に失敗しました: ' . $e->getMessage();
    error_log('質問データ取得エラー: ' . $e->getMessage());
    $questions = [];
}

// HTML出力
include 'includes/header.php';
?>

<head>
    <title>質問管理 - 教師用</title>
    <style>
        .question-card {
            transition: transform 0.2s;
            margin-bottom: 1rem;
        }
        .question-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .unanswered {
            border-left: 5px solid #dc3545;
        }
        .answered {
            border-left: 5px solid #28a745;
        }
        .question-text {
            white-space: pre-line;
        }
    </style>
</head>

<div class="container mt-5 pt-5">
    <h2 class="mb-4">質問管理 <small class="text-muted">- 教師用</small></h2>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>質問状況</h5>
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <h3 class="text-primary"><?php echo count($questions); ?></h3>
                            <p class="mb-0">全質問</p>
                        </div>
                        <div>
                            <h3 class="text-danger"><?php 
                                echo count(array_filter($questions, function($q) { 
                                    return empty($q['faq_answer']); 
                                })); 
                            ?></h3>
                            <p class="mb-0">未回答</p>
                        </div>
                        <div>
                            <h3 class="text-success"><?php 
                                echo count(array_filter($questions, function($q) { 
                                    return !empty($q['faq_answer']); 
                                })); 
                            ?></h3>
                            <p class="mb-0">回答済み</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php if (empty($questions)): ?>
            <div class="col-12">
                <div class="alert alert-info">質問はまだありません。</div>
            </div>
        <?php else: ?>
            <?php foreach ($questions as $question): ?>
                <div class="col-12">
                    <div class="card question-card <?php echo empty($question['faq_answer']) ? 'unanswered' : 'answered'; ?> shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <img src="<?php echo !empty($question['user_icon']) ? '../' . htmlspecialchars($question['user_icon']) : '../main/img/headerImg/account.png'; ?>" 
                                    class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <h5 class="card-title mb-0"><?php echo htmlspecialchars($question['user_name'] ?? '匿名'); ?></h5>
                                    <small class="text-muted"><?php echo htmlspecialchars($question['faq_created_at']); ?></small>
                                </div>
                                <div class="ms-auto">
                                    <?php if (empty($question['faq_answer'])): ?>
                                        <span class="badge bg-danger">未回答</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">回答済み</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <h5><?php echo htmlspecialchars($question['faq_title'] ?? 'タイトルなし'); ?></h5>
                            <p class="question-text"><?php echo nl2br(htmlspecialchars($question['faq_question'])); ?></p>
                            
                            <?php if (!empty($question['faq_answer'])): ?>
                                <div class="card bg-light mt-3">
                                    <div class="card-body">
                                        <h6 class="card-subtitle mb-2 text-muted">あなたの回答:</h6>
                                        <p class="question-text mb-0"><?php echo nl2br(htmlspecialchars($question['faq_answer'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="text-end mt-3">
                                <button type="button" class="btn <?php echo empty($question['faq_answer']) ? 'btn-primary' : 'btn-outline-secondary'; ?>" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#replyModal<?php echo $question['faq_id']; ?>">
                                    <?php echo empty($question['faq_answer']) ? '回答する' : '回答を編集'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 返信モーダル -->
                <div class="modal fade" id="replyModal<?php echo $question['faq_id']; ?>" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="replyModalLabel">
                                    <?php echo empty($question['faq_answer']) ? '質問に回答する' : '回答を編集'; ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="post">
                                <div class="modal-body">
                                    <input type="hidden" name="question_id" value="<?php echo $question['faq_id']; ?>">
                                    
                                    <div class="mb-3">
                                        <label class="form-label">質問:</label>
                                        <div class="card">
                                            <div class="card-body">
                                                <h5><?php echo htmlspecialchars($question['faq_title'] ?? 'タイトルなし'); ?></h5>
                                                <p class="question-text"><?php echo nl2br(htmlspecialchars($question['faq_question'])); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="reply_text" class="form-label">回答:</label>
                                        <textarea class="form-control" name="reply_text" id="reply_text" rows="6" required><?php echo htmlspecialchars($question['faq_answer'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                                    <button type="submit" name="reply_submit" class="btn btn-primary">送信する</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php // include 'includes/footer.php'; ?>
