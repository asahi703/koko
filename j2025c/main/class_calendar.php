<?php
require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user || !$class_id) {
    header('Location: community.php');
    exit;
}

$db = new cdb();

// クラス情報取得
$stmt = $db->prepare('
    SELECT c.*, com.community_name, com.community_id 
    FROM classes c 
    JOIN communities com ON c.class_community = com.community_id 
    WHERE c.class_id = ?
');
$stmt->execute([$class_id]);
$class = $stmt->fetch();

if (!$class) {
    header('Location: community.php');
    exit;
}

// イベント追加処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['start_date'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description'] ?? '');
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'] ?? null;
    if ($title && $start_date) {
        $stmt = $db->prepare('INSERT INTO calendar_events (class_id, title, description, start_date, end_date) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$class_id, $title, $description, $start_date, $end_date ?: null]);
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

// イベント一覧取得
$stmt = $db->prepare('SELECT * FROM calendar_events WHERE class_id = ? ORDER BY start_date ASC');
$stmt->execute([$class_id]);
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($class['class_name']); ?> - 予定表</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/chat.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content-wrapper">
    <main class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0"><?php echo htmlspecialchars($class['class_name']); ?> - 予定表</h2>
                <small class="text-muted">
                    <a href="class_select.php?id=<?php echo $class['community_id']; ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($class['community_name']); ?>
                    </a>
                </small>
            </div>
        </div>
        <div class="mb-4">
            <form method="post" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">タイトル</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">開始日</label>
                    <input type="date" name="start_date" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">終了日</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">説明</label>
                    <input type="text" name="description" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary">追加</button>
                </div>
            </form>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>タイトル</th>
                    <th>開始日</th>
                    <th>終了日</th>
                    <th>説明</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?php echo htmlspecialchars($event['title']); ?></td>
                    <td><?php echo htmlspecialchars($event['start_date']); ?></td>
                    <td><?php echo htmlspecialchars($event['end_date']); ?></td>
                    <td><?php echo htmlspecialchars($event['description']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
