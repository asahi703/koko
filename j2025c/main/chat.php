<?php
require_once('common/dbmanager.php');
require_once('common/session.php');
require_once('common/notification_helper.php');
$error = '';
$success = '';

// ログインユーザー取得
$user = get_login_user();
if (!$user) {
    header('Location: login.php');
    exit;
}
$login_user_id = $user['user_id'] ?? $user['uuid']; // user_idを優先、なければuuidを使用

// チャット相手のユーザーID取得（URLパラメータから）
$target_user_id = isset($_GET['user']) ? intval($_GET['user']) : 0; // デフォルトを0に変更
$target_user_name = isset($_GET['name']) ? $_GET['name'] : '';

// チャット相手のユーザー名を取得（URLパラメータから取得できない場合はDBから）
if (!$target_user_name && $target_user_id > 0) {
    try {
        $db = new cdb();
        $stmt = $db->prepare('SELECT user_name FROM users WHERE user_id = ?');
        $stmt->execute([$target_user_id]);
        $user_data = $stmt->fetch();
        if ($user_data) {
            $target_user_name = $user_data['user_name'];
        } else {
            // ユーザーが存在しない場合
            $target_user_id = 0;
            $target_user_name = '';
            $error = '指定されたユーザーが見つかりません。';
        }
    } catch (PDOException $e) {
        $target_user_name = '';
        $error = 'ユーザー情報の取得に失敗しました。';
    }
}

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

// チャット履歴があるユーザーを取得
try {
    $chat_history_stmt = $db->prepare('
        SELECT DISTINCT 
            CASE 
                WHEN c.from_chat = ? THEN c.to_chat 
                ELSE c.from_chat 
            END as user_id,
            u.user_name,
            MAX(c.sent_at) as last_chat_time
        FROM chats c
        JOIN users u ON (
            CASE 
                WHEN c.from_chat = ? THEN c.to_chat = u.user_id
                ELSE c.from_chat = u.user_id
            END
        )
        WHERE c.from_chat = ? OR c.to_chat = ?
        GROUP BY user_id, u.user_name
        ORDER BY last_chat_time DESC
    ');
    $chat_history_stmt->execute([$login_user_id, $login_user_id, $login_user_id, $login_user_id]);
    $chat_history_users = $chat_history_stmt->fetchAll();
    
    // チャット履歴があるユーザーのIDを配列で保存
    $chat_history_user_ids = array_column($chat_history_users, 'user_id');
    
    // チャット履歴がないユーザーを分離
    $new_users = array_filter($all_users, function($user) use ($chat_history_user_ids) {
        return !in_array($user['user_id'], $chat_history_user_ids);
    });
    
} catch (PDOException $e) {
    $error = 'チャット履歴の取得に失敗しました。';
    $chat_history_users = [];
    $new_users = $all_users;
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
    if (!empty($message)) {
        if ($selected_group_id) {
            // グループチャットの場合
            try {
                $stmt = $db->prepare('INSERT INTO group_chat_messages (group_id, user_id, message) VALUES (?, ?, ?)');
                $stmt->execute([$selected_group_id, $login_user_id, $message]);
                
                // グループチャット通知を送信
                $sender_name = $user['user_name'] ?? $user['name'] ?? 'ユーザー';
                notify_group_chat_message($selected_group_id, $login_user_id, $sender_name, $message);
                
                header('Location: chat.php?group_id=' . $selected_group_id);
                exit;
            } catch (PDOException $e) {
                $error = 'メッセージの送信に失敗しました。エラー: ' . $e->getMessage();
                error_log('グループチャット送信エラー: ' . $e->getMessage());
            }
        } else {
            // 1対1チャットの場合
            if ($target_user_id <= 0) {
                $error = 'チャット相手が選択されていません。';
            } else {
                try {
                    // 送信前にユーザーの存在確認
                    $check_stmt = $db->prepare('SELECT user_id FROM users WHERE user_id = ?');
                    $check_stmt->execute([$target_user_id]);
                    if (!$check_stmt->fetch()) {
                        $error = '送信相手のユーザーが見つかりません。';
                    } else {
                        $stmt = $db->prepare('INSERT INTO chats (from_chat, to_chat, chat_text) VALUES (?, ?, ?)');
                        $stmt->execute([$login_user_id, $target_user_id, $message]);
                        
                        // 1対1チャット通知を送信
                        $sender_name = $user['user_name'] ?? $user['name'] ?? 'ユーザー';
                        notify_direct_message($target_user_id, $login_user_id, $sender_name, $message);
                        
                        header('Location: chat.php?user=' . $target_user_id . '&name=' . urlencode($target_user_name));
                        exit;
                    }
                } catch (PDOException $e) {
                    $error = '1対1メッセージの送信に失敗しました。エラー: ' . $e->getMessage();
                    error_log('1対1チャット送信エラー: ' . $e->getMessage());
                }
            }
        }
    }
}

// チャット履歴取得
if ($selected_group_id) {
    try {
        // まず標準的なテーブル名で試行
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
        try {
            // 代替テーブル名で試行（created_atカラムを使用）
            $stmt = $db->prepare('
                SELECT m.*, u.user_name, m.created_at as sent_at
                FROM group_chat_messages m
                JOIN users u ON m.user_id = u.user_id
                WHERE m.group_id = ?
                ORDER BY m.created_at ASC
            ');
            $stmt->execute([$selected_group_id]);
            $chats = $stmt->fetchAll();
        } catch (PDOException $e2) {
            $error = 'チャット履歴の取得に失敗しました。エラー: ' . $e2->getMessage();
            error_log('グループチャット履歴取得エラー: ' . $e2->getMessage());
            $chats = [];
        }
    }
} else {
    // 1対1チャットの場合
    if ($target_user_id > 0) {
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
            try {
                // created_atカラムを使用した代替クエリ
                $stmt = $db->prepare('
                    SELECT c.*, u.user_name AS from_user_name, c.created_at as sent_at
                    FROM chats c
                    JOIN users u ON c.from_chat = u.user_id
                    WHERE (c.from_chat = ? AND c.to_chat = ?) OR (c.from_chat = ? AND c.to_chat = ?)
                    ORDER BY c.created_at ASC
                ');
                $stmt->execute([$login_user_id, $target_user_id, $target_user_id, $login_user_id]);
                $chats = $stmt->fetchAll();
            } catch (PDOException $e2) {
                $error = 'チャット履歴の取得に失敗しました。エラー: ' . $e2->getMessage();
                error_log('1対1チャット履歴取得エラー: ' . $e2->getMessage());
                $chats = [];
            }
        }
    } else {
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
                
                <!-- 個人チャット相手表示 -->
                <div class="p-3 border-top bg-white">
                    <h6 class="mb-2 text-success">
                        <i class="bi bi-person-circle me-2"></i>個人チャット
                    </h6>
                    
                    <!-- 現在の個人チャット相手表示 -->
                    <?php if (!$selected_group_id && $target_user_name): ?>
                        <div class="d-flex align-items-center p-2 bg-light rounded mb-2">
                            <img src="../main/img/headerImg/account.png" 
                                 style="width: 32px; height: 32px; border-radius: 50%;" 
                                 alt="プロフィール画像">
                            <div class="ms-2">
                                <div class="fw-bold text-primary" style="font-size: 0.9rem;">
                                    <?php echo htmlspecialchars($target_user_name); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- 利用可能なユーザー一覧を常に表示 -->
                    <div class="mt-2">
                        <!-- チャット履歴があるユーザー -->
                        <?php if (!empty($chat_history_users)): ?>
                            <small class="text-muted">最近のチャット:</small>
                            <div class="mt-2 mb-3" style="max-height: 120px; overflow-y: auto;">
                                <?php foreach ($chat_history_users as $chat_user): ?>
                                    <div class="d-flex align-items-center p-1 border rounded mb-1 <?php echo (!$selected_group_id && $target_user_id == $chat_user['user_id']) ? 'bg-primary bg-opacity-10' : ''; ?>" 
                                         style="cursor: pointer;"
                                         onclick="location.href='chat.php?user=<?php echo $chat_user['user_id']; ?>&name=<?php echo urlencode($chat_user['user_name']); ?>'">
                                        <img src="../main/img/headerImg/account.png" 
                                             style="width: 24px; height: 24px; border-radius: 50%;" 
                                             alt="プロフィール画像">
                                        <div class="ms-2 flex-grow-1">
                                            <small class="<?php echo (!$selected_group_id && $target_user_id == $chat_user['user_id']) ? 'text-primary fw-bold' : 'text-dark'; ?>">
                                                <?php echo htmlspecialchars($chat_user['user_name']); ?>
                                            </small>
                                            <div>
                                                <small class="text-muted" style="font-size: 0.7rem;">
                                                    <?php echo date('m/d H:i', strtotime($chat_user['last_chat_time'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="me-1">
                                            <small class="badge bg-success text-white" style="font-size: 0.6rem;">履歴</small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- チャット履歴がないユーザー -->
                        <?php if (!empty($new_users)): ?>
                            <small class="text-muted">その他のユーザー:</small>
                            <div class="mt-2" style="max-height: 100px; overflow-y: auto;">
                                <?php foreach ($new_users as $available_user): ?>
                                    <div class="d-flex align-items-center p-1 border rounded mb-1 <?php echo (!$selected_group_id && $target_user_id == $available_user['user_id']) ? 'bg-primary bg-opacity-10' : ''; ?>" 
                                         style="cursor: pointer;"
                                         onclick="location.href='chat.php?user=<?php echo $available_user['user_id']; ?>&name=<?php echo urlencode($available_user['user_name']); ?>'">
                                        <img src="../main/img/headerImg/account.png" 
                                             style="width: 24px; height: 24px; border-radius: 50%;" 
                                             alt="プロフィール画像">
                                        <div class="ms-2">
                                            <small class="<?php echo (!$selected_group_id && $target_user_id == $available_user['user_id']) ? 'text-primary fw-bold' : 'text-secondary'; ?>">
                                                <?php echo htmlspecialchars($available_user['user_name']); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$selected_group_id && !$target_user_name): ?>
                        <div class="text-center text-muted py-2 mt-2">
                            <small>上からユーザーを選択してください</small>
                        </div>
                    <?php endif; ?>
                </div>
            </nav>

            <!-- チャット画面 -->
            <main class="col-12 col-md-9 col-lg-9 px-0 d-flex flex-column chat-main-area position-relative bg-white">
                
                <!-- エラーメッセージ表示 -->
                <?php if ($error): ?>
                    <div class="alert alert-danger m-3"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <!-- デバッグ情報 (開発時のみ) -->
                <?php if (isset($_GET['debug'])): ?>
                    <div class="alert alert-info m-3">
                        <strong>デバッグ情報:</strong><br>
                        ログインユーザーID: <?php echo $login_user_id; ?><br>
                        選択グループID: <?php echo $selected_group_id; ?><br>
                        ターゲットユーザーID: <?php echo $target_user_id; ?><br>
                        チャット件数: <?php echo count($chats); ?>
                    </div>
                <?php endif; ?>
                
                <!-- チャット履歴 -->
                <div class="flex-grow-1 overflow-auto chat-history p-4 chat-history-scroll bg-light">
                    <?php if (empty($chats)): ?>
                        <div class="text-center text-muted py-5">
                            <h5>まだメッセージがありません</h5>
                            <p>最初のメッセージを送信してみましょう！</p>
                        </div>
                    <?php endif; ?>
                    
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
                    <?php 
                    // グループ作成用に全ユーザーを使用
                    $modal_users = array_merge($chat_history_users, $new_users);
                    ?>
                    <?php foreach ($modal_users as $user): ?>
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