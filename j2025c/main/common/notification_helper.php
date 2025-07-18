<?php
/**
 * 通知システム共通関数
 */

require_once('dbmanager.php');

/**
 * 通知を作成する
 * @param int $user_id 通知を受け取るユーザーID
 * @param int|null $from_user_id 通知の発生源ユーザーID
 * @param string $notification_type 通知の種類
 * @param string $title 通知のタイトル
 * @param string $message 通知の内容
 * @param int|null $related_id 関連するID
 * @return bool 成功時true、失敗時false
 */
function create_notification($user_id, $from_user_id, $notification_type, $title, $message, $related_id = null) {
    try {
        $db = new cdb();
        $stmt = $db->prepare('
            INSERT INTO notifications (user_id, from_user_id, notification_type, title, message, related_id, is_read)
            VALUES (?, ?, ?, ?, ?, ?, 0)
        ');
        $result = $stmt->execute([$user_id, $from_user_id, $notification_type, $title, $message, $related_id]);
        return $result;
    } catch (PDOException $e) {
        error_log('Notification creation failed: ' . $e->getMessage());
        return false;
    }
}

/**
 * チャットメッセージ通知を作成
 * @param int $class_id クラスID
 * @param int $sender_id 送信者ID
 * @param string $sender_name 送信者名
 * @param string $message_preview メッセージのプレビュー
 */
function notify_chat_message($class_id, $sender_id, $sender_name, $message_preview) {
    try {
        $db = new cdb();
        
        // クラス名とコミュニティ名を取得
        $class_info_stmt = $db->prepare('
            SELECT cl.class_name, c.community_name 
            FROM classes cl 
            JOIN communities c ON cl.class_community = c.community_id 
            WHERE cl.class_id = ?
        ');
        $class_info_stmt->execute([$class_id]);
        $class_info = $class_info_stmt->fetch();
        
        $class_name = $class_info['class_name'] ?? 'クラス';
        $community_name = $class_info['community_name'] ?? 'コミュニティ';
        
        // クラスが属するコミュニティのメンバーを取得
        $stmt = $db->prepare('
            SELECT DISTINCT cu.user_id 
            FROM community_users cu 
            JOIN classes cl ON cl.class_community = cu.community_id
            WHERE cl.class_id = ? AND cu.user_id != ?
        ');
        $stmt->execute([$class_id, $sender_id]);
        $community_members = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // コミュニティオーナー（教師）も取得
        $owner_stmt = $db->prepare('
            SELECT c.community_owner 
            FROM classes cl 
            JOIN communities c ON cl.class_community = c.community_id 
            WHERE cl.class_id = ? AND c.community_owner != ?
        ');
        $owner_stmt->execute([$class_id, $sender_id]);
        $owner_result = $owner_stmt->fetch();
        
        // 通知対象者リストを作成
        $notify_users = $community_members;
        if ($owner_result && $owner_result['community_owner']) {
            $owner_id = $owner_result['community_owner'];
            // オーナーがメンバーリストに含まれていない場合は追加
            if (!in_array($owner_id, $notify_users)) {
                $notify_users[] = $owner_id;
            }
        }
        
        // 各ユーザーに通知を送信
        foreach ($notify_users as $member_id) {
            $title = 'クラスチャット - ' . $class_name;
            $message = '「' . $community_name . '」の「' . $class_name . '」で' . $sender_name . 'さんからメッセージ: ' . mb_substr($message_preview, 0, 30) . '...';
            create_notification($member_id, $sender_id, 'chat_message', $title, $message, $class_id);
        }
    } catch (PDOException $e) {
        error_log('Chat notification failed: ' . $e->getMessage());
    }
}

/**
 * コミュニティ参加通知を作成
 * @param int $community_id コミュニティID
 * @param int $new_member_id 新メンバーID
 * @param string $new_member_name 新メンバー名
 * @param string $community_name コミュニティ名
 */
function notify_community_join($community_id, $new_member_id, $new_member_name, $community_name) {
    try {
        $db = new cdb();
        
        // コミュニティの既存メンバーを取得
        $stmt = $db->prepare('
            SELECT DISTINCT cu.user_id 
            FROM community_users cu 
            WHERE cu.community_id = ? AND cu.user_id != ?
        ');
        $stmt->execute([$community_id, $new_member_id]);
        $existing_members = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // コミュニティオーナーも通知対象に追加
        $owner_stmt = $db->prepare('SELECT community_owner FROM communities WHERE community_id = ?');
        $owner_stmt->execute([$community_id]);
        $owner_result = $owner_stmt->fetch();
        
        if ($owner_result && $owner_result['community_owner'] && $owner_result['community_owner'] != $new_member_id) {
            $owner_id = $owner_result['community_owner'];
            if (!in_array($owner_id, $existing_members)) {
                $existing_members[] = $owner_id;
            }
        }
        
        // 既存メンバーに通知
        foreach ($existing_members as $member_id) {
            $title = 'コミュニティ参加 - ' . $community_name;
            $message = $new_member_name . 'さんがコミュニティ「' . $community_name . '」に参加しました';
            create_notification($member_id, $new_member_id, 'community_join', $title, $message, $community_id);
        }
    } catch (PDOException $e) {
        error_log('Community join notification failed: ' . $e->getMessage());
    }
}

/**
 * FAQ回答通知を作成
 * @param int $faq_id FAQ ID
 * @param int $questioner_id 質問者ID
 * @param int $responder_id 回答者ID
 * @param string $responder_name 回答者名
 * @param string $faq_title FAQ タイトル
 */
function notify_faq_answer($faq_id, $questioner_id, $responder_id, $responder_name, $faq_title) {
    if ($questioner_id != $responder_id) {
        $title = 'FAQ回答 - ' . mb_substr($faq_title, 0, 20) . '...';
        $message = '先生の' . $responder_name . 'さんがあなたの質問「' . mb_substr($faq_title, 0, 30) . '...」に回答しました';
        create_notification($questioner_id, $responder_id, 'faq_answer', $title, $message, $faq_id);
    }
}

/**
 * クラス招待通知を作成
 * @param int $class_id クラスID
 * @param int $invited_user_id 招待されたユーザーID
 * @param int $inviter_id 招待者ID
 * @param string $inviter_name 招待者名
 * @param string $class_name クラス名
 */
function notify_class_invite($class_id, $invited_user_id, $inviter_id, $inviter_name, $class_name) {
    $title = 'クラス招待';
    $message = $inviter_name . 'さんから「' . $class_name . '」クラスに招待されました';
    create_notification($invited_user_id, $inviter_id, 'class_invite', $title, $message, $class_id);
}

/**
 * システム通知を作成
 * @param int $user_id ユーザーID
 * @param string $title タイトル
 * @param string $message メッセージ
 */
function notify_system($user_id, $title, $message) {
    create_notification($user_id, null, 'system', $title, $message, null);
}

/**
 * ウェルカム通知を作成（新規登録時）
 * @param int $user_id 新規ユーザーID
 * @param string $user_name ユーザー名
 */
function notify_welcome($user_id, $user_name) {
    $title = 'ようこそ！';
    $message = $user_name . 'さん、ご登録ありがとうございます。プロフィールを完成させて、コミュニティに参加してみましょう！';
    notify_system($user_id, $title, $message);
}

/**
 * FAQ質問投稿通知を作成（教師向け）
 * @param int $faq_id FAQ ID
 * @param int $questioner_id 質問者ID
 * @param string $questioner_name 質問者名
 * @param string $faq_title FAQ タイトル
 */
function notify_faq_question($faq_id, $questioner_id, $questioner_name, $faq_title) {
    try {
        $db = new cdb();
        
        // 教師ユーザーを取得
        $stmt = $db->prepare('SELECT user_id FROM users WHERE user_is_teacher = 1');
        $stmt->execute();
        $teachers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // 各教師に通知を送信
        foreach ($teachers as $teacher_id) {
            if ($teacher_id != $questioner_id) {
                $title = 'FAQ新規質問 - ' . mb_substr($faq_title, 0, 20) . '...';
                $message = $questioner_name . 'さんから新しい質問「' . mb_substr($faq_title, 0, 30) . '...」が投稿されました。回答をお願いします。';
                create_notification($teacher_id, $questioner_id, 'faq_question', $title, $message, $faq_id);
            }
        }
    } catch (PDOException $e) {
        error_log('FAQ question notification failed: ' . $e->getMessage());
    }
}

/**
 * 新しいコミュニティ作成通知を作成（管理者向け）
 * @param int $community_id コミュニティID
 * @param int $creator_id 作成者ID
 * @param string $creator_name 作成者名
 * @param string $community_name コミュニティ名
 */
function notify_community_created($community_id, $creator_id, $creator_name, $community_name) {
    try {
        $db = new cdb();
        
        // 管理者ユーザーを取得（ここでは他の教師に通知）
        $stmt = $db->prepare('SELECT user_id FROM users WHERE user_is_teacher = 1 AND user_id != ?');
        $stmt->execute([$creator_id]);
        $other_teachers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // 他の教師に通知
        foreach ($other_teachers as $teacher_id) {
            $title = '新しいコミュニティ';
            $message = $creator_name . 'さんが新しいコミュニティ「' . $community_name . '」を作成しました';
            create_notification($teacher_id, $creator_id, 'community_create', $title, $message, $community_id);
        }
    } catch (PDOException $e) {
        error_log('Community creation notification failed: ' . $e->getMessage());
    }
}
?>
