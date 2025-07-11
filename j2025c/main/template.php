<?php
require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
$user_id = $user['uuid']; // ユーザーIDを取得
if (!$user) {
    header('Location: login.php');
    exit;
}

$db = new cdb();
$error = '';
$success = '';

// テンプレート作成処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_template'])) {
    $title = trim($_POST['temprate_title'] ?? '');
    $body = trim($_POST['temprate_text'] ?? '');
    if ($title && $body) {
        $stmt = $db->prepare('INSERT INTO temprates (temprate_title, temprate_text, temprate_user) VALUES (?, ?, ?)');
        $stmt->execute([$title, $body, $user_id]);
        $success = 'テンプレートを作成しました。';
    } else {
        $error = 'タイトルと本文は必須です。';
    }
}

// テンプレート編集処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_template'])) {
    $edit_id = intval($_POST['temprate_id'] ?? 0);
    $title = trim($_POST['edit_temprate_title'] ?? '');
    $body = trim($_POST['edit_temprate_text'] ?? '');
    if ($edit_id && $title && $body) {
        $stmt = $db->prepare('UPDATE temprates SET temprate_title = ?, temprate_text = ? WHERE temprate_id = ? AND temprate_user = ?');
        $stmt->execute([$title, $body, $edit_id, $user_id]);
        $success = 'テンプレートを更新しました。';
    } else {
        $error = 'タイトルと本文は必須です。';
    }
}

// テンプレート削除処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_template_id'])) {
    $delete_id = intval($_POST['delete_template_id']);
    if ($delete_id) {
        $stmt = $db->prepare('DELETE FROM temprates WHERE temprate_id = ? AND temprate_user = ?');
        $stmt->execute([$delete_id, $user_id]);
        $success = 'テンプレートを削除しました。';
    }
}

// ユーザーのテンプレート一覧取得
$stmt = $db->prepare('SELECT temprate_id, temprate_title, temprate_text FROM temprates WHERE temprate_user = ? ORDER BY temprate_id DESC');
$stmt->execute([$user_id]);
$templates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>テンプレート管理</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/template.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/sidebar.css">
</head>

<body class="bg-light">
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <!--戻るボタン-->
    <a href="chat.php" class="arrow-back-btn">
        <i class="fa-solid fa-arrow-left fa-2xl"></i>
    </a>
    
    <div class="container-fluid">
        <div class="row">
            <!-- サイドバーはincludes/sidebar.phpで出力済み -->

            <!-- メインコンテンツ -->
            <main
                class="col-12 col-md-9 col-lg-10 px-md-4 d-flex flex-column align-items-center justify-content-center text-center mx-auto main-content-template"
                style="margin-top: 80px; margin-bottom: 60px; max-width: 1200px;">
                <div class="w-100" style="max-width: 700px;">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                </div>

                <!--タイトル-->
                <div class="d-flex flex-row w-100 justify-content-center">

                    <div>
                        <p class="fs-3">テンプレート一覧</p>
                    </div>

                    <!-- 作成ボタン：右上固定 -->
                    <button type="button" class="tmp-create-btn btn btn-primary mb-3" data-bs-toggle="modal"
                        data-bs-target="#templateCreateModal">
                        テンプレートを作成する
                    </button>
                </div>


                <!-- テンプレート作成モーダル -->
                <div class="modal fade" id="templateCreateModal" tabindex="-1"
                    aria-labelledby="templateCreateModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="templateCreateModalLabel">テンプレートを作成</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="閉じる"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="templateTitle" class="form-label">タイトル<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="templateTitle" name="temprate_title"
                                            placeholder="テンプレートタイトル" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="templateBody" class="form-label">本文<span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="templateBody" name="temprate_text" rows="4"
                                            placeholder="テンプレート本文" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                    <button type="submit" name="create_template" class="btn btn-primary">作成</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- テンプレート編集モーダル -->
                <div class="modal fade" id="templateEditModal" tabindex="-1" aria-labelledby="templateEditModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form method="post">
                                <input type="hidden" name="temprate_id" id="editTemplateId">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="templateEditModalLabel">テンプレート編集</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="閉じる"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="editTemplateTitle" class="form-label">タイトル<span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="editTemplateTitle"
                                            name="edit_temprate_title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editTemplateBody" class="form-label">本文<span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="editTemplateBody" name="edit_temprate_text"
                                            rows="4" required></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                    <button type="submit" name="edit_template" class="btn btn-primary">保存</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="template-list-container w-100 mt-3 template-list-scroll">
                    <div class="row g-4">
                        <?php foreach ($templates as $template): ?>
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="template-card p-0 bg-white rounded shadow-sm h-100 d-flex flex-column align-items-center template-card-hover"
                                    data-bs-toggle="modal" data-bs-target="#templateEditModal"
                                    data-id="<?php echo $template['temprate_id']; ?>"
                                    data-title="<?php echo htmlspecialchars($template['temprate_title']); ?>"
                                    data-body="<?php echo htmlspecialchars($template['temprate_text']); ?>">
                                    <div
                                        class="template-title fs-4 mb-2 d-flex align-items-center justify-content-center w-100">
                                        <?php echo htmlspecialchars($template['temprate_title']); ?>
                                        <form method="post" class="ms-2 d-inline position-relative" onsubmit="return confirm('本当に削除しますか？');"
                                            style="margin-bottom:0;">
                                            <input type="hidden" name="delete_template_id"
                                                value="<?php echo $template['temprate_id']; ?>">
                                            <button type="submit" class="tmp-dlt-btn btn btn-link p-0 ms-2 text-danger"
                                                style="font-size:1em; opacity:0.7;" title="削除">
                                                <i class="fa-solid fa-trash fa-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="template-body w-100">
                                        <?php echo nl2br(htmlspecialchars($template['temprate_text'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 編集モーダルに値をセット
        document.querySelectorAll('.template-card').forEach(function (card) {
            card.addEventListener('click', function () {
                // カード内のデータ属性から値を取得
                const id = card.getAttribute('data-id');
                const title = card.getAttribute('data-title');
                const body = card.getAttribute('data-body');

                // モーダルのinput, textareaに値をセット
                document.getElementById('editTemplateId').value = id;
                document.getElementById('editTemplateTitle').value = title;
                document.getElementById('editTemplateBody').value = body;
            });
        });
    </script>
</body>

</html>