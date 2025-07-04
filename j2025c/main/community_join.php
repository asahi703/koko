<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_code'])) {
    $invite_code = trim($_POST['invite_code']);
    if (!$user) {
        $error = 'ログインしてください。';
    } elseif ($invite_code === '') {
        $error = '招待コードを入力してください。';
    } else {
        $db = new cdb();
        // コードが有効か確認
        $stmt = $db->prepare('SELECT community_id FROM community_invite_codes WHERE invite_code = ?');
        $stmt->execute([$invite_code]);
        $row = $stmt->fetch();
        if ($row) {
            $community_id = $row['community_id'];
            // 既に参加していないか確認
            $stmt2 = $db->prepare('SELECT * FROM community_users WHERE user_id = ? AND community_id = ?');
            $stmt2->execute([$user['uuid'], $community_id]);
            if (!$stmt2->fetch()) {
                // 参加処理
                $stmt3 = $db->prepare('INSERT INTO community_users (user_id, community_id) VALUES (?, ?)');
                $stmt3->execute([$user['uuid'], $community_id]);
                $success = 'コミュニティに参加しました。';
            } else {
                $error = 'すでにこのコミュニティに参加しています。';
            }
        } else {
            $error = '招待コードが無効です。';
        }
    }
}
?>
<div class="main-content-wrapper">
    <main class="col-md-9 offset-md-2" style="padding-top: 150px;">
        <div class="text-center mb-4">
            <h2 class="fs-5 fw-bold">コミュニティに参加</h2>
        </div>
        <div class="card bg-secondary-subtle mx-auto w-100" style="max-width: 800px;">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="inviteCode" class="form-label">招待コード<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="inviteCode" name="invite_code" required>
                    </div>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary px-5">参加</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
