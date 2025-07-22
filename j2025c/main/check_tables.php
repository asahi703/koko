<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "スクリプト開始\n";

try {
    echo "dbmanager.phpをrequire中...\n";
    require_once('common/dbmanager.php');
    echo "dbmanager.php読み込み完了\n";
    $db = new cdb();
    
    // グループチャット関連のテーブルの存在確認
    echo 'グループチャット関連テーブルの確認:' . PHP_EOL;
    
    // group_chatsテーブル
    try {
        $stmt = $db->query('SHOW TABLES LIKE "group_chats"');
        $result = $stmt->fetch();
        if ($result) {
            echo '✓ group_chatsテーブル: 存在' . PHP_EOL;
            $stmt = $db->query('DESCRIBE group_chats');
            echo 'カラム構造:' . PHP_EOL;
            while ($row = $stmt->fetch()) {
                echo '  - ' . $row['Field'] . ' (' . $row['Type'] . ')' . PHP_EOL;
            }
        } else {
            echo '✗ group_chatsテーブル: 存在しません' . PHP_EOL;
        }
    } catch (Exception $e) {
        echo '✗ group_chatsテーブル確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL;
    
    // group_chat_membersテーブル
    try {
        $stmt = $db->query('SHOW TABLES LIKE "group_chat_members"');
        $result = $stmt->fetch();
        if ($result) {
            echo '✓ group_chat_membersテーブル: 存在' . PHP_EOL;
            $stmt = $db->query('DESCRIBE group_chat_members');
            echo 'カラム構造:' . PHP_EOL;
            while ($row = $stmt->fetch()) {
                echo '  - ' . $row['Field'] . ' (' . $row['Type'] . ')' . PHP_EOL;
            }
        } else {
            echo '✗ group_chat_membersテーブル: 存在しません' . PHP_EOL;
        }
    } catch (Exception $e) {
        echo '✗ group_chat_membersテーブル確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL;
    
    // group_chat_messagesテーブル
    try {
        $stmt = $db->query('SHOW TABLES LIKE "group_chat_messages"');
        $result = $stmt->fetch();
        if ($result) {
            echo '✓ group_chat_messagesテーブル: 存在' . PHP_EOL;
            $stmt = $db->query('DESCRIBE group_chat_messages');
            echo 'カラム構造:' . PHP_EOL;
            while ($row = $stmt->fetch()) {
                echo '  - ' . $row['Field'] . ' (' . $row['Type'] . ')' . PHP_EOL;
            }
        } else {
            echo '✗ group_chat_messagesテーブル: 存在しません' . PHP_EOL;
        }
    } catch (Exception $e) {
        echo '✗ group_chat_messagesテーブル確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
    echo PHP_EOL;
    
    // 既存のグループ数を確認
    try {
        $stmt = $db->query('SELECT COUNT(*) as count FROM group_chats');
        $count = $stmt->fetch();
        echo 'グループ数: ' . $count['count'] . PHP_EOL;
    } catch (Exception $e) {
        echo 'グループ数確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
    // 全テーブル一覧を表示
    echo PHP_EOL . '全テーブル一覧:' . PHP_EOL;
    try {
        $stmt = $db->query('SHOW TABLES');
        while ($row = $stmt->fetch()) {
            echo '  - ' . $row[0] . PHP_EOL;
        }
    } catch (Exception $e) {
        echo 'テーブル一覧確認エラー: ' . $e->getMessage() . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'データベース接続エラー: ' . $e->getMessage() . PHP_EOL;
}
?>
