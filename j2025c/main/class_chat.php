<?php
require_once('common/dbmanager.php');
require_once('common/session.php');
require_once('common/notification_helper.php');

$user = get_login_user();
$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user || !$class_id) {
    header('Location: community.php');
    exit;
}

$db = new cdb();

// クラス情報取得
$stmt = $db->prepare('
    SELECT c.*, com.community_name, com.community_id 
    FROM classes c 
    JOIN communities com ON c.class_community = com.community_id 
    WHERE c.class_id = ?
');
$stmt->execute([$class_id]);
$class = $stmt->fetch();

if (!$class) {
    header('Location: community.php');
    exit;
}

// メッセージ送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        try {
            $stmt = $db->prepare('
                INSERT INTO class_chats (class_id, user_id, message) 
                VALUES (?, ?, ?)
            ');
            $stmt->execute([$class_id, $user['uuid'], $message]);
            
            // チャットメッセージ通知を送信
            $sender_name = $user['user_name'] ?? $user['name'] ?? 'ユーザー';
            notify_chat_message($class_id, $user['uuid'], $sender_name, $message);
            
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } catch (PDOException $e) {
            $error = 'メッセージの送信に失敗しました。';
        }
    }
}

// チャットメッセージ取得
$stmt = $db->prepare('
    SELECT ch.*, u.user_name, u.user_icon 
    FROM class_chats ch 
    JOIN users u ON ch.user_id = u.user_id 
    WHERE ch.class_id = ? 
    ORDER BY ch.created_at DESC 
    LIMIT 100
');
$stmt->execute([$class_id]);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($class['class_name']); ?> - チャット</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/chat.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content-wrapper">
    <main class="container-fluid py-4 d-flex flex-column" style="height: calc(100vh - 56px);">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h2 class="mb-0"><?php echo htmlspecialchars($class['class_name']); ?></h2>
                <small class="text-muted">
                    <a href="class_select.php?id=<?php echo $class['community_id']; ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($class['community_name']); ?>
                    </a>
                </small>
                <a href="class_calender.php?id=<?php echo $class_id; ?>" class="btn btn-outline-primary btn-sm ms-3">行事予定表</a>
            </div>
        </div>

        <!-- チャットメッセージ表示エリア -->
        <div class="chat-messages bg-light rounded p-3 mb-4 flex-grow-1" style="overflow-y: auto;">
            <?php foreach (array_reverse($messages) as $message): ?>
                <div class="card mb-3">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex align-items-center">
                            <?php if (!empty($message['user_icon'])): ?>
                                <img src="../<?php echo htmlspecialchars($message['user_icon']); ?>"
                                     class="rounded-circle me-2" style="width: 32px; height: 32px;" alt="">
                            <?php else: ?>
                                <img src="../main/img/headerImg/account.png"
                                     class="rounded-circle me-2" style="width: 32px; height: 32px;" alt="">
                            <?php endif; ?>
                            <span class="fw-bold"><?php echo htmlspecialchars($message['user_name']); ?></span>
                        </div>
                        <small class="text-muted">
                            <?php echo date('Y/m/d H:i', strtotime($message['created_at'])); ?>
                        </small>
                    </div>
                    <div class="card-body">
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- メッセージ入力フォーム -->
        <div class="mt-auto bg-white border-top py-3">
            <form method="post" class="d-flex align-items-center">
                <textarea class="form-control me-2" name="message" rows="1"
                          placeholder="メッセージを入力" required></textarea>
                <button type="submit" class="btn btn-primary">送信</button>
            </form>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // チャットを最新のメッセージまでスクロール
    window.onload = function() {
        const chatMessages = document.querySelector('.chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    };
</script>
</body>
</html>
</body>
</html>

