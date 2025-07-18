<?php
require_once('common/session.php');
require_once('common/dbmanager.php');

// JSONレスポンス用のヘッダー設定
header('Content-Type: application/json; charset=utf-8');

// POSTリクエスト以外は拒否
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '無効なリクエストです']);
    exit;
}

try {
    // デバッグ情報を記録
    error_log("FAQ削除処理を開始します");
    
    // パラメータチェック
    $faq_id = isset($_POST['faq_id']) ? (int)$_POST['faq_id'] : 0;
    
    if ($faq_id <= 0) {
        error_log("無効なFAQ ID: " . $faq_id);
        echo json_encode(['success' => false, 'message' => '無効なIDが指定されました']);
        exit;
    }
    
    // ログイン確認
    $user = get_login_user();
    if (!$user) {
        error_log("未ログインユーザーによる削除リクエスト");
        echo json_encode(['success' => false, 'message' => 'ログインが必要です']);
        exit;
    }
    
    // 権限チェック用のフラグ
    $is_teacher = ($user['user_is_teacher'] == 1);
    $user_id = $user['uuid'] ?? 0;
    
    error_log("ユーザー情報: ID={$user_id}, 教師={$is_teacher}");
    
    // DBに接続
    $db = new cdb();
    
    // 削除対象のFAQを取得
    $check_stmt = $db->prepare("SELECT * FROM faq WHERE faq_id = ?");
    $check_stmt->execute([$faq_id]);
    $faq = $check_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$faq) {
        error_log("FAQ ID {$faq_id} は存在しません");
        echo json_encode(['success' => false, 'message' => '指定されたFAQが見つかりません']);
        exit;
    }
    
    // 権限チェック
    $is_owner = ((string)$faq['faq_user_id'] === (string)$user_id);
    
    error_log("権限チェック: 教師={$is_teacher}, 投稿者={$is_owner}");
    error_log("投稿者ID比較: FAQ投稿者ID={$faq['faq_user_id']}, ログインユーザーID={$user_id}");
    
    if (!$is_teacher && !$is_owner) {
        error_log("権限不足: 教師または投稿者のみ削除可能");
        echo json_encode(['success' => false, 'message' => '削除権限がありません']);
        exit;
    }
    
    // 削除処理実行
    $delete_stmt = $db->prepare("DELETE FROM faq WHERE faq_id = ?");
    $result = $delete_stmt->execute([$faq_id]);
    
    if ($result && $delete_stmt->rowCount() > 0) {
        error_log("FAQ ID {$faq_id} の削除に成功しました");
        echo json_encode(['success' => true, 'message' => '削除しました']);
    } else {
        error_log("FAQ ID {$faq_id} の削除に失敗しました");
        echo json_encode(['success' => false, 'message' => '削除に失敗しました']);
    }
    
} catch (Exception $e) {
    error_log("FAQ削除エラー: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'サーバーエラーが発生しました']);
}
