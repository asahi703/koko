<?php
require_once('common/dbmanager.php');
require_once('common/session.php');

// 現在のログインユーザー情報を確認
$user = get_login_user();
echo "セッション内ユーザー情報:\n";
print_r($user);

// データベース内のユーザー情報を確認
try {
    $db = new cdb();
    
    // 全ユーザーの情報を確認
    echo "\n=== データベース内のユーザー情報 ===\n";
    $stmt = $db->prepare("SELECT user_id, user_name, user_mailaddress, user_icon FROM users ORDER BY user_id");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    foreach ($users as $user_db) {
        echo "ID: {$user_db['user_id']}, 名前: {$user_db['user_name']}, アイコン: " . ($user_db['user_icon'] ?? '(なし)') . "\n";
        
        // アイコンファイルの存在確認
        if (!empty($user_db['user_icon'])) {
            $file_path = __DIR__ . '/../' . $user_db['user_icon'];
            echo "  ファイルパス: {$file_path}\n";
            echo "  ファイル存在: " . (file_exists($file_path) ? 'はい' : 'いいえ') . "\n";
        }
        echo "\n";
    }
    
    // 現在のログインユーザーのIDでデータベースから情報を取得
    if ($user && isset($user['user_id'])) {
        echo "=== 現在のログインユーザーのデータベース情報 ===\n";
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $current_user_db = $stmt->fetch();
        print_r($current_user_db);
    }
    
} catch (PDOException $e) {
    echo "データベースエラー: " . $e->getMessage() . "\n";
}
?>
