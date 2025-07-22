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
    
    // 最新の通知を取得（最大10件）
    $stmt = $db->prepare('
        SELECT 
            n.notification_id,
            n.from_user_id,
            n.notification_type,
            n.title,
            n.message,
            n.related_id,
            n.is_read,
            n.created_at,
            u.user_name as from_user_name,
            u.user_id as from_user_id_actual
        FROM notifications n
        LEFT JOIN users u ON n.from_user_id = u.user_id
        WHERE n.user_id = ? 
        ORDER BY n.created_at DESC 
        LIMIT 10
    ');
    
    $user_id = $user['user_id'] ?? $user['uuid'];
    $stmt->execute([$user_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'notifications' => $notifications
    ]);
    
} catch (PDOException $e) {
    error_log('通知取得エラー: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => '通知の取得に失敗しました'
    ]);
}
?>
