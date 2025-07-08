<?php
require_once(__DIR__ . '/../common/session.php');
$user = get_login_user();

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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
            crossorigin="anonymous"></script>
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
            <img src="../main/img/headerImg/logo.png" style="width: 50px" class="hd-img d-inline-block align-top img-fluid"
                 alt="">
            <img src="../main/img/headerImg/account.png" style="width: 50px"
                 class="hd-img d-inline-block align-top img-fluid ms-4" alt="">
        </a>
        <!-- ユーザー情報表示 -->
        <?php if ($user): ?>
            <div class="ms-4 d-flex align-items-center">
                <span class="me-2 fw-bold"><?php echo htmlspecialchars($user['name']); ?></span>
                <span class="text-secondary small"><?php echo htmlspecialchars($user['mail']); ?></span>
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