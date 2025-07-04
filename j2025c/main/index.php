<?php
require_once('common/config.php');
require_once('common/dbmanager.php');
require_once('common/session.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['user_mailaddress'] ?? '';
    $pass = $_POST['user_password'] ?? '';
    if ($mail && $pass) {
        try {
            $db = new cdb();
            $stmt = $db->prepare('SELECT * FROM users WHERE user_mailaddress = ?');
            $stmt->execute([$mail]);
            $user = $stmt->fetch();
            if ($user && $user['user_password'] === sha1($pass)) {
                // ログイン成功
                login_user([
                    'uuid' => $user['user_id'],
                    'name' => $user['user_name'],
                    'mail' => $user['user_mailaddress']
                ]);
                header('Location: home.php');
                exit;
            } else {
                $error = 'メールアドレスまたはパスワードが違います。';
            }
        } catch (Exception $e) {
            $error = 'ログイン処理でエラーが発生しました。';
        }
    } else {
        $error = 'メールアドレスとパスワードを入力してください。';
    }
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
    <link rel="stylesheet" href="css/Global.css">
</head>

<div class="main-content-wrapper">
    <div class="container d-flex flex-column align-items-center justify-content-center vw-100">
        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-md-3">
            <div class="mb-3">
                <h2>ログイン</h2>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            <form action="index.php" method="post" class="border rounded shadow p-4 w-100 login-form">
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
                <div class="form-group my-2 px-2 w-100">
                    <label>メールアドレス <span class="text-danger fw-bold">*</span></label>
                    <input type="email" name="user_mailaddress" class="form-control" placeholder="メールアドレス" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>パスワード <span class="text-danger fw-bold">*</span></label>
                    <input type="password" name="user_password" class="form-control" placeholder="パスワード" required>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <input type="submit" class="btn btn-primary w-100" value="ログイン">
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <a href="signin.php">新規登録はこちら</a>
                </div>
            </form>
            <button>
                <a href="home.php" class="btn btn-secondary mt-3 w-100">homepage(一時的)</a>
            </button>
        </div>
    </div>
</div>
