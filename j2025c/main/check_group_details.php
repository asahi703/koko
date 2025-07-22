<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once('common/dbmanager.php');
    $db = new cdb();
    
    echo "=== group_chatsテーブルの詳細確認 ===" . PHP_EOL;
    try {
        $stmt = $db->query('DESCRIBE group_chats');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . ' | ' . $row['Key'] . ' | ' . $row['Default'] . ' | ' . $row['Extra'] . PHP_EOL;
        }
    } catch (Exception $e) {
        echo 'group_chatsテーブル確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "=== 既存のグループ一覧 ===" . PHP_EOL;
    try {
        $stmt = $db->query('SELECT * FROM group_chats');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } catch (Exception $e) {
        echo 'グループ一覧確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "=== グループチャットメンバー確認 ===" . PHP_EOL;
    try {
        $stmt = $db->query('SELECT * FROM group_chat_members');
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            print_r($row);
        }
    } catch (Exception $e) {
        echo 'メンバー確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'データベース接続エラー: ' . $e->getMessage() . PHP_EOL;
}
?>
