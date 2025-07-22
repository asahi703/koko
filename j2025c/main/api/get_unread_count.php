<?php
require_once('../common/session.php');
require_once('../common/dbmanager.php');

header('Content-Type: application/json');

$user = get_login_user();
if (!$user) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit;
}

try {
    $db = new cdb();
    
    // 未読通知数を取得
    $stmt = $db->prepare('
        SELECT COUNT(*) as count 
        FROM notifications 
        WHERE user_id = ? AND is_read = 0
    ');
    
    $user_id = $user['user_id'] ?? $user['uuid'];
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true,
        'count' => (int)$result['count']
    ]);
    
} catch (PDOException $e) {
    error_log('未読通知数取得エラー: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'count' => 0
    ]);
}
?>
