<?php
require_once(__DIR__ . '/../common/session.php');
require_once(__DIR__ . '/../common/dbmanager.php');
$user = get_login_user();

// 未読通知数を取得
$unread_count = 0;
if ($user && isset($user['user_id'])) {
    try {
        $db = new cdb();
        $stmt = $db->prepare('SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$user['user_id']]);
        $result = $stmt->fetch();
        $unread_count = $result['count'] ?? 0;
    } catch (PDOException $e) {
        // エラーログに記録するが、画面表示は継続
        error_log('Header notification count error: ' . $e->getMessage());
    }
}

// ログアウト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['logout'])) {
    logout_user();
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="../main/css/header.css">
    <link rel="stylesheet" href="../main/css/Global.css">
</head>

<!--PC時ヘッダー-->
<header class="d-none d-md-flex w-100 navbar navbar-expand-md align-items-center py-md-2 fixed-top shadow-sm">
    <nav class="container-fluid d-flex flex-row justify-content-between align-items-center">
        <!-- ブランドロゴとタイトル -->
        <a class="navbar-brand d-flex align-items-center me-auto ms-3" href="#">
            <img src="../main/img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid" alt="">
        </a>
        
        <!-- 通知アイコン -->
        <div class="d-flex align-items-center me-3">
            <div class="dropdown">
                <a href="#" class="notification-link position-relative text-decoration-none" 
                   id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell fs-4 text-white"></i>
                    <?php if ($unread_count > 0): ?>
                        <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
                        </span>
                    <?php endif; ?>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end notification-dropdown" 
                    aria-labelledby="notificationDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <span>通知</span>
                        <a href="../main/notification.php" class="text-decoration-none small">すべて見る</a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    
                    <div id="notificationList">
                        <!-- 通知一覧はここに動的に読み込まれます -->
                        <li class="text-center p-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">読み込み中...</span>
                            </div>
                        </li>
                    </div>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li class="text-center p-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                            すべて既読にする
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- ユーザーアイコン -->
        <a href="../main/mypage.php">
            <?php 
            // アイコン表示ロジック
            $icon_src = '../main/img/headerImg/account.png'; // デフォルトアイコン
            
            if (!empty($user['user_icon'])) {
                $user_icon_path = $user['user_icon'];
                
                // 相対パスの場合は ../ を追加
                if (strpos($user_icon_path, 'img/user_icons/') === 0) {
                    $icon_src = '../' . $user_icon_path;
                } else {
                    // ファイル名のみの場合
                    $icon_src = '../img/user_icons/' . $user_icon_path;
                }
            }
            ?>
            <img src="<?php echo htmlspecialchars($icon_src); ?>"
                 style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #667eea;"
                 class="hd-img d-inline-block align-top img-fluid ms-2" alt="プロフィールアイコン"
                 onerror="this.src='../main/img/headerImg/account.png';">
        </a>
        <!-- ユーザー情報表示 -->
        <?php if ($user): ?>
            <div class="ms-4 d-flex align-items-center">
                <span class="me-2 fw-bold"><?php echo htmlspecialchars($user['user_name'] ?? ''); ?></span>
                <span class="text-secondary small"><?php echo htmlspecialchars($user['user_mailaddress'] ?? ''); ?></span>
                <form method="post" style="display: inline;">
                    <button type="submit" name="logout" class="btn btn-outline-secondary btn-sm ms-3">ログアウト</button>
                </form>
            </div>
        <?php else: ?>
            <div class="ms-4">
                <span class="text-secondary small">未ログイン</span>
            </div>
        <?php endif; ?>
    </nav>
</header>

<!-- 通知用JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 通知ドロップダウンが開かれた時に通知を読み込む
    const notificationDropdown = document.getElementById('notificationDropdown');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('shown.bs.dropdown', function() {
            loadNotifications();
        });
    }
});

// 通知一覧を読み込む
function loadNotifications() {
    fetch('../main/api/get_notifications.php')
        .then(response => response.json())
        .then(data => {
            const listContainer = document.getElementById('notificationList');
            
            if (data.success && data.notifications && data.notifications.length > 0) {
                let html = '';
                data.notifications.forEach(notification => {
                    const timeAgo = formatTimeAgo(notification.created_at);
                    const isUnread = notification.is_read == '0';
                    
                    html += `
                        <li class="notification-item ${isUnread ? 'bg-light' : ''}" 
                            onclick="markAsRead(${notification.notification_id}, '${notification.related_id || ''}', '${notification.notification_type}', '${notification.from_user_id || notification.from_user_id_actual || ''}', '${notification.from_user_name || ''}')"
                            style="cursor: pointer;">
                            <div class="d-flex align-items-start p-3 border-bottom">
                                <div class="me-2">
                                    ${getNotificationIcon(notification.notification_type)}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 ${isUnread ? 'fw-bold' : ''}">${notification.title}</h6>
                                    <p class="mb-1 small text-muted">${notification.message}</p>
                                    <small class="text-muted">${timeAgo}</small>
                                </div>
                                ${isUnread ? '<div class="ms-2"><span class="badge bg-primary">新着</span></div>' : ''}
                            </div>
                        </li>
                    `;
                });
                listContainer.innerHTML = html;
            } else {
                listContainer.innerHTML = `
                    <li class="text-center p-3 text-muted">
                        通知はありません
                    </li>
                `;
            }
        })
        .catch(error => {
            console.error('通知読み込みエラー:', error);
            document.getElementById('notificationList').innerHTML = `
                <li class="text-center p-3 text-danger">
                    通知の読み込みに失敗しました
                </li>
            `;
        });
}

// 通知アイコンを取得
function getNotificationIcon(type) {
    switch(type) {
        case 'group_chat_message':
            return '<i class="fas fa-users text-primary" title="グループチャット"></i>';
        case 'class_chat_message':
            return '<i class="fas fa-chalkboard-teacher text-success" title="コミュニティチャット"></i>';
        case 'direct_message':
            return '<i class="fas fa-comment text-info" title="個人メッセージ"></i>';
        default:
            return '<i class="fas fa-bell text-secondary"></i>';
    }
}

// 時間の経過を表示
function formatTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return 'たった今';
    if (diff < 3600) return Math.floor(diff / 60) + '分前';
    if (diff < 86400) return Math.floor(diff / 3600) + '時間前';
    if (diff < 604800) return Math.floor(diff / 86400) + '日前';
    
    return date.toLocaleDateString('ja-JP');
}

// 通知を既読にする
function markAsRead(notificationId, relatedId, type, fromUserId, fromUserName) {
    fetch('../main/api/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            notification_id: notificationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 通知数バッジを更新
            updateNotificationBadge();
            
            // 関連ページにリダイレクト
            if (type) {
                redirectToRelatedPage(type, relatedId, fromUserId, fromUserName);
            }
        }
    })
    .catch(error => console.error('既読マークエラー:', error));
}

// すべて既読にする
function markAllAsRead() {
    fetch('../main/api/mark_all_notifications_read.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateNotificationBadge();
            loadNotifications(); // 通知一覧を再読み込み
        }
    })
    .catch(error => console.error('全既読マークエラー:', error));
}

// 通知バッジを更新
function updateNotificationBadge() {
    fetch('../main/api/get_unread_count.php')
        .then(response => response.json())
        .then(data => {
            const badge = document.querySelector('.notification-badge');
            if (data.count > 0) {
                if (badge) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                } else {
                    // バッジを新規作成
                    const bellIcon = document.querySelector('.notification-link');
                    const newBadge = document.createElement('span');
                    newBadge.className = 'notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                    newBadge.textContent = data.count > 99 ? '99+' : data.count;
                    bellIcon.appendChild(newBadge);
                }
            } else if (badge) {
                badge.remove();
            }
        })
        .catch(error => console.error('バッジ更新エラー:', error));
}

// 関連ページにリダイレクト
function redirectToRelatedPage(type, relatedId, fromUserId, fromUserName) {
    switch(type) {
        case 'group_chat_message':
            // グループチャットの場合
            if (relatedId) {
                window.location.href = `../main/chat.php?group_id=${relatedId}`;
            } else {
                window.location.href = '../main/chat.php';
            }
            break;
        case 'class_chat_message':
            // クラス・コミュニティチャットの場合
            if (relatedId) {
                window.location.href = `../main/class_chat.php?id=${relatedId}`;
            } else {
                window.location.href = '../main/class_chat.php';
            }
            break;
        case 'direct_message':
            // 個人メッセージの場合は送信者とのチャットページに移動
            if (fromUserId) {
                // 送信者IDと名前を使ってチャットページに移動
                const params = new URLSearchParams();
                params.set('user', fromUserId);
                if (fromUserName) {
                    params.set('name', fromUserName);
                }
                window.location.href = `../main/chat.php?${params.toString()}`;
            } else {
                // フォールバック
                window.location.href = '../main/chat.php';
            }
            break;
        default:
            // その他の通知は通知ページへ
            window.location.href = '../main/notification.php';
    }
}
</script>

</html>