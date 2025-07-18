<?php
/*!
@file notification.php
@brief 通知一覧
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルーチE
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノーチE
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $user;
	public $notifications;
	public $error;
	public $success;
	
	//--------------------------------------------------------------------------------------
	/*!
	@brief	コンストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __construct() {
		//親クラスのコンストラクタを呼ぶ
		parent::__construct();
		$this->user = null;
		$this->notifications = array();
		$this->error = '';
		$this->success = '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のチE��ォルト値をセチE��
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処琁E継承して使用)
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// セチE��ョン惁E��の取征E
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
		
		if(!$this->user){
			cutil::redirect_exit('index.php');
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  本体実行（表示前�E琁E��E
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo 'ペ�Eジが無効でぁE;
			exit();
		}
		
		// 通知一覧を取征E
		$this->get_notifications();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	通知一覧取征E
	@return	なぁE
	*/
	//--------------------------------------------------------------------------------------
	function get_notifications(){
		// 現在はサンプル通知を生戁E
		// 実際のチE�Eタベ�Eスから取得する場合�Eここで処琁E
		$this->notifications = array(
			array(
				'notification_id' => 1,
				'content' => 'コミュニティ「�Eログラミング学習」に新しい投稿があります、E,
				'created_date' => '2024/01/15 14:30'
			),
			array(
				'notification_id' => 2,
				'content' => 'クラス「Java基礎」�Eメンバ�Eに追加されました、E,
				'created_date' => '2024/01/14 10:15'
			)
		);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー存在斁E���Eの取征E
	@return	エラー表示斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">入力エラーがあります。各頁E��のエラーを確認してください、E/div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">処琁E��失敗しました。サポ�Eトを確認下さぁE��E/div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー表示の取征E
	@return	エラー表示斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_error_display(){
		if(!empty($this->error)){
			return '<div class="alert alert-danger mt-3">' . display($this->error) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	成功メチE��ージ表示の取征E
	@return	成功メチE��ージ表示斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_success_display(){
		if(!empty($this->success)){
			return '<div class="alert alert-success mt-3">' . display($this->success) . '</div>';
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	通知一覧の取征E
	@return	通知一覧斁E���E
	*/
	//--------------------------------------------------------------------------------------
	function get_notification_list(){
		if(count($this->notifications) == 0){
			return '<div class="alert alert-info">新しい通知はありません、E/div>';
		}
		
		$list_str = '';
		foreach($this->notifications as $notification){
			$content = display($notification['content']);
			$created_date = display($notification['created_date']);
			
			$list_str .= <<<END_BLOCK
<div class="rounded border border-2 mb-3 shadow-sm">
    <div class="border-bottom d-flex align-items-center justify-content-between" style="max-height: 50px;">
        <div class="d-flex align-items-center px-3 py-2">
            <img src="img/headerImg/account.png" style="width: 40px"
                 class="hd-img d-inline-block align-top img-fluid" alt="">
            <span class="ms-2 d-flex align-items-center fs-5">通知</span>
        </div>
        <span class="opacity-75 me-3" style="font-size: 0.9em;">{$created_date}</span>
    </div>
    <div class="post-word p-3">
        {$content}
    </div>
</div>
END_BLOCK;
		}
		return $list_str;
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なぁE
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロチE��終亁E
?>
<!-- コンチE��チE��-->
<head>
    <title>通知</title>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <main class="class-main-content col-12 col-md-9 col-lg-10 px-3 px-md-5 py-4 py-md-5 mx-auto"
          style="min-height: 100vh;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">通知</h2>
        </div>

        <?= $this->get_notification_list(); ?>

    </main>
</div>
</div>
<!-- /コンチE��チE��-->
<?php 
//PHPブロチE��再開
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	チE��トラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのチE��トラクタを呼ぶ
		parent::__destruct();
	}
}

//ペ�Eジを作�E
$page_obj = new cnode();
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//サイドバー追加
$page_obj->add_child(cutil::create('csidebar'));
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処琁E
$page_obj->create();
//POST変数のチE��ォルト値をセチE��
$main_obj->post_default();
//本体実行（表示前�E琁E��E
$main_obj->execute();
//ペ�Eジ全体を表示
$page_obj->display();

?>
require_once('common/session.php');
require_once('common/dbmanager.php');

$current_user = get_login_user();
if (!$current_user) {
    header('Location: signin.php');
    exit;
}

$user_id = $current_user['user_id'] ?? $current_user['uuid'] ?? null;
$notifications = [];
$error = '';

if ($user_id) {
    try {
        $db = new cdb();
        
        // 通知を取得（未読を優先、日時頁E��並び替え！E
        $stmt = $db->prepare('
            SELECT n.*, 
                   fu.user_name as from_user_name,
                   fu.user_icon as from_user_icon
            FROM notifications n
            LEFT JOIN users fu ON n.from_user_id = fu.user_id
            WHERE n.user_id = ?
            ORDER BY n.is_read ASC, n.created_at DESC
            LIMIT 50
        ');
        $stmt->execute([$user_id]);
        $notifications = $stmt->fetchAll();
        
        // 既読処琁E��EETパラメータで持E��された通知を既読にする�E�E
        if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
            $notification_id = $_GET['mark_read'];
            $update_stmt = $db->prepare('UPDATE notifications SET is_read = 1, read_at = NOW() WHERE notification_id = ? AND user_id = ?');
            $update_stmt->execute([$notification_id, $user_id]);
            header('Location: notification.php');
            exit;
        }
        
        // 全て既読にする処琁E
        if (isset($_POST['mark_all_read'])) {
            $update_all_stmt = $db->prepare('UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0');
            $update_all_stmt->execute([$user_id]);
            header('Location: notification.php');
            exit;
        }
        
    } catch (PDOException $e) {
        $error = '通知の取得に失敗しました、E;
        error_log('Notification error: ' . $e->getMessage());
    }
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>通知 - コミュニティプラチE��フォーム</title>
    <link rel="stylesheet" href="css/notification.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>

<div class="main-content-wrapper">
    <main class="col-12 col-md-9 col-lg-10 px-md-4 mx-auto" style="margin-top: 100px; max-width: 900px;">
        
        <!-- ヘッダー -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">
                <i class="bi bi-bell me-2"></i>通知
            </h2>
            <?php if (!empty($notifications)): ?>
            <form method="post" class="d-inline">
                <button type="submit" name="mark_all_read" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-check-all me-1"></i>すべて既読にする
                </button>
            </form>
            <?php endif; ?>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif (empty($notifications)): ?>
            <div class="empty-state text-center py-5">
                <i class="bi bi-bell-slash display-1 text-muted mb-3"></i>
                <h4 class="text-muted">通知はありません</h4>
                <p class="text-muted">新しい通知が届くとここに表示されます、E/p>
            </div>
        <?php else: ?>
            
            <!-- 通知リスチE-->
            <div class="notification-list">
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" 
                         data-notification-id="<?php echo $notification['notification_id']; ?>"
                         data-type="<?php echo $notification['notification_type']; ?>">
                        
                        <div class="d-flex align-items-start">
                            <!-- アイコン -->
                            <div class="notification-icon me-3">
                                <?php
                                $icon_class = '';
                                $icon_color = '';
                                switch ($notification['notification_type']) {
                                    case 'chat_message':
                                        $icon_class = 'bi-chat-dots';
                                        $icon_color = 'text-primary';
                                        break;
                                    case 'community_join':
                                        $icon_class = 'bi-people';
                                        $icon_color = 'text-success';
                                        break;
                                    case 'community_create':
                                        $icon_class = 'bi-people-fill';
                                        $icon_color = 'text-success';
                                        break;
                                    case 'faq_answer':
                                        $icon_class = 'bi-question-circle';
                                        $icon_color = 'text-info';
                                        break;
                                    case 'faq_question':
                                        $icon_class = 'bi-question-circle-fill';
                                        $icon_color = 'text-warning';
                                        break;
                                    case 'class_invite':
                                        $icon_class = 'bi-calendar-event';
                                        $icon_color = 'text-warning';
                                        break;
                                    case 'system':
                                        $icon_class = 'bi-gear';
                                        $icon_color = 'text-secondary';
                                        break;
                                    default:
                                        $icon_class = 'bi-bell';
                                        $icon_color = 'text-muted';
                                }
                                ?>
                                <i class="bi <?php echo $icon_class; ?> <?php echo $icon_color; ?> fs-4"></i>
                            </div>
                            
                            <!-- 冁E�� -->
                            <div class="notification-content flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="notification-header">
                                        <h6 class="notification-title mb-1">
                                            <?php echo htmlspecialchars($notification['title']); ?>
                                            <?php if (!$notification['is_read']): ?>
                                                <span class="badge bg-primary ms-2">新着</span>
                                            <?php endif; ?>
                                        </h6>
                                        <!-- 通知カチE��リーバッジ -->
                                        <?php
                                        $category_badge = '';
                                        $badge_class = '';
                                        switch ($notification['notification_type']) {
                                            case 'chat_message':
                                                $category_badge = 'クラスチャチE��';
                                                $badge_class = 'bg-primary';
                                                break;
                                            case 'community_join':
                                                $category_badge = 'コミュニティ';
                                                $badge_class = 'bg-success';
                                                break;
                                            case 'community_create':
                                                $category_badge = 'コミュニティ';
                                                $badge_class = 'bg-success';
                                                break;
                                            case 'faq_answer':
                                                $category_badge = 'FAQ回筁E;
                                                $badge_class = 'bg-info';
                                                break;
                                            case 'faq_question':
                                                $category_badge = 'FAQ質啁E;
                                                $badge_class = 'bg-warning';
                                                break;
                                            case 'class_invite':
                                                $category_badge = 'クラス招征E;
                                                $badge_class = 'bg-secondary';
                                                break;
                                            case 'system':
                                                $category_badge = 'シスチE��';
                                                $badge_class = 'bg-dark';
                                                break;
                                        }
                                        ?>
                                        <?php if ($category_badge): ?>
                                            <span class="badge <?php echo $badge_class; ?> category-badge"><?php echo $category_badge; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted notification-time">
                                        <?php
                                        $created_time = strtotime($notification['created_at']);
                                        $time_diff = time() - $created_time;
                                        
                                        if ($time_diff < 3600) {
                                            echo floor($time_diff / 60) . '刁E��';
                                        } elseif ($time_diff < 86400) {
                                            echo floor($time_diff / 3600) . '時間剁E;
                                        } elseif ($time_diff < 604800) {
                                            echo floor($time_diff / 86400) . '日剁E;
                                        } else {
                                            echo date('m/d', $created_time);
                                        }
                                        ?>
                                    </small>
                                </div>
                                
                                <p class="notification-message mb-2">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </p>
                                
                                <?php if ($notification['from_user_name']): ?>
                                    <div class="notification-from d-flex align-items-center">
                                        <img src="<?php 
                                            if (!empty($notification['from_user_icon'])) {
                                                if (strpos($notification['from_user_icon'], 'img/user_icons/') === 0) {
                                                    echo '../' . $notification['from_user_icon'];
                                                } else {
                                                    echo '../img/user_icons/' . $notification['from_user_icon'];
                                                }
                                            } else {
                                                echo '../main/img/headerImg/account.png';
                                            }
                                        ?>" 
                                             alt="ユーザー画僁E 
                                             class="user-icon me-2"
                                             onerror="this.src='../main/img/headerImg/account.png';">
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($notification['from_user_name']); ?>さんより
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- アクションボタン -->
                                <div class="notification-actions mt-2">
                                    <?php if (!$notification['is_read']): ?>
                                        <a href="notification.php?mark_read=<?php echo $notification['notification_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-check me-1"></i>既読にする
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($notification['related_id'] && $notification['notification_type'] === 'chat_message'): ?>
                                        <a href="class_chat.php?id=<?php echo $notification['related_id']; ?>" 
                                           class="btn btn-sm btn-primary ms-2">
                                            <i class="bi bi-arrow-right me-1"></i>クラスチャチE��を見る
                                        </a>
                                    <?php elseif ($notification['related_id'] && ($notification['notification_type'] === 'community_join' || $notification['notification_type'] === 'community_create')): ?>
                                        <a href="community.php" 
                                           class="btn btn-sm btn-success ms-2">
                                            <i class="bi bi-people me-1"></i>コミュニティを見る
                                        </a>
                                    <?php elseif ($notification['related_id'] && $notification['notification_type'] === 'faq_answer'): ?>
                                        <a href="faq.php" 
                                           class="btn btn-sm btn-info ms-2">
                                            <i class="bi bi-question-circle me-1"></i>FAQを見る
                                        </a>
                                    <?php elseif ($notification['related_id'] && $notification['notification_type'] === 'faq_question'): ?>
                                        <a href="teacher_questions.php" 
                                           class="btn btn-sm btn-warning ms-2">
                                            <i class="bi bi-person-raised-hand me-1"></i>質問に回答すめE
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
        
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // 通知をクリチE��したとき�E処琁E
    document.addEventListener('DOMContentLoaded', function() {
        const notificationItems = document.querySelectorAll('.notification-item.unread');
        
        notificationItems.forEach(item => {
            item.addEventListener('click', function(e) {
                // ボタンがクリチE��された場合�E無要E
                if (e.target.closest('.btn')) return;
                
                const notificationId = this.dataset.notificationId;
                if (notificationId) {
                    window.location.href = `notification.php?mark_read=${notificationId}`;
                }
            });
        });
    });
</script>
