<?php
require_once('common/dbmanager.php');
require_once('common/session.php');
$error = '';
$success = '';

// ログインユーザー取得
$user = get_login_user();
if (!$user) {
    header('Location: login.php');
    exit;
}
$login_user_id = $user['uuid']; // または $user['user_id'] など、ログインユーザーIDのカラム名に合わせてください

// 仮のユーザー情報（本来はセッションやGET/POSTから取得）
$target_user_id = 9;

try {
    $db = new cdb();
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}

// 選択中グループID取得
$selected_group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// グループ一覧取得
try {
    $group_stmt = $db->query('SELECT group_id, group_name FROM group_chats');
    $group_list = $group_stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'グループ一覧の取得に失敗しました。';
    $group_list = [];
}

// グループ参加者一覧取得
try {
    $user_stmt = $db->prepare('SELECT user_id, user_name FROM users WHERE user_id != ?');
    $user_stmt->execute([$login_user_id]);
    $all_users = $user_stmt->fetchAll();
} catch (PDOException $e) {
    $error = 'ユーザー一覧の取得に失敗しました。';
    $all_users = [];
}

// 新規グループ作成
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'], $_POST['group_members'])) {
    $group_name = trim($_POST['group_name']);
    $group_members = $_POST['group_members'];
    if ($group_name === '') {
        $error = 'グループ名を入力してください。';
    } elseif (!is_array($group_members) || count($group_members) === 0) {
        $error = '参加ユーザーを選択してください。';
    } else {
        try {
            $stmt = $db->prepare('INSERT INTO group_chats (group_name) VALUES (?)');
            $stmt->execute([$group_name]);
            $group_id = $db->lastInsertId();

            $members = array_unique(array_merge([$login_user_id], $group_members));
            $mem_stmt = $db->prepare('INSERT INTO group_chat_members (group_id, user_id) VALUES (?, ?)');
            foreach ($members as $uid) {
                $mem_stmt->execute([$group_id, $uid]);
            }
            header('Location: chat.php?group_id=' . $group_id);
            exit;
        } catch (PDOException $e) {
            $error = 'グループ作成に失敗しました。';
        }
    }
}

// メッセージ送信
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message) && $selected_group_id) {
        try {
            $stmt = $db->prepare('INSERT INTO group_chat_messages (group_id, user_id, message) VALUES (?, ?, ?)');
            $stmt->execute([$selected_group_id, $login_user_id, $message]);
            header('Location: chat.php?group_id=' . $selected_group_id);
            exit;
        } catch (PDOException $e) {
            $error = 'メッセージの送信に失敗しました。';
        }
    }
}

// チャット履歴取得
if ($selected_group_id) {
    try {
        $stmt = $db->prepare('
            SELECT m.*, u.user_name
            FROM group_chat_messages m
            JOIN users u ON m.user_id = u.user_id
            WHERE m.group_id = ?
            ORDER BY m.sent_at ASC
        ');
        $stmt->execute([$selected_group_id]);
        $chats = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'チャット履歴の取得に失敗しました。';
        $chats = [];
    }
} else {
    // 1対1チャットの場合
    try {
        $stmt = $db->prepare('
            SELECT c.*, u.user_name AS from_user_name
            FROM chats c
            JOIN users u ON c.from_chat = u.user_id
            WHERE (c.from_chat = ? AND c.to_chat = ?) OR (c.from_chat = ? AND c.to_chat = ?)
            ORDER BY c.sent_at ASC
        ');
        $stmt->execute([$login_user_id, $target_user_id, $target_user_id, $login_user_id]);
        $chats = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = 'チャット履歴の取得に失敗しました。';
        $chats = [];
    }
}

// ユーザーのテンプレート一覧取得
$stmt = $db->prepare('SELECT temprate_id, temprate_title, temprate_text FROM temprates WHERE temprate_user = ? ORDER BY temprate_id DESC');
$stmt->execute([$login_user_id]);
$templates = $stmt->fetchAll();



include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content-wrapper">
    <div class="container-fluid h-100">
        <div class="row vh-100 gx-0">
            <!-- グループ選択サイドバー -->
            <nav class="col-12 col-md-3 col-lg-3 px-0 group-sidebar d-flex flex-column bg-light border-end">
                <!-- テンプレート編集ボタン -->
                <div class="p-3 border-bottom bg-white">
                    <button class="btn btn-success w-100 mb-2" onclick="location.href='template.php'">
                        テンプレート編集
                    </button>
                </div>

                <div class="p-3 border-bottom bg-white">
                    <h5 class="mb-3 text-primary">グループ</h5>
                    <!-- 新規グループ作成ボタン -->
                    <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal"
                        data-bs-target="#createGroupModal">＋ 新規グループ</button>
                </div>
                <ul class="list-group list-group-flush flex-grow-1 overflow-auto group-list-scroll bg-light px-2">
                    <?php foreach ($group_list as $group): ?>
                        <li class="list-group-item list-group-item-action border-0 rounded my-1<?php if ($selected_group_id == $group['group_id'])
                            echo ' active'; ?>"
                            onclick="location.href='chat.php?group_id=<?php echo $group['group_id']; ?>'">
                            <?php echo htmlspecialchars($group['group_name']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- チャット画面 -->
            <main class="col-12 col-md-9 col-lg-9 px-0 d-flex flex-column chat-main-area position-relative bg-white">
                <!-- チャット履歴 -->
                <div class="flex-grow-1 overflow-auto chat-history p-4 chat-history-scroll bg-light">
                    <?php foreach ($chats as $chat): ?>
                        <?php
                        // グループチャットかどうかでキーを分岐
                        $is_group = $selected_group_id && isset($chat['user_name']);
                        $user_name = $is_group ? $chat['user_name'] : ($chat['from_user_name'] ?? '');
                        $message_text = $is_group ? $chat['message'] : ($chat['chat_text'] ?? '');
                        $from_id = $is_group ? $chat['user_id'] : ($chat['from_chat'] ?? 0);
                        $sent_at = $is_group ? $chat['sent_at'] : ($chat['sent_at'] ?? '');
                        ?>
                        <div
                            class="d-flex <?php echo $from_id == $login_user_id ? 'justify-content-end' : 'justify-content-start'; ?> mb-2">
                            <div
                                class="chat-msg <?php echo $from_id == $login_user_id ? 'chat-msg-sbm' : 'bg-white'; ?> border rounded p-2">
                                <div>
                                    <span class="fw-bold"><?php echo htmlspecialchars($user_name); ?></span><br>
                                    <?php echo nl2br(htmlspecialchars($message_text)); ?>
                                </div>
                                <div class="opacity-50 small mt-1 text-end">
                                    <?php echo date('H:i', strtotime($sent_at)); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- 入力欄（ページ下部に固定） -->
                <form method="post" class="input-form">
                    <div class="mb-3 input-group"
                        style="position: fixed !important; bottom: 2% !important; left: 33% !important; width: 65% !important;">
                        <input type="text" id="chatInput" name="message" class="form-control" placeholder="メッセージを入力">
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
                        <button class="btn btn-primary" type="submit">送信</button>
                    </div>
                </form>

            </main>
        </div>
    </div>
</div>

<!-- 新規グループ作成モーダル -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-labelledby="createGroupModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createGroupModalLabel">新規グループ作成</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">グループ名</label>
                    <input type="text" name="group_name" class="form-control" placeholder="グループ名" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">参加ユーザー</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="<?php echo $login_user_id; ?>"
                            id="user_self" checked disabled>
                        <label class="form-check-label" for="user_self">自分</label>
                    </div>
                    <?php foreach ($all_users as $user): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="group_members[]"
                                value="<?php echo $user['user_id']; ?>" id="user_<?php echo $user['user_id']; ?>">
                            <label class="form-check-label" for="user_<?php echo $user['user_id']; ?>">
                                <?php echo htmlspecialchars($user['user_name']); ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">作成</button>
            </div>
        </form>
    </div>
</div>

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


<script>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>