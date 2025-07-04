<?php
require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
$community_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$error = '';
$success = '';

if (!$user || !$community_id) {
    header('Location: community.php');
    exit;
}

$db = new cdb();

// コミュニティ情報取得
$stmt = $db->prepare('SELECT * FROM communities WHERE community_id = ?');
$stmt->execute([$community_id]);
$community = $stmt->fetch();
if (!$community) {
    header('Location: community.php');
    exit;
}

// クラス作成処理（オーナーのみ）
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['class_name']) && $community['community_owner'] == $user['uuid']) {
    $class_name = trim($_POST['class_name']);
    if ($class_name === '') {
        $error = 'クラス名を入力してください。';
    } else {
        try {
            $stmt = $db->prepare('INSERT INTO classes (class_name, class_community) VALUES (?, ?)');
            $stmt->execute([$class_name, $community_id]);
            $success = 'クラスを作成しました。';
        } catch (PDOException $e) {
            $error = 'クラス作成に失敗しました。';
        }
    }
}

// 招待コード生成・削除処理（オーナーのみ）
$invite_code = null;
if ($community['community_owner'] == $user['uuid']) {
    // コード生成
    if (isset($_POST['generate_invite_code'])) {
        // ランダムなコード生成
        $invite_code = bin2hex(random_bytes(8));
        // DBに保存
        $stmt = $db->prepare('INSERT INTO community_invite_codes (community_id, invite_code) VALUES (?, ?)');
        try {
            $stmt->execute([$community_id, $invite_code]);
            $success = '招待コードを生成しました。';
        } catch (PDOException $e) {
            $error = '招待コードの生成に失敗しました。';
        }
    }
    // コード削除
    if (isset($_POST['delete_invite_code'])) {
        $stmt = $db->prepare('DELETE FROM community_invite_codes WHERE community_id = ?');
        $stmt->execute([$community_id]);
        $success = '招待コードを削除しました。';
    }
    // 現在の招待コード取得
    $stmt = $db->prepare('SELECT invite_code FROM community_invite_codes WHERE community_id = ?');
    $stmt->execute([$community_id]);
    $row = $stmt->fetch();
    if ($row) {
        $invite_code = $row['invite_code'];
    }
}

// コミュニティ内の全クラス一覧取得
$stmt = $db->prepare('SELECT * FROM classes WHERE class_community = ?');
$stmt->execute([$community_id]);
$all_classes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>クラス選択</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/community.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content-wrapper">
    <main class="container py-4">
        <h2 class="mb-3"><?php echo htmlspecialchars($community['community_name']); ?> のクラス一覧</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($community['community_owner'] == $user['uuid']): ?>
            <!-- クラス作成フォーム -->
            <form method="post" class="mb-4">
                <div class="input-group">
                    <input type="text" name="class_name" class="form-control" placeholder="新しいクラス名" required>
                    <button type="submit" class="btn btn-primary">クラス作成</button>
                </div>
            </form>
            <!-- 招待コード生成・削除UI -->
            <div class="mb-4">
                <form method="post" style="display:inline;">
                    <button type="submit" name="generate_invite_code" class="btn btn-success"
                        <?php if ($invite_code) echo 'disabled'; ?>>招待コード生成</button>
                </form>
                <?php if ($invite_code): ?>
                    <span class="ms-3">招待コード: <strong><?php echo htmlspecialchars($invite_code); ?></strong></span>
                    <form method="post" style="display:inline;">
                        <button type="submit" name="delete_invite_code" class="btn btn-danger btn-sm ms-2">削除</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <h4>コミュニティ内のクラス</h4>
        <div class="row">
            <?php if (count($all_classes) > 0): ?>
                <?php foreach ($all_classes as $class): ?>
                    <div class="col-12 col-md-6 col-lg-4 mb-2">
                        <a href="class_chat.php?id=<?php echo $class['class_id']; ?>" class="text-decoration-none">
                            <div class="card p-3 hover-shadow">
                                <div class="fw-bold"><?php echo htmlspecialchars($class['class_name']); ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">クラスがありません。</div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
