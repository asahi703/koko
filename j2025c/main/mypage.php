<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

$user = get_login_user();
if (!$user) {
    header('Location: index.php'); // ログインしていない場合はログインページへ
    exit;
}

$error = '';
$success = '';

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ユーザー情報更新処理
    $name = $_POST['name'] ?? '';
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($mail)) {
        $error = '名前とメールアドレスは必須です。';
    } else {
        try {
            $db = new cdb();
            $sql = 'UPDATE users SET user_name = ?, user_mailaddress = ?, user_login = ?';
            $params = [$name, $mail, $mail];

            if (!empty($password)) {
                if ($password !== $password_confirm) {
                    $error = 'パスワードが一致しません。';
                } else {
                    // パスワードも更新
                    $sql .= ', user_password = ?';
                    $params[] = sha1($password); // パスワードはハッシュ化
                }
            }

            if (empty($error)) {
                $sql .= ' WHERE user_id = ?';
                $params[] = $user['user_id'];

                $stmt = $db->prepare($sql);
                $stmt->execute($params);

                // セッション情報も更新
                $_SESSION['user']['name'] = $name;
                $_SESSION['user']['mail'] = $mail;
                
                $success = 'アカウント情報を更新しました。';
                // ユーザー情報を再取得して表示に反映
                $user = get_login_user();
            }
        } catch (PDOException $e) {
            // メールアドレスの重複エラーなど
            if ($e->getCode() == '23000') {
                 $error = 'このメールアドレスは既に使用されています。';
            } else {
                 $error = '更新に失敗しました。';
            }
        }
    }
}

// HTML出力
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>マイページ</title>
</head>
<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">マイページ</h3>
                <p class="card-text">アカウント情報を編集します。</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post" action="mypage.php" class="mt-4">
                    <div class="mb-3 text-start">
                        <label for="name" class="form-label">名前<span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">メールアドレス<span class="text-danger">*</span></label>
                        <input type="email" class="form-control shadow-sm" id="mail" name="mail" value="<?php echo htmlspecialchars($user['mail']); ?>" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password" class="form-label">新しいパスワード (変更する場合のみ)</label>
                        <input type="password" class="form-control shadow-sm" id="password" name="password">
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">新しいパスワード (確認)</label>
                        <input type="password" class="form-control shadow-sm" id="password_confirm" name="password_confirm">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary px-5 mt-3">更新</button>
                    </div>
                </form>
                
                <?php if (!empty($user['user_is_teacher'])): ?>
                <hr>
                <div class="mt-3">
                    <a href="create_teacher.php" class="btn btn-info">教師アカウント作成ページへ</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?php
// include 'includes/footer.php';
?>
