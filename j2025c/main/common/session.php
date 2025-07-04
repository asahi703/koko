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
    return isset($_SESSION['tmI2025_user']) && isset($_SESSION['tmI2025_user']['uuid']);
}

// ログインユーザー情報取得（未ログイン時はnull）
function get_login_user() {
    session_start_safe();
    return isset($_SESSION['tmI2025_user']) ? $_SESSION['tmI2025_user'] : null;
}

// ログイン処理（ユーザー情報をセッションにセット）
function login_user($user_arr) {
    session_start_safe();
    $_SESSION['tmI2025_user'] = $user_arr;
}

// ログアウト処理
function logout_user() {
    session_start_safe();
    unset($_SESSION['tmI2025_user']);
}
?>
