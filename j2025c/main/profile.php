
<?php
// エラー表示を制御
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', 0);

require_once('common/session.php');
require_once('common/dbmanager.php');

$current_user = get_login_user();
$error = '';
$profile_user = null;

// デバッグ用: セッション情報の確認
if ($current_user) {
    error_log("Current user session data: " . print_r($current_user, true));
}

// ログインユーザーのIDを適切に取得
$current_user_id = null;
if ($current_user && is_array($current_user)) {
    $current_user_id = $current_user['user_id'] ?? $current_user['uuid'] ?? null;
}

// URLパラメータからユーザーIDを取得
$user_id = $_GET['user'] ?? null;

if (!$current_user) {
    header('Location: signin.php');
    exit;
}

if (!$user_id) {
    $error = 'ユーザーIDが指定されていません。';
} else {
    try {
        $db = new cdb();
        
        // 指定されたユーザーの情報を取得
        $stmt = $db->prepare('SELECT user_id, user_name, user_mailaddress, user_is_teacher, user_icon, last_login_time FROM users WHERE user_id = ?');
        $stmt->execute([$user_id]);
        $profile_user = $stmt->fetch();
        
        if (!$profile_user) {
            $error = 'ユーザーが見つかりません。';
        } else {
            // 活動情報を取得
            $activity_data = [];
            
            // 1. チャットメッセージ数を取得
            try {
                $stmt = $db->prepare('SELECT COUNT(*) as message_count FROM class_chats WHERE user_id = ?');
                $stmt->execute([$user_id]);
                $message_result = $stmt->fetch();
                $activity_data['message_count'] = $message_result['message_count'] ?? 0;
            } catch (PDOException $e) {
                $activity_data['message_count'] = 0;
            }
            
            // 2. 参加コミュニティ数を取得
            try {
                $stmt = $db->prepare('SELECT COUNT(DISTINCT cu.community_id) as community_count FROM community_users cu WHERE cu.user_id = ?');
                $stmt->execute([$user_id]);
                $community_result = $stmt->fetch();
                $activity_data['community_count'] = $community_result['community_count'] ?? 0;
            } catch (PDOException $e) {
                $activity_data['community_count'] = 0;
            }
            
            // 3. FAQ投稿数を取得
            try {
                $stmt = $db->prepare('SELECT COUNT(*) as faq_count FROM faq WHERE faq_user = ?');
                $stmt->execute([$user_id]);
                $faq_result = $stmt->fetch();
                $activity_data['faq_count'] = $faq_result['faq_count'] ?? 0;
            } catch (PDOException $e) {
                $activity_data['faq_count'] = 0;
            }
            
            // 4. 最新のチャットメッセージの時刻を取得
            try {
                $stmt = $db->prepare('SELECT MAX(created_at) as last_activity FROM class_chats WHERE user_id = ?');
                $stmt->execute([$user_id]);
                $last_activity_result = $stmt->fetch();
                $activity_data['last_activity'] = $last_activity_result['last_activity'] ?? null;
            } catch (PDOException $e) {
                $activity_data['last_activity'] = null;
            }
            
            // 最終ログイン時間を取得（既にプロフィール情報として取得済み）
            $activity_data['last_login'] = $profile_user['last_login_time'] ?? null;
        }
    } catch (PDOException $e) {
        $error = 'ユーザー情報の取得に失敗しました。';
    }
}

// 出力バッファをクリア
if (ob_get_length()) {
    ob_clean();
}

// キャッシュを無効にする
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title><?php echo $profile_user ? htmlspecialchars($profile_user['user_name']) . 'のプロフィール' : 'プロフィール'; ?></title>
    <link rel="stylesheet" href="../main/css/profile.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 mx-auto" style="margin-top: 100px; max-width: 800px;">
        
        <?php if ($error): ?>
            <div class="alert alert-danger shadow-lg">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <h5 class="mb-1">エラーが発生しました</h5>
                        <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="community.php" class="btn btn-light btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>コミュニティ一覧に戻る
                    </a>
                </div>
            </div>
        <?php elseif ($profile_user): ?>
            
            <!-- プロフィールヘッダー -->
            <div class="card shadow-lg mb-5">
                <div class="card-body text-center py-5">
                    <div class="profile-image-container">
                        <?php
                        // プロフィールアイコンの表示ロジック
                        $profile_icon_src = '../main/img/headerImg/account.png'; // デフォルト
                        
                        if (!empty($profile_user['user_icon'])) {
                            $user_icon_path = $profile_user['user_icon'];
                            
                            // 相対パスの場合は ../ を追加
                            if (strpos($user_icon_path, 'img/user_icons/') === 0) {
                                $profile_icon_src = '../' . $user_icon_path;
                            } else {
                                // ファイル名のみの場合
                                $profile_icon_src = '../img/user_icons/' . $user_icon_path;
                            }
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($profile_icon_src); ?>" 
                             alt="プロフィール画像" 
                             class="profile-image"
                             onerror="this.src='../main/img/headerImg/account.png';">
                    </div>
                    
                    <h2 class="mb-3"><?php echo htmlspecialchars($profile_user['user_name'] ?? '名前不明'); ?></h2>
                    <p class="text-muted mb-4 fs-5"><?php echo htmlspecialchars($profile_user['user_mailaddress'] ?? 'メールアドレス不明'); ?></p>
                    
                    <div class="mb-4">
                        <span class="badge <?php echo ($profile_user['user_is_teacher'] ?? false) ? 'bg-success' : 'bg-primary'; ?> fs-6 px-4 py-3">
                            <i class="bi bi-<?php echo ($profile_user['user_is_teacher'] ?? false) ? 'mortarboard' : 'person'; ?> me-2"></i>
                            <?php echo ($profile_user['user_is_teacher'] ?? false) ? '先生' : '生徒'; ?>
                        </span>
                    </div>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="chat.php?user=<?php echo htmlspecialchars($profile_user['user_id'] ?? ''); ?>" 
                           class="btn btn-primary btn-lg">
                            <i class="bi bi-chat-dots me-2"></i>メッセージを送る
                        </a>
                        <?php if ($current_user_id && ($profile_user['user_id'] ?? null) && $current_user_id !== ($profile_user['user_id'] ?? null)): ?>
                        <button class="btn btn-outline-secondary btn-lg" onclick="history.back()">
                            <i class="bi bi-arrow-left me-2"></i>戻る
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- 活動情報 -->
            <div class="row g-4">
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-lg">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-activity me-2"></i>最近の活動
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="activity-item d-flex align-items-center">
                                <div class="activity-icon bg-primary text-white rounded-circle me-3">
                                    <i class="bi bi-clock"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">最終ログイン</div>
                                    <small class="text-muted">
                                        <?php 
                                        if (!empty($activity_data['last_login'])) {
                                            $last_time = strtotime($activity_data['last_login']);
                                            $time_diff = time() - $last_time;
                                            
                                            if ($time_diff < 3600) { // 1時間以内
                                                echo floor($time_diff / 60) . '分前';
                                            } elseif ($time_diff < 86400) { // 1日以内
                                                echo floor($time_diff / 3600) . '時間前';
                                            } elseif ($time_diff < 604800) { // 1週間以内
                                                echo floor($time_diff / 86400) . '日前';
                                            } else {
                                                echo date('Y/m/d H:i', $last_time);
                                            }
                                        } else {
                                            echo 'ログイン履歴なし';
                                        }
                                        ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="activity-item d-flex align-items-center">
                                <div class="activity-icon bg-success text-white rounded-circle me-3">
                                    <i class="bi bi-chat-text"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">投稿数</div>
                                    <small class="text-muted">
                                        <?php 
                                        $total_posts = ($activity_data['message_count'] ?? 0) + ($activity_data['faq_count'] ?? 0);
                                        echo $total_posts . '件の投稿';
                                        ?>
                                    </small>
                                </div>
                            </div>
                            
                            <div class="activity-item d-flex align-items-center">
                                <div class="activity-icon bg-info text-white rounded-circle me-3">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">参加コミュニティ</div>
                                    <small class="text-muted"><?php echo ($activity_data['community_count'] ?? 0) . 'つのコミュニティ'; ?></small>
                                </div>
                            </div>
                            
                            <?php if (($activity_data['faq_count'] ?? 0) > 0): ?>
                            <div class="activity-item d-flex align-items-center">
                                <div class="activity-icon bg-warning text-white rounded-circle me-3">
                                    <i class="bi bi-question-circle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold mb-1">FAQ投稿</div>
                                    <small class="text-muted"><?php echo $activity_data['faq_count']; ?>件の質問</small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-lg">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle me-2"></i>基本情報
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <div class="fw-semibold text-muted mb-2">ユーザーID</div>
                                <div class="fs-5"><?php echo htmlspecialchars($profile_user['user_id'] ?? 'ID不明'); ?></div>
                            </div>
                            
                            <div class="info-item">
                                <div class="fw-semibold text-muted mb-2">役割</div>
                                <div>
                                    <span class="badge <?php echo ($profile_user['user_is_teacher'] ?? false) ? 'bg-success' : 'bg-primary'; ?>">
                                        <i class="bi bi-<?php echo ($profile_user['user_is_teacher'] ?? false) ? 'mortarboard' : 'person'; ?> me-1"></i>
                                        <?php echo ($profile_user['user_is_teacher'] ?? false) ? '先生' : '生徒'; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="info-item">
                                <div class="fw-semibold text-muted mb-2">登録日</div>
                                <div class="fs-6">
                                    <i class="bi bi-calendar3 me-2 text-primary"></i>
                                    2024年4月1日
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php endif; ?>
        
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // プロフィール画像の読み込みエラーハンドリング
    document.addEventListener('DOMContentLoaded', function() {
        const profileImage = document.querySelector('.profile-image');
        if (profileImage) {
            profileImage.addEventListener('error', function() {
                console.log('プロフィール画像の読み込みに失敗しました。デフォルト画像を表示します。');
                this.src = '../main/img/headerImg/account.png';
            });
        }
    });
</script>
