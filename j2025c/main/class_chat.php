<?php
require_once('common/dbmanager.php');
require_once('common/session.php');
require_once('common/notification_helper.php');

$user = get_login_user();
$login_user_id = $user['uuid'];
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

// メッセージ送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        try {
            $stmt = $db->prepare('
                INSERT INTO class_chats (class_id, user_id, message) 
                VALUES (?, ?, ?)
            ');
            $stmt->execute([$class_id, $user['uuid'], $message]);

            // チャットメッセージ通知を送信
            $sender_name = $user['user_name'] ?? $user['name'] ?? 'ユーザー';
            notify_chat_message($class_id, $user['uuid'], $sender_name, $message);

            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } catch (PDOException $e) {
            $error = 'メッセージの送信に失敗しました。';
        }
    }
}

// チャットメッセージ取得
$stmt = $db->prepare('
    SELECT ch.*, u.user_name, u.user_icon 
    FROM class_chats ch 
    JOIN users u ON ch.user_id = u.user_id 
    WHERE ch.class_id = ? 
    ORDER BY ch.created_at DESC 
    LIMIT 100
');
$stmt->execute([$class_id]);
$messages = $stmt->fetchAll();


// ユーザーのテンプレート一覧取得
$stmt = $db->prepare('SELECT temprate_id, temprate_title, temprate_text FROM temprates WHERE temprate_user = ? ORDER BY temprate_id DESC');
$stmt->execute([$login_user_id]);
$templates = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($class['class_name']); ?> - チャット</title>
    <link rel="stylesheet" href="css/Global.css">
    <link rel="stylesheet" href="css/chat.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content-wrapper">
        <main class="container-fluid py-4 d-flex flex-column" style="height: calc(100vh - 56px);">
            <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                <div>
                    <h2 class="mb-0"><?php echo htmlspecialchars($class['class_name']); ?></h2>
                    <small class="text-muted">
                        <a href="class_select.php?id=<?php echo $class['community_id']; ?>"
                            class="text-decoration-none">
                            <?php echo htmlspecialchars($class['community_name']); ?>
                        </a>
                    </small>
                    <a href="class_calender.php?id=<?php echo $class_id; ?>"
                        class="btn btn-outline-primary btn-sm ms-3">行事予定表</a>
                </div>
            </div>

            <!-- チャットメッセージ表示エリア -->
            <div class="chat-messages bg-light rounded p-3 mb-4 flex-grow-1" style="overflow-y: auto;">
                <?php foreach (array_reverse($messages) as $message): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                            <div class="d-flex align-items-center">
                                <?php if (!empty($message['user_icon'])): ?>
                                    <img src="../<?php echo htmlspecialchars($message['user_icon']); ?>"
                                        class="rounded-circle me-2" style="width: 32px; height: 32px;" alt="">
                                <?php else: ?>
                                    <img src="../main/img/headerImg/account.png" class="rounded-circle me-2"
                                        style="width: 32px; height: 32px;" alt="">
                                <?php endif; ?>
                                <span class="fw-bold"><?php echo htmlspecialchars($message['user_name']); ?></span>
                            </div>
                            <small class="text-muted">
                                <?php echo date('Y/m/d H:i', strtotime($message['created_at'])); ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- メッセージ入力フォーム -->
            <div class="mt-auto bg-white border-top">
                <form method="post" class="d-flex align-items-center">
                    <textarea class="form-control me-2" name="message" id="chatInput" rows="1" placeholder="メッセージを入力"
                        required></textarea>
                    <!-- +アイコンとドロップダウン -->
                    <div class="dropdown">
                        <button class="btn btn-link px-2" type="button" id="templateDropdownBtn"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-plus fa-lg"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="templateDropdownBtn"
                            style="max-height:200px;overflow-y:auto;">
                            <?php foreach ($templates as $template): ?>
                                <li>
                                    <!--テンプレートドロップダウンメニュー-->
                                    <a class="dropdown-item template-insert-btn" href="#"
                                        data-body="<?php echo htmlspecialchars($template['temprate_text'], ENT_QUOTES); ?>">
                                        <?php echo htmlspecialchars($template['temprate_title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <button type="submit" class="btn btn-primary">送信</button>
                </form>
            </div>


            <!--テンプレートモーダル-->
            <div class="modal fade" id="myModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">テンプレート編集・差し替え</h5>
                        </div>
                        <div class="modal-body" id="modal-body"></div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // チャットを最新のメッセージまでスクロール
        window.onload = function () {
            const chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        };

        // プレースホルダを全部抜き出す
        function extractPlaceholders(template) {
            const regex = /{([^{}]+)}/g;
            let match;
            const results = new Set();
            while ((match = regex.exec(template)) !== null) {
                results.add(match[1]);
            }
            return Array.from(results);
        }

        // 置換
        function fillTemplate(template, values) {
            return template.replace(/{([^{}]+)}/g, (m, key) => values[key] ?? m);
        }



        // ページロード時にモーダルの背景やbodyクラスが残っていたら消す
        document.addEventListener('DOMContentLoaded', function () {
            document.body.classList.remove('modal-open');
            var backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function (bd) { bd.parentNode.removeChild(bd); });

            // テンプレートを入力欄に挿入する
            document.querySelectorAll('.template-insert-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const originalTemplate = btn.getAttribute('data-body');
                    // プレースホルダ重複排除（SetでOK）
                    const regex = /{([^{}]+)}/g;
                    let match;
                    const placeholderSet = new Set();
                    while ((match = regex.exec(originalTemplate)) !== null) {
                        placeholderSet.add(match[1]);
                    }
                    const placeholders = Array.from(placeholderSet);

                    // 編集可能本文
                    let formHtml = `
            <div class="mb-3">
                <label>本文（書き換え可）</label>
                <textarea id="modalTemplateText" class="form-control" rows="4">${originalTemplate}</textarea>
            </div>
            <form id="placeholderForm">
                <table class="table table-sm">
                  <thead><tr><th>項目</th><th>値</th></tr></thead><tbody>
        `;
                    placeholders.forEach(ph => {
                        formHtml += `
                <tr>
                  <td>${ph}</td>
                  <td><input type="text" class="form-control" name="${ph}"></td>
                </tr>
            `;
                    });
                    formHtml += `
                  </tbody>
                </table>
                <button type="submit" class="btn btn-primary mt-2">反映</button>
            </form>
        `;
                    document.getElementById('modal-body').innerHTML = formHtml;
                    const modal = new bootstrap.Modal(document.getElementById('myModal'));
                    modal.show();

                    // サブミット時
                    document.getElementById('placeholderForm').onsubmit = function (e) {
                        e.preventDefault();
                        const values = {};
                        placeholders.forEach(ph => values[ph] = this.elements[ph].value);
                        // 最新の本文で置換
                        const currentTemplate = document.getElementById('modalTemplateText').value;
                        document.getElementById('chatInput').value = fillTemplate(currentTemplate, values);
                        modal.hide();
                    };
                });
            });
        });
    </script>
</body>

</html>