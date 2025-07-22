<?php
/**
 * 通知システムテスト用ファイル
 * このファイルを実行して通知機能をテストします
 */

require_once('common/dbmanager.php');
require_once('common/session.php');
require_once('common/notification_helper.php');

// ログインユーザー取得
$user = get_login_user();
if (!$user) {
    echo "ログインしてください。";
    exit;
}

// テスト通知作成
$test_created = false;
if (isset($_POST['create_test'])) {
    $result = create_notification(
        $user['user_id'], // 受信者（自分）
        null, // 送信者なし
        'system', // システム通知
        'テスト通知',
        'これはテスト通知です。通知システムが正常に動作しています。',
        null // 関連IDなし
    );
    
    if ($result) {
        $test_created = true;
    }
}

// 現在の通知を取得
try {
    $db = new cdb();
    $stmt = $db->prepare('
        SELECT n.*, fu.user_name as from_user_name 
        FROM notifications n 
        LEFT JOIN users fu ON n.from_user_id = fu.user_id 
        WHERE n.user_id = ? 
        ORDER BY n.created_at DESC 
        LIMIT 10
    ');
    $stmt->execute([$user['user_id']]);
    $notifications = $stmt->fetchAll();
} catch (PDOException $e) {
    $notifications = [];
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>通知システムテスト</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>通知システムテスト</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>テスト通知作成</h5>
                </div>
                <div class="card-body">
                    <?php if ($test_created): ?>
                        <div class="alert alert-success">
                            テスト通知を作成しました！
                        </div>
                    <?php endif; ?>
                    
                    <form method="post">
                        <button type="submit" name="create_test" class="btn btn-primary">
                            テスト通知を作成
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>現在のユーザー情報</h5>
                </div>
                <div class="card-body">
                    <p><strong>ユーザーID:</strong> <?php echo htmlspecialchars($user['user_id']); ?></p>
                    <p><strong>ユーザー名:</strong> <?php echo htmlspecialchars($user['user_name'] ?? 'N/A'); ?></p>
                    <p><strong>教師フラグ:</strong> <?php echo $user['user_is_teacher'] ? '教師' : '生徒'; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <div class="card">
            <div class="card-header">
                <h5>最新の通知 (最大10件)</h5>
            </div>
            <div class="card-body">
                <?php if (isset($db_error)): ?>
                    <div class="alert alert-danger">
                        データベースエラー: <?php echo htmlspecialchars($db_error); ?>
                    </div>
                <?php elseif (empty($notifications)): ?>
                    <p class="text-muted">通知はありません。</p>
                <?php else: ?>
                    <div class="list-group">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="list-group-item <?php echo $notification['is_read'] ? '' : 'list-group-item-primary'; ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                    <small><?php echo $notification['created_at']; ?></small>
                                </div>
                                <p class="mb-1"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <small>
                                    <span class="badge bg-secondary"><?php echo $notification['notification_type']; ?></span>
                                    <?php if (!$notification['is_read']): ?>
                                        <span class="badge bg-primary">未読</span>
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="mt-3">
        <a href="notification.php" class="btn btn-success">通知画面へ</a>
        <a href="community.php" class="btn btn-secondary">メインページへ</a>
    </div>
</div>
</body>
</html>
