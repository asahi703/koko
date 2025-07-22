<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once('common/dbmanager.php');
    $db = new cdb();
    
    echo "=== chatsテーブルの構造確認 ===" . PHP_EOL;
    
    // chatsテーブルの構造確認
    try {
        $stmt = $db->query('DESCRIBE chats');
        echo "chatsテーブルのカラム:" . PHP_EOL;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  - {$row['Field']} ({$row['Type']}) | NULL: {$row['Null']} | KEY: {$row['Key']} | DEFAULT: {$row['Default']}" . PHP_EOL;
        }
    } catch (Exception $e) {
        echo "chatsテーブル構造確認エラー: " . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL . "=== 1対1チャットテストクエリ ===" . PHP_EOL;
    
    // テストクエリ1: sent_atカラムありの場合
    try {
        $stmt = $db->prepare('
            SELECT c.*, u.user_name AS from_user_name
            FROM chats c
            JOIN users u ON c.from_chat = u.user_id
            WHERE (c.from_chat = ? AND c.to_chat = ?) OR (c.from_chat = ? AND c.to_chat = ?)
            ORDER BY c.sent_at ASC
            LIMIT 3
        ');
        $stmt->execute([1, 3, 3, 1]); // admin@mail と kusu の間
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "sent_atカラム使用クエリ結果:" . PHP_EOL;
        foreach ($results as $row) {
            echo "  - {$row['from_user_name']}: {$row['chat_text']} ({$row['sent_at']})" . PHP_EOL;
        }
        
    } catch (Exception $e) {
        echo "sent_atカラム使用クエリエラー: " . $e->getMessage() . PHP_EOL;
        
        // テストクエリ2: created_atカラムの場合
        try {
            $stmt = $db->prepare('
                SELECT c.*, u.user_name AS from_user_name, c.created_at as sent_at
                FROM chats c
                JOIN users u ON c.from_chat = u.user_id
                WHERE (c.from_chat = ? AND c.to_chat = ?) OR (c.from_chat = ? AND c.to_chat = ?)
                ORDER BY c.created_at ASC
                LIMIT 3
            ');
            $stmt->execute([1, 3, 3, 1]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "created_atカラム使用クエリ結果:" . PHP_EOL;
            foreach ($results as $row) {
                echo "  - {$row['from_user_name']}: {$row['chat_text']} ({$row['sent_at']})" . PHP_EOL;
            }
            
        } catch (Exception $e2) {
            echo "created_atカラム使用クエリエラー: " . $e2->getMessage() . PHP_EOL;
        }
    }
    
    echo PHP_EOL . "=== 全1対1チャットデータ ===" . PHP_EOL;
    try {
        $stmt = $db->query('SELECT * FROM chats ORDER BY chat_id DESC LIMIT 5');
        $all_chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($all_chats as $chat) {
            echo "チャットID: {$chat['chat_id']}, 送信者: {$chat['from_chat']}, 受信者: {$chat['to_chat']}, メッセージ: {$chat['chat_text']}" . PHP_EOL;
            if (isset($chat['sent_at'])) {
                echo "  送信時刻(sent_at): {$chat['sent_at']}" . PHP_EOL;
            }
            if (isset($chat['created_at'])) {
                echo "  作成時刻(created_at): {$chat['created_at']}" . PHP_EOL;
            }
        }
    } catch (Exception $e) {
        echo "全チャットデータ取得エラー: " . $e->getMessage() . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'データベース接続エラー: ' . $e->getMessage() . PHP_EOL;
}
?>
