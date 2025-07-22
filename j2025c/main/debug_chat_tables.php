<?php
require_once('common/dbmanager.php');

echo "<h3>チャット関連テーブル確認</h3>";

try {
    $db = new cdb();
    
    // 利用可能なテーブル一覧を取得
    echo "<h4>利用可能なテーブル:</h4>";
    $stmt = $db->prepare('SHOW TABLES');
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
        
        // チャット関連のテーブルの場合、構造も表示
        if (stripos($table, 'chat') !== false || stripos($table, 'group') !== false) {
            echo "<ul>";
            $desc_stmt = $db->prepare("DESCRIBE `$table`");
            $desc_stmt->execute();
            $columns = $desc_stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $column) {
                echo "<li>{$column['Field']} ({$column['Type']})</li>";
            }
            echo "</ul>";
        }
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo 'エラー: ' . $e->getMessage() . "<br>";
}
?>
