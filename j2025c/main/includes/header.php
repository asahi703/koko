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
            <a href="../main/notification.php" class="notification-link position-relative text-decoration-none">
                <i class="fas fa-bell fs-4 text-white"></i>
                <?php if ($unread_count > 0): ?>
                    <span class="notification-badge position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $unread_count > 99 ? '99+' : $unread_count; ?>
                    </span>
                <?php endif; ?>
            </a>
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
</html>