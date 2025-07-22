<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once('common/dbmanager.php');
    $db = new cdb();
    
    echo "=== グループチャットテーブル修正開始 ===" . PHP_EOL;
    
    // 1. 既存データのバックアップ
    echo "既存データをバックアップ中..." . PHP_EOL;
    $backup_groups = [];
    $backup_members = [];
    $backup_messages = [];
    
    // グループデータのバックアップ
    $stmt = $db->query('SELECT * FROM group_chats');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $backup_groups[] = $row;
    }
    
    // メンバーデータのバックアップ
    $stmt = $db->query('SELECT * FROM group_chat_members');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $backup_members[] = $row;
    }
    
    // メッセージデータのバックアップ
    try {
        $stmt = $db->query('SELECT * FROM group_chat_messages');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $backup_messages[] = $row;
        }
    } catch (Exception $e) {
        echo "メッセージテーブルが空または存在しません" . PHP_EOL;
    }
    
    echo "バックアップ完了 - グループ数: " . count($backup_groups) . ", メンバー数: " . count($backup_members) . ", メッセージ数: " . count($backup_messages) . PHP_EOL;
    
    // 2. テーブルを削除して再作成
    echo "テーブルを再作成中..." . PHP_EOL;
    
    // 外部キー制約を無効化
    $db->exec('SET FOREIGN_KEY_CHECKS = 0');
    
    // テーブル削除
    $db->exec('DROP TABLE IF EXISTS group_chat_messages');
    $db->exec('DROP TABLE IF EXISTS group_chat_members');
    $db->exec('DROP TABLE IF EXISTS group_chats');
    
    // テーブル再作成
    $db->exec('
        CREATE TABLE group_chats (
            group_id INT PRIMARY KEY AUTO_INCREMENT,
            group_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ');
    
    $db->exec('
        CREATE TABLE group_chat_members (
            group_id INT,
            user_id INT,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (group_id, user_id),
            FOREIGN KEY (group_id) REFERENCES group_chats(group_id) ON DELETE CASCADE
        )
    ');
    
    $db->exec('
        CREATE TABLE group_chat_messages (
            message_id INT PRIMARY KEY AUTO_INCREMENT,
            group_id INT,
            user_id INT,
            message TEXT NOT NULL,
            sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (group_id) REFERENCES group_chats(group_id) ON DELETE CASCADE
        )
    ');
    
    // 外部キー制約を有効化
    $db->exec('SET FOREIGN_KEY_CHECKS = 1');
    
    echo "テーブル再作成完了" . PHP_EOL;
    
    // 3. ユニークなグループ名でデータを復元
    echo "データを復元中..." . PHP_EOL;
    
    $unique_groups = [];
    foreach ($backup_groups as $group) {
        if (!in_array($group['group_name'], array_column($unique_groups, 'group_name'))) {
            $unique_groups[] = $group;
        }
    }
    
    $group_id_mapping = [];
    foreach ($unique_groups as $group) {
        $stmt = $db->prepare('INSERT INTO group_chats (group_name) VALUES (?)');
        $stmt->execute([$group['group_name']]);
        $new_group_id = $db->lastInsertId();
        $group_id_mapping[0] = $new_group_id; // 全て0だったので新しいIDにマッピング
        echo "グループ '{$group['group_name']}' を ID {$new_group_id} で復元" . PHP_EOL;
    }
    
    // メンバーデータの復元（重複除去）
    $inserted_members = [];
    foreach ($backup_members as $member) {
        $key = $group_id_mapping[0] . '_' . $member['user_id'];
        if (!in_array($key, $inserted_members)) {
            $stmt = $db->prepare('INSERT INTO group_chat_members (group_id, user_id) VALUES (?, ?)');
            $stmt->execute([$group_id_mapping[0], $member['user_id']]);
            $inserted_members[] = $key;
        }
    }
    
    // メッセージデータの復元
    foreach ($backup_messages as $message) {
        $stmt = $db->prepare('INSERT INTO group_chat_messages (group_id, user_id, message, sent_at) VALUES (?, ?, ?, ?)');
        $stmt->execute([$group_id_mapping[0], $message['user_id'], $message['message'], $message['sent_at']]);
    }
    
    echo "データ復元完了" . PHP_EOL;
    
    // 4. 確認
    echo "=== 修正後の確認 ===" . PHP_EOL;
    $stmt = $db->query('SELECT * FROM group_chats');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "グループ: ID={$row['group_id']}, 名前={$row['group_name']}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . PHP_EOL;
}
?>
