<?php
require_once('../main/common/dbmanager.php');
require_once('../main/common/adminsession.php');

$db = new cdb();

// コミュニティ追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_community'])) {
    $name = trim($_POST['community_name'] ?? '');
    $desc = trim($_POST['community_description'] ?? '');
    $owner = intval($_POST['community_owner'] ?? 0);
    if ($name && $owner) {
        $stmt = $db->prepare('INSERT INTO communities (community_name, community_description, community_owner) VALUES (?, ?, ?)');
        $stmt->execute([$name, $desc, $owner]);
    }
}

// 削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_community_id'])) {
    $delete_id = intval($_POST['delete_community_id']);
    $stmt = $db->prepare('DELETE FROM communities WHERE community_id = ?');
    $stmt->execute([$delete_id]);
}

// コミュニティ一覧取得（オーナー名も取得）
$stmt = $db->prepare('
    SELECT c.community_id, c.community_name, c.community_description, u.user_name AS owner_name
    FROM communities c
    LEFT JOIN users u ON c.community_owner = u.user_id
');
$stmt->execute();
$communities = $stmt->fetchAll();

// ユーザー一覧（コミュニティ追加用）
$user_stmt = $db->prepare('SELECT user_id, user_name FROM users');
$user_stmt->execute();
$users = $user_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>コミュニティ管理</title>
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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCommunityModal">
            コミュニティを追加
        </button>
    </div>
    <h2>コミュニティ一覧</h2>
    <table class="table table-striped table-hover mt-3">
        <thead class="table-light section-header">
            <tr>
                <th>コミュニティ名</th>
                <th>説明</th>
                <th>オーナーのユーザー</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($communities as $community): ?>
            <tr>
                <td><?php echo htmlspecialchars($community['community_name']); ?></td>
                <td><?php echo htmlspecialchars($community['community_description']); ?></td>
                <td><?php echo htmlspecialchars($community['owner_name'] ?? ''); ?></td>
                <td>
                    <form method="post" onsubmit="return confirm('本当に削除しますか？');" style="display:inline;">
                        <input type="hidden" name="delete_community_id" value="<?php echo $community['community_id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm">削除</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- コミュニティ追加モーダル -->
<div class="modal fade" id="addCommunityModal" tabindex="-1" aria-labelledby="addCommunityModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addCommunityModalLabel">コミュニティを追加</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="community_name" class="form-label">コミュニティ名</label>
          <input type="text" class="form-control" id="community_name" name="community_name" required>
        </div>
        <div class="mb-3">
          <label for="community_description" class="form-label">説明</label>
          <textarea class="form-control" id="community_description" name="community_description"></textarea>
        </div>
        <div class="mb-3">
          <label for="community_owner" class="form-label">オーナーのユーザー</label>
          <select class="form-select" id="community_owner" name="community_owner" required>
            <option value="">選択してください</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['user_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
        <button type="submit" name="add_community" class="btn btn-primary">追加</button>
      </div>
    </form>
  </div>
</div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>