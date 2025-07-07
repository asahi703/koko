<?php
// DB接続
$dsn = 'mysql:host=localhost;dbname=j2025cdb;charset=utf8mb4';
$user = 'root';
$password = '';
try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    exit('DB接続エラー: ' . $e->getMessage());
}

// 仮のユーザー情報（本来はセッションやGET/POSTから取得）
$login_user_id = 8; // ログインユーザー
$target_user_id = 9; // 宛先ユーザー

// 選択中グループID取得
$selected_group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// グループ一覧取得
$group_stmt = $pdo->query('SELECT group_id, group_name FROM group_chats');
$group_list = $group_stmt->fetchAll(PDO::FETCH_ASSOC);

// グループ参加者一覧取得
$user_stmt = $pdo->prepare('SELECT user_id, user_name FROM users WHERE user_id != ?');
$user_stmt->execute([$login_user_id]);
$all_users = $user_stmt->fetchAll(PDO::FETCH_ASSOC);

// 新規グループ作成
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_name'], $_POST['group_members'])) {
    $group_name = trim($_POST['group_name']);
    $group_members = $_POST['group_members'];
    if ($group_name !== '' && is_array($group_members) && count($group_members) > 0) {
        // 1. group_chatsにグループ名を追加
        $stmt = $pdo->prepare('INSERT INTO group_chats (group_name) VALUES (?)');
        $stmt->execute([$group_name]);
        $group_id = $pdo->lastInsertId();

        // 2. group_chat_membersに自分と選択ユーザーを追加
        $members = array_unique(array_merge([$login_user_id], $group_members));
        $mem_stmt = $pdo->prepare('INSERT INTO group_chat_members (group_id, user_id) VALUES (?, ?)');
        foreach ($members as $uid) {
            $mem_stmt->execute([$group_id, $uid]);
        }
        header('Location: chat.php?group_id=' . $group_id);
        exit;
    }
}

// メッセージ送信
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message) && $selected_group_id) {
        $stmt = $pdo->prepare('INSERT INTO group_chat_messages (group_id, user_id, message) VALUES (?, ?, ?)');
        $stmt->execute([$selected_group_id, $login_user_id, $message]);
        header('Location: chat.php?group_id=' . $selected_group_id);
        exit;
    }
}

// チャット履歴取得
if ($selected_group_id) {
    $stmt = $pdo->prepare('
        SELECT m.*, u.user_name
        FROM group_chat_messages m
        JOIN users u ON m.user_id = u.user_id
        WHERE m.group_id = ?
        ORDER BY m.sent_at ASC
    ');
    $stmt->execute([$selected_group_id]);
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // 1対1チャットの場合
    $stmt = $pdo->prepare('
        SELECT c.*, u.user_name AS from_user_name
        FROM chats c
        JOIN users u ON c.from_chat = u.user_id
        WHERE (c.from_chat = ? AND c.to_chat = ?) OR (c.from_chat = ? AND c.to_chat = ?)
        ORDER BY c.sent_at ASC
    ');
    $stmt->execute([$login_user_id, $target_user_id, $target_user_id, $login_user_id]);
    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// メッセージ送信処理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        if ($selected_group_id) {
            // グループチャットの場合
            // chat_groupsからchat_idを取得
            $stmt = $pdo->prepare('SELECT chat_id FROM chat_groups WHERE group_id = ?');
            $stmt->execute([$selected_group_id]);
            $group = $stmt->fetch(PDO::FETCH_ASSOC);
            $group_chat_id = $group ? $group['chat_id'] : 0;

            // グループ参加者を取得
            $stmt = $pdo->prepare('SELECT user_id FROM chat_recipients WHERE chat_id = ?');
            $stmt->execute([$group_chat_id]);
            $recipients = $stmt->fetchAll(PDO::FETCH_COLUMN);

            // 参加者全員分INSERT（自分以外にも送信）
            $stmt = $pdo->prepare('INSERT INTO chats (chat_text, from_chat, to_chat) VALUES (?, ?, ?)');
            foreach ($recipients as $to_user_id) {
                $stmt->execute([$message, $login_user_id, $to_user_id]);
            }
            header('Location: chat.php?group_id=' . $selected_group_id);
            exit;
        } else {
            // 1対1チャットの場合
            $stmt = $pdo->prepare('INSERT INTO chats (chat_text, from_chat, to_chat) VALUES (?, ?, ?)');
            $stmt->execute([$message, $login_user_id, $target_user_id]);
            header('Location: chat.php');
            exit;
        }
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<div class="main-content-wrapper">
    <div class="container-fluid h-100">
        <div class="row vh-100 gx-0">
            <!-- グループ選択サイドバー -->
            <nav class="col-12 col-md-3 col-lg-3 px-0 group-sidebar d-flex flex-column">
                <div class="p-3 border-bottom">
                    <h5 class="mb-3">グループ</h5>
                    <!-- 新規グループ作成ボタン -->
                    <button class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#createGroupModal">＋ 新規グループ</button>
                </div>
                <ul class="list-group list-group-flush flex-grow-1 overflow-auto group-list-scroll">
                    <?php foreach ($group_list as $group): ?>
                        <li class="list-group-item list-group-item-action<?php if ($selected_group_id == $group['group_id']) echo ' active'; ?>"
                            onclick="location.href='chat.php?group_id=<?php echo $group['group_id']; ?>'">
                            <?php echo htmlspecialchars($group['group_name']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

            <!-- チャット画面 -->
            <main class="col-12 col-md-9 col-lg-9 px-0 d-flex flex-column chat-main-area position-relative">
                <!-- チャット履歴 -->
                <div class="flex-grow-1 overflow-auto chat-history p-4 chat-history-scroll">
                    <?php foreach ($chats as $chat): ?>
                        <?php
                        // グループチャットかどうかでキーを分岐
                        $is_group = $selected_group_id && isset($chat['user_name']);
                        $user_name = $is_group ? $chat['user_name'] : ($chat['from_user_name'] ?? '');
                        $message_text = $is_group ? $chat['message'] : ($chat['chat_text'] ?? '');
                        $from_id = $is_group ? $chat['user_id'] : ($chat['from_chat'] ?? 0);
                        $sent_at = $is_group ? $chat['sent_at'] : ($chat['sent_at'] ?? '');
                        ?>
                        <div class="d-flex <?php echo $from_id == $login_user_id ? 'justify-content-end' : 'justify-content-start'; ?> mb-2">
                            <div class="chat-msg <?php echo $from_id == $login_user_id ? 'chat-msg-sbm' : 'bg-white'; ?> border rounded p-2">
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
                <form class="chat-input-box border-top p-3 bg-white" method="post" autocomplete="off">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control rounded-pill" placeholder="メッセージを入力" required>
                        <button type="submit" class="btn btn-primary rounded-pill ms-2">
                            送信
                        </button>
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
            <input class="form-check-input" type="checkbox" value="<?php echo $login_user_id; ?>" id="user_self" checked disabled>
            <label class="form-check-label" for="user_self">自分</label>
          </div>
          <?php foreach ($all_users as $user): ?>
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="group_members[]" value="<?php echo $user['user_id']; ?>" id="user_<?php echo $user['user_id']; ?>">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
