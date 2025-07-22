<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once('common/dbmanager.php');
    $db = new cdb();
    
    echo "=== 最終調整 ===" . PHP_EOL;
    
    // 既存のメンバーテーブルをクリア
    $db->exec('DELETE FROM group_chat_members');
    
    // 各グループに適切なメンバーを追加
    // グループ1 (ko) に全ユーザーを追加
    $users = [1, 2, 3]; // ユーザーID 1, 2, 3
    
    foreach ($users as $user_id) {
        $stmt = $db->prepare('INSERT INTO group_chat_members (group_id, user_id) VALUES (?, ?)');
        $stmt->execute([1, $user_id]);
        echo "グループ1 (ko) にユーザー {$user_id} を追加" . PHP_EOL;
    }
    
    foreach ($users as $user_id) {
        $stmt = $db->prepare('INSERT INTO group_chat_members (group_id, user_id) VALUES (?, ?)');
        $stmt->execute([2, $user_id]);
        echo "グループ2 (ijhenfkif) にユーザー {$user_id} を追加" . PHP_EOL;
    }
    
    echo "=== 最終確認 ===" . PHP_EOL;
    
    // グループとメンバーの確認
    $stmt = $db->query('
        SELECT g.group_id, g.group_name, COUNT(m.user_id) as member_count
        FROM group_chats g
        LEFT JOIN group_chat_members m ON g.group_id = m.group_id
        GROUP BY g.group_id, g.group_name
    ');
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "グループID={$row['group_id']}, 名前={$row['group_name']}, メンバー数={$row['member_count']}" . PHP_EOL;
    }
    
    echo "修正完了！グループチャットが正常に動作するはずです。" . PHP_EOL;
    
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . PHP_EOL;
}
?>
