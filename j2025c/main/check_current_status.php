<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once('common/dbmanager.php');
    $db = new cdb();
    
    echo "=== 現在のグループチャット状況確認 ===" . PHP_EOL;
    
    // 1. グループ一覧
    echo "【グループ一覧】" . PHP_EOL;
    $stmt = $db->query('SELECT * FROM group_chats ORDER BY group_id');
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($groups as $group) {
        echo "- ID: {$group['group_id']}, 名前: {$group['group_name']}" . PHP_EOL;
    }
    
    echo PHP_EOL;
    
    // 2. 各グループのメンバー
    echo "【各グループのメンバー】" . PHP_EOL;
    foreach ($groups as $group) {
        echo "グループ: {$group['group_name']} (ID: {$group['group_id']})" . PHP_EOL;
        $stmt = $db->prepare('
            SELECT m.user_id, u.user_name 
            FROM group_chat_members m 
            JOIN users u ON m.user_id = u.user_id 
            WHERE m.group_id = ?
        ');
        $stmt->execute([$group['group_id']]);
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($members)) {
            echo "  メンバーなし" . PHP_EOL;
        } else {
            foreach ($members as $member) {
                echo "  - {$member['user_name']} (ID: {$member['user_id']})" . PHP_EOL;
            }
        }
        echo PHP_EOL;
    }
    
    // 3. ユーザー一覧
    echo "【全ユーザー一覧】" . PHP_EOL;
    $stmt = $db->query('SELECT user_id, user_name FROM users ORDER BY user_id');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($users as $user) {
        echo "- ID: {$user['user_id']}, 名前: {$user['user_name']}" . PHP_EOL;
    }
    
    echo PHP_EOL;
    
    // 4. グループチャットメッセージ
    echo "【グループチャットメッセージ】" . PHP_EOL;
    foreach ($groups as $group) {
        echo "グループ: {$group['group_name']} (ID: {$group['group_id']})" . PHP_EOL;
        $stmt = $db->prepare('
            SELECT m.*, u.user_name 
            FROM group_chat_messages m 
            JOIN users u ON m.user_id = u.user_id 
            WHERE m.group_id = ? 
            ORDER BY m.sent_at DESC 
            LIMIT 3
        ');
        $stmt->execute([$group['group_id']]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($messages)) {
            echo "  メッセージなし" . PHP_EOL;
        } else {
            foreach ($messages as $msg) {
                $time = date('Y-m-d H:i:s', strtotime($msg['sent_at']));
                echo "  - {$msg['user_name']}: {$msg['message']} ({$time})" . PHP_EOL;
            }
        }
        echo PHP_EOL;
    }
    
    // 5. 1対1チャットメッセージ
    echo "【1対1チャットメッセージ (最新5件)】" . PHP_EOL;
    try {
        $stmt = $db->query('
            SELECT c.*, u1.user_name as from_name, u2.user_name as to_name 
            FROM chats c 
            JOIN users u1 ON c.from_chat = u1.user_id 
            JOIN users u2 ON c.to_chat = u2.user_id 
            ORDER BY c.sent_at DESC 
            LIMIT 5
        ');
        $direct_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($direct_messages)) {
            echo "  1対1メッセージなし" . PHP_EOL;
        } else {
            foreach ($direct_messages as $msg) {
                $time = isset($msg['sent_at']) ? date('Y-m-d H:i:s', strtotime($msg['sent_at'])) : '不明';
                echo "  - {$msg['from_name']} → {$msg['to_name']}: {$msg['chat_text']} ({$time})" . PHP_EOL;
            }
        }
    } catch (Exception $e) {
        echo "  1対1チャット確認エラー: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "=== 確認完了 ===" . PHP_EOL;
    
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . PHP_EOL;
}
?>
