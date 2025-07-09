<?php
include 'includes/header.php';
include 'includes/sidebar.php';
require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['community_name'])) {
        // コミュニティ作成処理
        if (!$user) {
            $error = 'ログインしてください。';
        } elseif (empty($_POST['community_name'])) {
            $error = 'コミュニティ名を入力してください。';
        } else {
            try {
                $db = new cdb();
                $stmt = $db->prepare('INSERT INTO communities (community_name, community_description, community_owner) VALUES (?, ?, ?)');
                $stmt->execute([
                    $_POST['community_name'],
                    $_POST['community_description'] ?? '',
                    $user['uuid']
                ]);
                $success = 'コミュニティを作成しました。';
                // ページリロードでフォーム再送信防止
                header("Location: community.php?created=1");
                exit;
            } catch (PDOException $e) {
                $error = 'コミュニティ作成に失敗しました。';
            }
        }
    } elseif (isset($_POST['invite_code'])) {
        // コミュニティ参加処理
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
                    header("Location: community.php?joined=1"); // 参加後のリダイレクト
                    exit;
                } else {
                    $error = 'すでにこのコミュニティに参加しています。';
                }
            } else {
                $error = '招待コードが無効です。';
            }
        }
    }
}

if (isset($_GET['created'])) {
    $success = 'コミュニティを作成しました。';
}
if (isset($_GET['joined'])) {
    $success = 'コミュニティに参加しました。';
}

// 参加している or オーナーのコミュニティ一覧取得
$communities = [];
if ($user) {
    try {
        $db = new cdb();
        $stmt = $db->prepare(
            'SELECT DISTINCT c.*
             FROM communities c
             LEFT JOIN community_users cu ON c.community_id = cu.community_id
             WHERE cu.user_id = ? OR c.community_owner = ?
             ORDER BY c.community_id DESC'
        );
        $stmt->execute([$user['uuid'], $user['uuid']]);
        $communities = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'コミュニティ一覧の取得に失敗しました。';
    }
}
?>
<head>
    <title>コミュニティ選択</title>
    <link rel="stylesheet" href="../main/css/community.css">
</head>
<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-styles">

        <div class="d-flex justify-content-center gap-3 position-fixed class-create-button" style="top: 150px; right: 20px;">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCommunityModal">
                コミュニティ作成
            </button>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#joinCommunityModal">
                コミュニティに参加
            </button>
        </div>

        <div class="modal fade" id="createCommunityModal" tabindex="-1" aria-labelledby="createCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCommunityModalLabel">コミュニティ作成</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="community.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="communityName" class="form-label">コミュニティ名<span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow" id="communityName" name="community_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="communityDesc" class="form-label">説明</label>
                                <textarea class="form-control shadow" id="communityDesc" name="community_description"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                            <button type="submit" class="btn btn-primary px-5">作成</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="joinCommunityModal" tabindex="-1" aria-labelledby="joinCommunityModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="joinCommunityModalLabel">コミュニティに参加</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="community.php">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="inviteCode" class="form-label">招待コード<span class="text-danger">*</span></label>
                                <input type="text" class="form-control shadow" id="inviteCode" name="invite_code" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                            <button type="submit" class="btn btn-success px-5">参加</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="w-100 d-flex justify-content-start align-items-start mb-3 community-name-display">
            <p class="mb-0 fs-3">参加中またはオーナーのコミュニティ一覧</p>
        </div>

        <div class="container mt-4 class-card-container-styles">
            <div class="row">
                <?php if ($user && count($communities) > 0): ?>
                    <?php foreach ($communities as $community): ?>
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                            <a href="class_select.php?id=<?php echo $community['community_id']; ?>" class="nav-link">
                                <div class="class-card rounded d-flex align-items-center p-3 shadow-sm w-100 class-card-style">
                                    <div class="rounded me-3 class-card-image-placeholder"></div>
                                    <div class="flex-grow-1 text-start">
                                        <p class="mb-0 fw-bold"><?php echo htmlspecialchars($community['community_name']); ?></p>
                                        <small class="text-muted"><?php echo htmlspecialchars($community['community_description']); ?></small>
                                        <?php if ($community['community_owner'] == $user['uuid']): ?>
                                            <span class="badge bg-primary ms-2">オーナー</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($user): ?>
                    <div class="col-12">
                        <div class="alert alert-info">参加中またはオーナーのコミュニティがありません。</div>
                    </div>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-warning">ログインしてください。</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>