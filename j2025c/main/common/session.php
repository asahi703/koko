<?php
// セッション開始（多重呼び出し安全）
function session_start_safe() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// ログイン状態かどうか
function is_logged_in() {
    session_start_safe();
    return isset($_SESSION['tmC2025_user']) && isset($_SESSION['tmC2025_user']['uuid']);
}

// ログインユーザー情報取得（未ログイン時はnull）
function get_login_user() {
    session_start_safe();
    if (isset($_SESSION['tmC2025_user'])) {
        $user = $_SESSION['tmC2025_user'];
        
        // 必要なフィールドが不足している場合、データベースから最新情報を取得
        if (!isset($user['user_id']) || !isset($user['user_name']) || !isset($user['user_mailaddress'])) {
            try {
                require_once(__DIR__ . '/dbmanager.php');
                $db = new cdb();
                $stmt = $db->prepare('SELECT * FROM users WHERE user_id = ?');
                $stmt->execute([$user['uuid'] ?? $user['user_id'] ?? 0]);
                $fresh_user = $stmt->fetch();
                
                if ($fresh_user) {
                    $updated_user = [
                        'uuid' => $fresh_user['user_id'],
                        'user_id' => $fresh_user['user_id'],
                        'user_name' => $fresh_user['user_name'],
                        'user_mailaddress' => $fresh_user['user_mailaddress'],
                        'user_icon' => $fresh_user['user_icon'],
                        'user_is_teacher' => $fresh_user['user_is_teacher'],
                        // 後方互換性のため
                        'name' => $fresh_user['user_name'],
                        'mail' => $fresh_user['user_mailaddress']
                    ];
                    $_SESSION['tmC2025_user'] = $updated_user;
                    return $updated_user;
                }
            } catch (Exception $e) {
                // データベースエラーの場合は既存のセッション情報を返す
            }
        }
        
        return $user;
    }
    return null;
}

// ログイン処理（ユーザー情報をセッションにセット）
function login_user($user_arr) {
    session_start_safe();
    $_SESSION['tmC2025_user'] = $user_arr;
}

// ログアウト処理
function logout_user() {
    session_start_safe();
    unset($_SESSION['tmC2025_user']);
}
?>
