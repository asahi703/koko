<?php
include 'includes/header.php';
include 'includes/sidebar.php';

require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
$error = '';

// 先生以外はアクセス不可
if (!$user || empty($user['user_is_teacher'])) {
    $error = 'コミュニティ作成権限がありません。';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$user) {
        $error = 'ログインしてください。';
    } elseif (empty($_POST['community_name'])) {
        $error = 'コミュニティ名を入力してください。';
    } elseif (empty($user['user_is_teacher'])) {
        // 先生でなければ作成不可
        $error = 'コミュニティ作成権限がありません。';
    } else {
        try {
            $db = new cdb();
            $stmt = $db->prepare('INSERT INTO communities (community_name, community_description, community_owner) VALUES (?, ?, ?)');
            $stmt->execute([
                $_POST['community_name'],
                $_POST['community_description'] ?? '',
                $user['uuid']
            ]);
            // 作成後は一覧ページにリダイレクト
            header("Location: community.php?created=1");
            exit;
        } catch (PDOException $e) {
            $error = 'コミュニティ作成に失敗しました。';
        }
    }
}
?>
<head>
    <title>コミュニティ作成</title>
    <link rel="stylesheet" href="../main/css/community_create.css">
</head>
<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2 main-content-community">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">コミュニティ作成</h2>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (!$error): ?>
        <div class="card bg-secondary-subtle mx-auto w-100 card-community-create">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="communityName" class="form-label">コミュニティ名<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="communityName" name="community_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="communityDescription" class="form-label">説明</label>
                        <textarea class="form-control" id="communityDescription" name="community_description" rows="4"></textarea>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">作成</button>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </main>
</div>
