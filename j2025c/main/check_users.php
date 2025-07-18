<?php
require_once('common/dbmanager.php');

try {
    $db = new cdb();
    
    echo "現在のユーザー情報とアイコン状態:\n";
    $stmt = $db->prepare("SELECT user_id, user_name, user_icon FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        $icon_status = empty($user['user_icon']) ? 'なし' : $user['user_icon'];
        echo "ID: {$user['user_id']}, 名前: {$user['user_name']}, アイコン: {$icon_status}\n";
    }
    
    // 空のファイル名や不正なアイコンパスを修正
    echo "\n不正なアイコンパスを修正中...\n";
    $stmt = $db->prepare("UPDATE users SET user_icon = NULL WHERE user_icon LIKE 'img/user_icons/.%' OR user_icon = '' OR user_icon IS NULL");
    $result = $stmt->execute();
    
    if ($result) {
        echo "不正なアイコンパスを修正しました。\n";
    }
    
    echo "\n修正後のユーザー情報:\n";
    $stmt = $db->prepare("SELECT user_id, user_name, user_icon FROM users WHERE user_icon IS NOT NULL");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        echo "ID: {$user['user_id']}, 名前: {$user['user_name']}, アイコン: {$user['user_icon']}\n";
    }
    
} catch (PDOException $e) {
    echo "エラー: " . $e->getMessage() . "\n";
}
?>
