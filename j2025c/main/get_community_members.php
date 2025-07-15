<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

header('Content-Type: application/json');

$user = get_login_user();
if (!$user) {
    echo json_encode(['success' => false, 'error' => 'ログインが必要です']);
    exit;
}

$community_id = $_GET['community_id'] ?? '';
if (empty($community_id)) {
    echo json_encode(['success' => false, 'error' => 'コミュニティIDが指定されていません']);
    exit;
}

try {
    $db = new cdb();
    
    // コミュニティ情報を取得
    $stmt = $db->prepare('SELECT * FROM communities WHERE community_id = ?');
    $stmt->execute([$community_id]);
    $community = $stmt->fetch();
    
    if (!$community) {
        echo json_encode(['success' => false, 'error' => 'コミュニティが見つかりません']);
        exit;
    }
    
    // ユーザーIDを適切に取得（uuidまたはuser_idのどちらかを使用）
    $user_id = $user['user_id'] ?? $user['uuid'] ?? null;
    if (!$user_id) {
        echo json_encode(['success' => false, 'error' => 'ユーザーIDが取得できません']);
        exit;
    }
    
    // ユーザーがそのコミュニティに参加しているか確認
    $stmt = $db->prepare('SELECT 1 FROM community_users WHERE user_id = ? AND community_id = ?');
    $stmt->execute([$user_id, $community_id]);
    $is_member = $stmt->fetch();
    
    $is_owner = ($community['community_owner'] == $user_id);
    
    if (!$is_member && !$is_owner) {
        echo json_encode(['success' => false, 'error' => 'このコミュニティのメンバー情報を見る権限がありません']);
        exit;
    }
    
    $members = [];
    
    // 1. オーナー情報を取得（現在のユーザーでない場合のみ）
    if ($community['community_owner'] != $user_id) {
        $stmt = $db->prepare('SELECT user_id, user_name, user_mailaddress as user_email, user_is_teacher FROM users WHERE user_id = ?');
        $stmt->execute([$community['community_owner']]);
        $owner = $stmt->fetch();
        
        if ($owner) {
            $owner['is_owner'] = true;
            $members[] = $owner;
        }
    }
    
    // 2. メンバー情報を取得（オーナーと現在のユーザー以外）
    $stmt = $db->prepare(
        'SELECT u.user_id, u.user_name, u.user_mailaddress as user_email, u.user_is_teacher
         FROM users u
         JOIN community_users cu ON u.user_id = cu.user_id
         WHERE cu.community_id = ? AND u.user_id != ? AND u.user_id != ?
         ORDER BY u.user_is_teacher DESC, u.user_name'
    );
    $stmt->execute([$community_id, $community['community_owner'], $user_id]);
    $community_members = $stmt->fetchAll();
    
    foreach ($community_members as $member) {
        $member['is_owner'] = false;
        $members[] = $member;
    }
    
    echo json_encode([
        'success' => true,
        'members' => $members,
        'community' => $community
    ]);
    
} catch (PDOException $e) {
    // デバッグ用：実際のエラーメッセージを返す（本番環境では削除）
    echo json_encode(['success' => false, 'error' => 'データベースエラー: ' . $e->getMessage()]);
}
?>
