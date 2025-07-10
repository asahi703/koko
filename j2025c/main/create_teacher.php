<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

$user = get_login_user();
// ログインしていない、または教師でない場合はコミュニティ選択ページにリダイレクト
if (!$user || empty($user['user_is_teacher'])) {
    header('Location: community.php');
    exit;
}

$error = '';
$success = '';

// POST処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $mail = $_POST['mail'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name) || empty($mail) || empty($password)) {
        $error = 'すべての必須項目を入力してください。';
    } elseif ($password !== $password_confirm) {
        $error = 'パスワードが一致しません。';
    } else {
        try {
            $db = new cdb();
            $sql = 'INSERT INTO users (user_name, user_mailaddress, user_password, user_login, user_is_teacher) VALUES (?, ?, ?, ?, 1)';
            $params = [$name, $mail, sha1($password), $mail];

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            $success = '教師アカウント「' . htmlspecialchars($name) . '」を作成しました。';
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                 $error = 'このメールアドレスは既に使用されています。';
            } else {
                 $error = 'アカウント作成に失敗しました。';
            }
        }
    }
}

// HTML出力
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>教師アカウント作成</title>
</head>
<div class="main-content-wrapper" style="padding-top: 100px;">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">
        <div class="card w-100" style="max-width: 600px;">
            <div class="card-body p-4">
                <h3 class="card-title mb-3">教師アカウント作成</h3>
                <p class="card-text">新しい教師アカウントを作成します。</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="post" action="create_teacher.php" class="mt-4">
                    <div class="mb-3 text-start">
                        <label for="name" class="form-label">名前<span class="text-danger">*</span></label>
                        <input type="text" class="form-control shadow-sm" id="name" name="name" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="mail" class="form-label">メールアドレス<span class="text-danger">*</span></label>
                        <input type="email" class="form-control shadow-sm" id="mail" name="mail" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password" class="form-label">パスワード<span class="text-danger">*</span></label>
                        <input type="password" class="form-control shadow-sm" id="password" name="password" required>
                    </div>
                    <div class="mb-3 text-start">
                        <label for="password_confirm" class="form-label">パスワード (確認)<span class="text-danger">*</span></label>
                        <input type="password" class="form-control shadow-sm" id="password_confirm" name="password_confirm" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info px-5 mt-3">作成</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<?php
// include 'includes/footer.php';
?>
