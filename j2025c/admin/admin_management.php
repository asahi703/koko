<?php
require_once('../main/common/dbmanager.php');
require_once('../main/common/adminsession.php');

$db = new cdb();

//ログイン情報取得
$login_admin = get_login_admin();

// 管理者追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
  $name = trim($_POST['administer_name'] ?? '');
  $mail = trim($_POST['administer_mailaddress'] ?? '');
  $pass = $_POST['administer_password'] ?? '';
  if ($name && $mail && $pass) {
    $stmt = $db->prepare('INSERT INTO administers (administer_name, administer_mailaddress, administer_password) VALUES (?, ?, ?)');
    $stmt->execute([$name, $mail, sha1($pass)]);
  }
}

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_admin_id'])) {
  $delete_id = intval($_POST['delete_admin_id']);
  $stmt = $db->prepare('DELETE FROM administers WHERE administer_id = ?');
  $stmt->execute([$delete_id]);
}

// 管理者一覧取得
$stmt = $db->prepare('SELECT administer_id, administer_name, administer_mailaddress FROM administers');
$stmt->execute();
$admins = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <title>管理者管理</title>
  <link rel="stylesheet" href="css/Global.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <?php include 'includes/header.php'; ?>
  <?php include 'includes/sidebar.php'; ?>
  <main class="main-content-wrapper">
    <div class="container bg-white rounded shadow p-4">
      <div class="d-flex justify-content-end mt-3">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
          管理者を追加
        </button>
      </div>
      <h2>管理者一覧</h2>
      <table class="table table-bordered table-hover mt-3">
        <thead class="table-light">
          <tr>
            <th>管理者名</th>
            <th>メールアドレス</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($admins as $admin): ?>
            <tr>
              <td><?php echo htmlspecialchars($admin['administer_name']); ?></td>
              <td><?php echo htmlspecialchars($admin['administer_mailaddress']); ?></td>
              <td>
                <?php if ($login_admin && $login_admin['auid'] != $admin['administer_id']): ?>
                  <form method="post" onsubmit="return confirm('本当に削除しますか？');" style="display:inline;">
                    <input type="hidden" name="delete_admin_id" value="<?php echo $admin['administer_id']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">削除</button>
                  </form>
                <?php else: ?>
                  <span class="text-muted">自分自身は削除できません</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- 管理者追加モーダル -->
    <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form method="post" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addAdminModalLabel">管理者を追加</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="administer_name" class="form-label">管理者名</label>
              <input type="text" class="form-control" id="administer_name" name="administer_name" required>
            </div>
            <div class="mb-3">
              <label for="administer_mailaddress" class="form-label">メールアドレス</label>
              <input type="email" class="form-control" id="administer_mailaddress" name="administer_mailaddress"
                required>
            </div>
            <div class="mb-3">
              <label for="administer_password" class="form-label">パスワード</label>
              <input type="password" class="form-control" id="administer_password" name="administer_password" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
            <button type="submit" name="add_admin" class="btn btn-primary">追加</button>
          </div>
        </form>
      </div>
    </div>
  </main>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>