<?php
/*!
@file class_calender.php
@brief クラスカレンダー
@copyright Copyright (c) 2024 Yamanoi Yasushi.
*/

//ライブラリをインクルード
require_once("common/libs.php");

$err_array = array();
$err_flag = 0;
$page_obj = null;

//--------------------------------------------------------------------------------------
///	本体ノード
//--------------------------------------------------------------------------------------
class cmain_node extends cnode {
	public $user;
	public $class_id;
	public $db;
	public $class_info;
	public $events;
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
		$this->class_id = 0;
		$this->db = null;
		$this->class_info = null;
		$this->events = array();
		$this->error = '';
		$this->success = '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  POST変数のデフォルト値をセット
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function post_default(){
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	構築時の処理(継承して使用)
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	public function create(){
		// セッション情報の取得
		require_once(__DIR__ . '/common/session.php');
		if(is_logged_in()){
			$this->user = get_login_user();
		}
		
		$this->class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		
		if(!$this->user || !$this->class_id){
			cutil::redirect_exit('community.php');
		}
		
		// DB接続
		require_once(__DIR__ . '/common/dbmanager.php');
		$this->db = new cdb();
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  本体実行（表示前処理）
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function execute(){
		global $err_array;
		global $err_flag;
		global $page_obj;
		
		if(is_null($page_obj)){
			echo 'ページが無効です';
			exit();
		}
		
		try{
			// クラス情報取得
			$this->get_class_info();
			
			if(!$this->class_info){
				cutil::redirect_exit('community.php');
			}
			
			// イベント一覧取得
			$this->get_events();
			
		} catch(exception $e){
			$err_flag = 2;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	クラス情報取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_class_info(){
		try{
			$stmt = $this->db->prepare('
				SELECT c.*, com.community_name, com.community_id 
				FROM classes c 
				JOIN communities com ON c.class_community = com.community_id 
				WHERE c.class_id = ?
			');
			$stmt->execute([$this->class_id]);
			$this->class_info = $stmt->fetch();
		} catch(exception $e){
			$this->class_info = null;
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	イベント一覧取得
	@return	なし
	*/
	//--------------------------------------------------------------------------------------
	function get_events(){
		try{
			$stmt = $this->db->prepare('
				SELECT event_id as id, title, start_date as start, description 
				FROM calendar_events 
				WHERE class_id = ?
			');
			$stmt->execute([$this->class_id]);
			$this->events = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(PDOException $e){
			// テーブルが存在しない場合はサンプルデータ
			$this->events = array(
				array(
					'id' => '1',
					'title' => 'サンプルイベント1',
					'start' => date('Y-m-d'),
					'description' => 'サンプルの説明です'
				),
				array(
					'id' => '2',
					'title' => 'サンプルイベント2',
					'start' => date('Y-m-d', strtotime('+3 days')),
					'description' => 'サンプルイベント2の説明'
				)
			);
		} catch(exception $e){
			$this->events = array();
		}
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー存在文字列の取得
	@return	エラー表示文字列
	*/
	//--------------------------------------------------------------------------------------
	function get_err_flag(){
		global $err_flag;
		switch($err_flag){
			case 1:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">入力エラーがあります。各項目のエラーを確認してください。</div>
END_BLOCK;
			return $str;
			break;
			case 2:
			$str =<<<END_BLOCK

<div class="alert alert-danger mt-3">処理に失敗しました。サポートを確認下さい。</div>
END_BLOCK;
			return $str;
			break;
		}
		return '';
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief	エラー表示の取得
	@return	エラー表示文字列
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
	@brief	成功メッセージ表示の取得
	@return	成功メッセージ表示文字列
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
	@brief	イベントJSONデータ取得
	@return	イベントJSONデータ
	*/
	//--------------------------------------------------------------------------------------
	function get_events_json(){
		return json_encode($this->events, JSON_UNESCAPED_UNICODE);
	}
	//--------------------------------------------------------------------------------------
	/*!
	@brief  表示(継承して使用)
	@return なし
	*/
	//--------------------------------------------------------------------------------------
	public function display(){
//PHPブロック終了
?>
<!-- コンテンツ　-->
<head>
    <title>クラスカレンダー</title>
    <link rel="stylesheet" href="css/class_calender.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <style>
        .calendar-container {
            max-width: 700px;
            margin: 0 auto;
        }
        #calendar {
            min-height: 0;
        }
        /* スクロールバー非表示 */
        #calendar ::-webkit-scrollbar {
            display: none;
        }
        #calendar {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
    </style>
</head>
<div class="contents">
<?= $this->get_err_flag(); ?>
<?= $this->get_error_display(); ?>
<?= $this->get_success_display(); ?>

<div class="main-content-wrapper">
    <?php include 'includes/class_sidebar.php'?>

    <!-- メインコンテンツ -->
    <main class="class-main-content p-0" style="min-height: 100vh; width: 100%;">
        <div class="calendar-container">
            <h2 class="text-center mb-4">行事予定カレンダー</h2>
            <div id="calendar" class="w-100"></div>
            <div id="tooltip" class="custom-tooltip"></div>
        </div>

        <!-- モーダル（フォームつき） -->
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="calendar_event.php" method="POST">
                    <input type="hidden" name="func" value="insert">
                    <input type="hidden" name="class_id" value="<?= display($this->class_id); ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eventModalLabel">予定を追加</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="event_date" class="form-label">日付<span class="text-danger"> *</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" required>
                            </div>
                            <div class="mb-3">
                                <label for="event_title" class="form-label">タイトル<span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" id="event_title" name="event_title" required>
                            </div>
                            <div class="mb-3">
                                <label for="event_desc" class="form-label">説明</label>
                                <textarea class="form-control" id="event_desc" name="event_desc" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">登録</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- イベント詳細モーダル -->
        <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventDetailModalLabel">予定の詳細</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div><strong>タイトル:</strong> <span id="detail-title"></span></div>
                        <div><strong>日付:</strong> <span id="detail-date"></span></div>
                        <div><strong>説明:</strong> <span id="detail-desc"></span></div>
                    </div>
                    <div class="modal-footer">
                        <form id="deleteEventForm" method="POST" action="calendar_event.php" style="display:inline;">
                            <input type="hidden" name="func" value="delete">
                            <input type="hidden" name="event_id" id="delete-event-id">
                            <input type="hidden" name="class_id" value="<?= display($this->class_id); ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('本当に削除しますか？');">削除</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var events = <?= $this->get_events_json(); ?>;
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ja',
            // カレンダーを大きくしない
            height: 'auto',
            contentHeight: 'auto',
            aspectRatio: 1.5,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'addEventButton'
            },
            customButtons: {
                addEventButton: {
                    text: '予定を追加',
                    click: function() {
                        var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                        modal.show();
                    }
                }
            },
            events: events,
            eventClick: function(info) {
                // 詳細モーダルに値をセット
                document.getElementById('detail-title').textContent = info.event.title;
                document.getElementById('detail-date').textContent = info.event.startStr;
                document.getElementById('detail-desc').textContent = info.event.extendedProps.description || '';
                document.getElementById('delete-event-id').value = info.event.id;
                var detailModal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                detailModal.show();
            }
        });
        calendar.render();
    }
});
</script>
<!-- /コンテンツ　-->
<?php 
//PHPブロック再開
	}

	//--------------------------------------------------------------------------------------
	/*!
	@brief	デストラクタ
	*/
	//--------------------------------------------------------------------------------------
	public function __destruct(){
		//親クラスのデストラクタを呼ぶ
		parent::__destruct();
	}
}

//ページを作成
$page_obj = new cnode();
//ヘッダ追加
$page_obj->add_child(cutil::create('cheader'));
//サイドバー追加
$page_obj->add_child(cutil::create('csidebar'));
//本体追加
$page_obj->add_child($main_obj = cutil::create('cmain_node'));
//構築時処理
$page_obj->create();
//POST変数のデフォルト値をセット
$main_obj->post_default();
//本体実行（表示前処理）
$main_obj->execute();
//ページ全体を表示
$page_obj->display();

?>
