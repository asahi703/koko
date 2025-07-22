<?php
/**
 * 通知システム関数
 */

require_once('dbmanager.php');

/**
 * 基本通知作成
 */
function create_notification($user_id, $from_user_id, $notification_type, $title, $message, $related_id = null) {
    try {
        $db = new cdb();
        $stmt = $db->prepare('
            INSERT INTO notifications (user_id, from_user_id, notification_type, title, message, related_id, is_read, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 0, NOW())
        ');
        return $stmt->execute([$user_id, $from_user_id, $notification_type, $title, $message, $related_id]);
    } catch (PDOException $e) {
        error_log('通知作成エラー: ' . $e->getMessage());
        return false;
    }
}

/**
 * グループチャット通知
 */
function notify_group_chat_message($group_id, $sender_id, $sender_name, $message_preview) {
    try {
        $db = new cdb();
        
        // グループ情報を取得
        $stmt = $db->prepare('SELECT group_name FROM group_chats WHERE group_id = ?');
        $stmt->execute([$group_id]);
        $group_info = $stmt->fetch();
        
        if (!$group_info) {
            error_log("グループが見つかりません: group_id=$group_id");
            return false;
        }
        
        // グループメンバーを取得（送信者以外）
        $stmt = $db->prepare('
            SELECT user_id FROM group_chat_members WHERE group_id = ? AND user_id != ?
        ');
        $stmt->execute([$group_id, $sender_id]);
        $members = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // 各メンバーに通知
        foreach ($members as $member_id) {
            $title = 'グループチャット - ' . $group_info['group_name'];
            $msg = $sender_name . 'さんからメッセージ: ' . mb_substr($message_preview, 0, 30) . '...';
            create_notification($member_id, $sender_id, 'group_chat_message', $title, $msg, $group_id);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log('グループチャット通知エラー: ' . $e->getMessage());
        return false;
    }
}
?>
