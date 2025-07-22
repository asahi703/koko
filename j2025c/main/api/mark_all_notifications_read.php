<?php
require_once('../common/session.php');
require_once('../common/dbmanager.php');

header('Content-Type: application/json');

$user = get_login_user();
if (!$user) {
    echo json_encode(['success' => false, 'error' => 'ログインが必要です']);
    exit;
}

try {
    $db = new cdb();
    
    // すべての通知を既読にする
    $stmt = $db->prepare('
        UPDATE notifications 
        SET is_read = 1 
        WHERE user_id = ? AND is_read = 0
    ');
    
    $user_id = $user['user_id'] ?? $user['uuid'];
    $stmt->execute([$user_id]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    error_log('全通知既読マークエラー: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => '全既読マークに失敗しました'
    ]);
}
?>
