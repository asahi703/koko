<?php
require_once('../common/session.php');
require_once('../common/dbmanager.php');

header('Content-Type: application/json');

$user = get_login_user();
if (!$user) {
    echo json_encode(['success' => false, 'error' => 'ログインが必要です']);
    exit;
}

// POSTデータを取得
$input = json_decode(file_get_contents('php://input'), true);
$notification_id = $input['notification_id'] ?? null;

if (!$notification_id) {
    echo json_encode(['success' => false, 'error' => '通知IDが指定されていません']);
    exit;
}

try {
    $db = new cdb();
    
    // 通知を既読にする
    $stmt = $db->prepare('
        UPDATE notifications 
        SET is_read = 1 
        WHERE notification_id = ? AND user_id = ?
    ');
    
    $user_id = $user['user_id'] ?? $user['uuid'];
    $stmt->execute([$notification_id, $user_id]);
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    error_log('通知既読マークエラー: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => '既読マークに失敗しました'
    ]);
}
?>
