<?php
require_once('common/session.php');
require_once('common/dbmanager.php');
require_once('common/notification_helper.php');

$user = get_login_user();
if (!$user) {
    header('Location: signin.php');
    exit;
}

$message = '';
$error = '';

// フォーム送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $question = trim($_POST['question'] ?? '');
    
    if (empty($title)) {
        $error = 'タイトルを入力してください。';
    } elseif (empty($question)) {
        $error = '質問内容を入力してください。';
    } else {
        try {
            // ユーザー情報のデバッグ
            error_log('FAQ Create - User Info: ' . print_r($user, true));
            error_log('FAQ Create - User ID: ' . ($user['uuid'] ?? 'NULL'));
            
            // user_idが存在しない場合のエラーハンドリング
            if (!isset($user['uuid']) || empty($user['uuid'])) {
                $error = 'ユーザー情報が正しく取得できませんでした。再度ログインしてください。';
                error_log('FAQ Create Error: User UUID is missing or empty');
            } else {
                $db = new cdb();
                $stmt = $db->prepare('
                    INSERT INTO faq (faq_title, faq_question, faq_user_id, faq_created_at) 
                    VALUES (?, ?, ?, NOW())
                ');
                
                if ($stmt->execute([$title, $question, $user['uuid']])) {
                    $faq_id = $db->lastInsertId();
                    
                    // 教師への質問通知を送信
                    $questioner_name = $user['user_name'] ?? $user['name'] ?? 'ユーザー';
                    notify_faq_question($faq_id, $user['uuid'], $questioner_name, $title);
                    
                    $message = '質問を投稿しました。回答をお待ちください。';
                    $title = ''; // フォームをリセット
                    $question = ''; // フォームをリセット
                    error_log('FAQ Created successfully with user_id: ' . $user['uuid']);
                } else {
                    $error = '質問の投稿に失敗しました。もう一度お試しください。';
                    // デバッグ情報
                    error_log('FAQ Insert Error: ' . print_r($stmt->errorInfo(), true));
                }
            }
        } catch (Exception $e) {
            $error = '質問の投稿中にエラーが発生しました。';
            // デバッグ情報
            error_log('FAQ Exception: ' . $e->getMessage());
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 40px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">質問は何ですか？</h2>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success mx-auto" style="max-width: 800px;" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                <div class="mt-2">
                    <a href="faq.php" class="btn btn-sm btn-outline-success">FAQページで確認</a>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger mx-auto" style="max-width: 800px;" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="title" class="form-label">タイトル<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" 
                               placeholder="質問のタイトルを入力してください..." maxlength="100" required 
                               value="<?php echo htmlspecialchars($title ?? ''); ?>">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            質問内容を簡潔に表すタイトルをつけてください（最大100文字）
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="question" class="form-label">質問内容<span class="text-danger">*</span></label>
                        <textarea class="form-control" id="question" name="question" rows="6" 
                                  placeholder="ここに質問を入力してください..." required><?php echo htmlspecialchars($question ?? ''); ?></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            できるだけ具体的に質問内容を記載してください。回答が早くなります。
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="fas fa-paper-plane me-2"></i>質問を送信
                        </button>
                        <a href="faq.php" class="btn btn-outline-secondary px-5 ms-3">
                            <i class="fas fa-arrow-left me-2"></i>戻る
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
