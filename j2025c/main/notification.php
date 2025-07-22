<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

$user = get_login_user();
if (!$user) {
    header('Location: index.php');
    exit;
}

$error = '';
$notifications = [];

// 既読にする処理
if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    try {
        $db = new cdb();
        $stmt = $db->prepare('UPDATE notifications SET is_read = 1 WHERE notification_id = ? AND user_id = ?');
        $stmt->execute([$_GET['mark_read'], $user['user_id']]);
        header('Location: notification.php');
        exit;
    } catch (PDOException $e) {
        $error = '既読処理でエラーが発生しました。';
    }
}

// 通知データを取得
try {
    $db = new cdb();
    $stmt = $db->prepare('
        SELECT n.*, 
               fu.user_name as from_user_name, 
               fu.user_icon as from_user_icon
        FROM notifications n
        LEFT JOIN users fu ON n.from_user_id = fu.user_id
        WHERE n.user_id = ?
        ORDER BY n.created_at DESC
        LIMIT 50
    ');
    $stmt->execute([$user['user_id']]);
    $notifications = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = '通知の取得でエラーが発生しました。';
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通知 - J2025C</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/notification.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="main-content-wrapper">
    <main class="container-fluid py-4">
        <!-- ページヘッダー -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h2 mb-0">
                <i class="bi bi-bell me-2"></i>通知
            </h1>
            <?php if (!empty($notifications)): ?>
                <small class="text-muted">
                    <?php
                    $unread_count = count(array_filter($notifications, function($n) { return !$n['is_read']; }));
                    echo $unread_count > 0 ? "未読: {$unread_count}件" : "すべて既読";
                    ?>
                </small>
            <?php endif; ?>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif (empty($notifications)): ?>
            <div class="empty-state text-center py-5">
                <i class="bi bi-bell-slash display-1 text-muted mb-3"></i>
                <h4 class="text-muted">通知はありません</h4>
                <p class="text-muted">新しい通知が届くとここに表示されます。</p>
            </div>
        <?php else: ?>
            
            <!-- 通知リスト -->
            <div class="notification-list">
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
                         data-notification-id="<?php echo $notification['notification_id']; ?>"
                         data-type="<?php echo $notification['notification_type']; ?>">
                        
                        <div class="d-flex align-items-start">
                            <!-- アイコン -->
                            <div class="notification-icon me-3">
                                <?php
                                $icon_class = '';
                                $icon_color = '';
                                switch ($notification['notification_type']) {
                                    case 'chat_message':
                                        $icon_class = 'bi-chat-dots';
                                        $icon_color = 'text-primary';
                                        break;
                                    case 'community_join':
                                        $icon_class = 'bi-people';
                                        $icon_color = 'text-success';
                                        break;
                                    case 'community_create':
                                        $icon_class = 'bi-people-fill';
                                        $icon_color = 'text-success';
                                        break;
                                    case 'faq_answer':
                                        $icon_class = 'bi-question-circle-fill';
                                        $icon_color = 'text-info';
                                        break;
                                    case 'faq_question':
                                        $icon_class = 'bi-patch-question';
                                        $icon_color = 'text-warning';
                                        break;
                                    case 'class_invite':
                                        $icon_class = 'bi-envelope';
                                        $icon_color = 'text-secondary';
                                        break;
                                    default:
                                        $icon_class = 'bi-info-circle';
                                        $icon_color = 'text-dark';
                                }
                                ?>
                                <i class="bi <?php echo $icon_class; ?> <?php echo $icon_color; ?> fs-4"></i>
                            </div>
                            
                            <!-- 内容 -->
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="notification-header">
                                        <h6 class="notification-title mb-1">
                                            <?php echo htmlspecialchars($notification['title']); ?>
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge bg-primary ms-2">新着</span>
                                            <?php endif; ?>
                                        </h6>
                                        <!-- 通知カテゴリーバッジ -->
                                        <?php
                                        $category_badge = '';
                                        $badge_class = '';
                                        switch ($notification['notification_type']) {
                                            case 'chat_message':
                                                $category_badge = 'クラスチャット';
                                                $badge_class = 'bg-primary';
                                                break;
                                            case 'community_join':
                                                $category_badge = 'コミュニティ';
                                                $badge_class = 'bg-success';
                                                break;
                                            case 'community_create':
                                                $category_badge = 'コミュニティ';
                                                $badge_class = 'bg-success';
                                                break;
                                            case 'faq_answer':
                                                $category_badge = 'FAQ回答';
                                                $badge_class = 'bg-info';
                                                break;
                                            case 'faq_question':
                                                $category_badge = 'FAQ質問';
                                                $badge_class = 'bg-warning';
                                                break;
                                            case 'class_invite':
                                                $category_badge = 'クラス招待';
                                                $badge_class = 'bg-secondary';
                                                break;
                                            case 'system':
                                                $category_badge = 'システム';
                                                $badge_class = 'bg-dark';
                                                break;
                                        }
                                        ?>
                                        <?php if ($category_badge): ?>
                                            <span class="badge <?php echo $badge_class; ?> category-badge"><?php echo $category_badge; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted notification-time">
                                        <?php
                                        $created_time = strtotime($notification['created_at']);
                                        $time_diff = time() - $created_time;
                                        
                                        if ($time_diff < 3600) {
                                            echo floor($time_diff / 60) . '分前';
                                        } elseif ($time_diff < 86400) {
                                            echo floor($time_diff / 3600) . '時間前';
                                        } elseif ($time_diff < 604800) {
                                            echo floor($time_diff / 86400) . '日前';
                                        } else {
                                            echo date('m/d', $created_time);
                                        }
                                        ?>
                                    </small>
                                </div>
                                
                                <p class="notification-message mb-2">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </p>
                                
                                <?php if ($notification['from_user_name']): ?>
                                    <div class="notification-from d-flex align-items-center">
                                        <img src="<?php 
                                            if (!empty($notification['from_user_icon'])) {
                                                if (strpos($notification['from_user_icon'], 'img/user_icons/') === 0) {
                                                    echo '../' . $notification['from_user_icon'];
                                                } else {
                                                    echo '../img/user_icons/' . $notification['from_user_icon'];
                                                }
                                            } else {
                                                echo '../main/img/headerImg/account.png';
                                            }
                                        ?>" 
                                             alt="ユーザー画像" 
                                             class="user-icon me-2"
                                             onerror="this.src='../main/img/headerImg/account.png';">
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($notification['from_user_name']); ?>さんより
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- アクションボタン -->
                                <div class="notification-actions mt-2">
                                    <?php if (!$notification['is_read']): ?>
                                        <a href="notification.php?mark_read=<?php echo $notification['notification_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-check me-1"></i>既読にする
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($notification['related_id'] && $notification['notification_type'] === 'chat_message'): ?>
                                        <?php 
                                        // クラスチャットかグループチャットかを判定
                                        $db_check = new cdb();
                                        $class_check = $db_check->prepare('SELECT class_id FROM classes WHERE class_id = ?');
                                        $class_check->execute([$notification['related_id']]);
                                        $is_class_chat = $class_check->fetch();
                                        ?>
                                        <?php if ($is_class_chat): ?>
                                            <a href="class_chat.php?id=<?php echo $notification['related_id']; ?>" 
                                               class="btn btn-sm btn-primary ms-2">
                                                <i class="bi bi-arrow-right me-1"></i>クラスチャットを見る
                                            </a>
                                        <?php else: ?>
                                            <a href="chat.php?group_id=<?php echo $notification['related_id']; ?>" 
                                               class="btn btn-sm btn-primary ms-2">
                                                <i class="bi bi-chat-dots me-1"></i>グループチャットを見る
                                            </a>
                                        <?php endif; ?>
                                    <?php elseif ($notification['related_id'] && ($notification['notification_type'] === 'community_join' || $notification['notification_type'] === 'community_create')): ?>
                                        <a href="community.php" 
                                           class="btn btn-sm btn-success ms-2">
                                            <i class="bi bi-people me-1"></i>コミュニティを見る
                                        </a>
                                    <?php elseif ($notification['related_id'] && $notification['notification_type'] === 'faq_answer'): ?>
                                        <a href="faq.php" 
                                           class="btn btn-sm btn-info ms-2">
                                            <i class="bi bi-question-circle me-1"></i>FAQを見る
                                        </a>
                                    <?php elseif ($notification['related_id'] && $notification['notification_type'] === 'faq_question'): ?>
                                        <a href="teacher_questions.php" 
                                           class="btn btn-sm btn-warning ms-2">
                                            <i class="bi bi-person-raised-hand me-1"></i>質問に回答する
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
        
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// 通知アイテムクリックで自動的に既読にする
document.querySelectorAll('.notification-item.unread').forEach(item => {
    item.addEventListener('click', function(e) {
        // ボタンクリックの場合は処理しない
        if (e.target.closest('.notification-actions')) return;
        
        const notificationId = this.dataset.notificationId;
        // 既読処理（非同期）
        fetch(`notification.php?mark_read=${notificationId}`)
            .then(() => {
                this.classList.remove('unread');
                this.classList.add('read');
                const badge = this.querySelector('.badge');
                if (badge && badge.textContent === '新着') {
                    badge.remove();
                }
            })
            .catch(err => console.error('既読処理エラー:', err));
    });
});
</script>
</body>
</html>
