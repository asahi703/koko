<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

$current_user = get_login_user();
if (!$current_user) {
    header('Location: signin.php');
    exit;
}

$user_id = $current_user['user_id'] ?? $current_user['uuid'] ?? null;
$notifications = [];
$error = '';

if ($user_id) {
    try {
        $db = new cdb();
        
        // 通知を取得（未読を優先、日時順で並び替え）
        $stmt = $db->prepare('
            SELECT n.*, 
                   fu.user_name as from_user_name,
                   fu.user_icon as from_user_icon
            FROM notifications n
            LEFT JOIN users fu ON n.from_user_id = fu.user_id
            WHERE n.user_id = ?
            ORDER BY n.is_read ASC, n.created_at DESC
            LIMIT 50
        ');
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll();
        
        // 既読処理（GETパラメータで指定された通知を既読にする）
        if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
            $notification_id = $_GET['mark_read'];
            $update_stmt = $db->prepare('UPDATE notifications SET is_read = 1, read_at = NOW() WHERE notification_id = ? AND user_id = ?');
            $update_stmt->execute([$notification_id, $user_id]);
            header('Location: notification.php');
            exit;
        }
        
        // 全て既読にする処理
        if (isset($_POST['mark_all_read'])) {
            $update_all_stmt = $db->prepare('UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0');
            $update_all_stmt->execute([$user_id]);
            header('Location: notification.php');
            exit;
        }
        
    } catch (PDOException $e) {
        $error = '通知の取得に失敗しました。';
        error_log('Notification error: ' . $e->getMessage());
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>通知 - コミュニティプラットフォーム</title>
    <link rel="stylesheet" href="css/notification.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 mx-auto" style="margin-top: 100px; max-width: 900px;">
        
        <!-- ヘッダー -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-bell me-2"></i>通知
            </h2>
            <?php if (!empty($notifications)): ?>
            <form method="post" class="d-inline">
                <button type="submit" name="mark_all_read" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-check-all me-1"></i>すべて既読にする
                </button>
            </form>
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
                                        $icon_class = 'bi-question-circle';
                                        $icon_color = 'text-info';
                                        break;
                                    case 'faq_question':
                                        $icon_class = 'bi-question-circle-fill';
                                        $icon_color = 'text-warning';
                                        break;
                                    case 'class_invite':
                                        $icon_class = 'bi-calendar-event';
                                        $icon_color = 'text-warning';
                                        break;
                                    case 'system':
                                        $icon_class = 'bi-gear';
                                        $icon_color = 'text-secondary';
                                        break;
                                    default:
                                        $icon_class = 'bi-bell';
                                        $icon_color = 'text-muted';
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
                                        <a href="class_chat.php?id=<?php echo $notification['related_id']; ?>" 
                                           class="btn btn-sm btn-primary ms-2">
                                            <i class="bi bi-arrow-right me-1"></i>クラスチャットを見る
                                        </a>
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
    // 通知をクリックしたときの処理
    document.addEventListener('DOMContentLoaded', function() {
        const notificationItems = document.querySelectorAll('.notification-item.unread');
        
        notificationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // ボタンがクリックされた場合は無視
                if (e.target.closest('.btn')) return;
                
                const notificationId = this.dataset.notificationId;
                if (notificationId) {
                    window.location.href = `notification.php?mark_read=${notificationId}`;
                }
            });
        });
    });
</script>
