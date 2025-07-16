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
    return isset($_SESSION['tmC2025_admin']) && isset($_SESSION['tmC2025_admin']['auid']);
}

// 管理者ログイン状態かどうか（エイリアス）
function is_admin_logged_in() {
    return is_logged_in();
}

// ログインユーザー情報取得（未ログイン時はnull）
function get_login_admin() {
    session_start_safe();
    return isset($_SESSION['tmC2025_admin']) ? $_SESSION['tmC2025_admin'] : null;
}

// 管理者ユーザー情報取得（エイリアス）
function get_admin_user() {
    return get_login_admin();
}

// ログイン処理（ユーザー情報をセッションにセット）
function login_admin($admin_arr) {
    session_start_safe();
    $_SESSION['tmC2025_admin'] = $admin_arr;
}

// ログアウト処理
function logout_admin() {
    session_start_safe();
    unset($_SESSION['tmC2025_admin']);
}
?>
