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

/**
 * 個人チャット通知
 */
function notify_direct_message($target_user_id, $sender_id, $sender_name, $message_preview) {
    try {
        $db = new cdb();
        
        // 送信者の情報を確認
        $stmt = $db->prepare('SELECT user_name FROM users WHERE user_id = ?');
        $stmt->execute([$sender_id]);
        $sender_info = $stmt->fetch();
        
        if (!$sender_info) {
            error_log("送信者が見つかりません: sender_id=$sender_id");
            return false;
        }
        
        // 個人チャット通知を作成
        $title = '個人メッセージ - ' . $sender_name;
        $msg = $sender_name . 'さんからメッセージ: ' . mb_substr($message_preview, 0, 30) . '...';
        
        create_notification($target_user_id, $sender_id, 'direct_message', $title, $msg, $sender_id);
        
        return true;
    } catch (PDOException $e) {
        error_log('個人チャット通知エラー: ' . $e->getMessage());
        return false;
    }
}

/**
 * クラスチャット通知（コミュニティチャット）
 */
function notify_chat_message($class_id, $sender_id, $sender_name, $message_preview) {
    try {
        $db = new cdb();
        
        // クラス情報を取得
        $stmt = $db->prepare('
            SELECT c.class_name, com.community_name 
            FROM classes c 
            JOIN communities com ON c.class_community = com.community_id 
            WHERE c.class_id = ?
        ');
        $stmt->execute([$class_id]);
        $class_info = $stmt->fetch();
        
        if (!$class_info) {
            error_log("クラスが見つかりません: class_id=$class_id");
            return false;
        }
        
        // クラスメンバーを取得（送信者以外）
        $stmt = $db->prepare('
            SELECT DISTINCT cu.user_id 
            FROM community_users cu
            JOIN classes c ON cu.community_id = c.class_community
            WHERE c.class_id = ? AND cu.user_id != ?
        ');
        $stmt->execute([$class_id, $sender_id]);
        $members = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // 各メンバーに通知
        foreach ($members as $member_id) {
            $title = 'クラスチャット - ' . $class_info['class_name'];
            $msg = $sender_name . 'さんからメッセージ: ' . mb_substr($message_preview, 0, 30) . '...';
            create_notification($member_id, $sender_id, 'class_chat_message', $title, $msg, $class_id);
        }
        
        return true;
    } catch (PDOException $e) {
        error_log('クラスチャット通知エラー: ' . $e->getMessage());
        return false;
    }
}
?>
