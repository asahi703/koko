<?php
require_once('../main/common/dbmanager.php');// Path to the dbmanager file
require_once('../main/common/adminsession.php');//session


$db = new cdb();


// ユーザー追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['user_name'] ?? '');
    $mail = trim($_POST['user_mailaddress'] ?? '');
    $pass = $_POST['user_password'] ?? '';
    $is_teacher = isset($_POST['user_is_teacher']) ? 1 : 0;
    if ($name && $mail && $pass) {
        $stmt = $db->prepare('INSERT INTO users (user_name, user_mailaddress, user_password, user_is_teacher, user_login) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $mail, sha1($pass), $is_teacher, $mail]);
    }
}

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_id = intval($_POST['delete_user_id']);
    $stmt = $db->prepare('DELETE FROM users WHERE user_id = ?');
    $stmt->execute([$delete_id]);
}

// ユーザー一覧取得
$stmt = $db->prepare('SELECT user_id, user_name, user_mailaddress, user_is_teacher FROM users');
$stmt->execute();
$users = $stmt->fetchAll();
?>
?>


<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>管理者ページ ホーム</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-content-wrapper">
        <div class="container bg-white rounded shadow">
            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    ユーザーを追加
                </button>
            </div>
            <h2>ユーザー一覧</h2>
            <table class="table table-striped table-hover mt-3">
                <thead class="table-light section-header">
                    <tr>
                        <th>ユーザー名</th>
                        <th>メールアドレス</th>
                        <th>アカウント種別</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['user_mailaddress']); ?></td>
                            <td>
                                <span class="badge <?php echo $user['user_is_teacher'] ? 'bg-primary' : 'bg-success'; ?>">
                                    <?php echo $user['user_is_teacher'] ? '教師' : '保護者'; ?>
                                </span>
                            </td>
                            <td>
                                <form method="post" onsubmit="return confirm('本当に削除しますか？');" style="display:inline;">
                                    <input type="hidden" name="delete_user_id" value="<?php echo $user['user_id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">削除</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- ユーザー追加モーダル -->
        <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">ユーザーを追加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user_name" class="form-label">ユーザー名</label>
                            <input type="text" class="form-control" id="user_name" name="user_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_mailaddress" class="form-label">メールアドレス</label>
                            <input type="email" class="form-control" id="user_mailaddress" name="user_mailaddress" required>
                        </div>
                        <div class="mb-3">
                            <label for="user_password" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="user_password" name="user_password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">アカウント種別</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="user_is_teacher" name="user_is_teacher">
                                <label class="form-check-label" for="user_is_teacher">
                                    教師アカウント（チェックしない場合は保護者アカウント）
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                        <button type="submit" name="add_user" class="btn btn-primary">追加</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>