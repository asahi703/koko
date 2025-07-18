<?php
require_once('common/config.php');
require_once('common/dbmanager.php');
require_once('common/notification_helper.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = new cdb();

        // パスワードをSHA-1でハッシュ化
        $hashed_password = sha1($_POST['user_password']);

        // ユーザー情報をデータベースに登録
        $stmt = $db->prepare('INSERT INTO users (user_name, user_mailaddress, user_password, user_login) VALUES (?, ?, ?, ?)');
        $stmt->execute([
            $_POST['user_name'],
            $_POST['user_mailaddress'],
            $hashed_password,
            $_POST['user_mailaddress'] // user_loginにはメールアドレスを使用
        ]);

        // 新規登録されたユーザーIDを取得
        $new_user_id = $db->lastInsertId();
        
        // ウェルカム通知を送信
        notify_welcome($new_user_id, $_POST['user_name']);

        // 登録成功時はログインページにリダイレクト
        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        $error = 'ユーザー登録に失敗しました。';
        if ($e->getCode() == 23000) { // 重複エラーの場合
            $error = 'このメールアドレスは既に登録されています。';
        }
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
                <h2>新規登録</h2>
            </div>
            <form action="signin.php" method="post" class="border rounded shadow p-4 w-100 login-form">
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
                <div class="form-group my-2 px-2 w-100">
                    <label>ユーザーネーム <span class="text-danger fw-bold">*</span></label>
                    <input type="text" name="user_name" class="form-control" placeholder="ユーザーネーム" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>メールアドレス <span class="text-danger fw-bold">*</span></label>
                    <input type="email" name="user_mailaddress" class="form-control" placeholder="メールアドレス" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>パスワード <span class="text-danger fw-bold">*</span></label>
                    <input type="password" name="user_password" class="form-control" placeholder="パスワード" required>
                </div>
                <div class="text-center">
                    <p>利用規約は <a href="">こちらから</a></p>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <input type="submit" class="btn btn-primary w-100" value="新規登録">
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <a href="index.php">ログインはこちら</a>
                </div>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
