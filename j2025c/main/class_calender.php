<?php
include 'includes/header.php';
include 'includes/sidebar.php';

// 予定データ取得
require_once('common/dbmanager.php');
$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$events = [];
if ($class_id > 0) {
    $db = new cdb();
    $stmt = $db->prepare('SELECT event_id as id, title, start_date as start, description FROM calendar_events WHERE class_id = ?');
    $stmt->execute([$class_id]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<head>
    <title>クラスカレンダー</title>
    <link rel="stylesheet" href="css/class_calendar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
</head>
<div class="main-content-wrapper">
    <?php include 'includes/class_sidebar.php'?>

    <!-- メインコンテンツ -->
    <main class="class-main-content col-12 col-md-9 col-lg-10 p-5"
          style="min-height: 100vh; margin-left: 320px; width: calc(100% - 320px);">
        <div class="calendar-container">
            <h2>行事予定カレンダー</h2>
            <div id="calendar" class="w-100"></div>
            <div id="tooltip" class="custom-tooltip"></div>
        </div>

        <!-- モーダル（フォームつき） -->
        <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form action="calendar_event.php" method="POST">
                    <input type="hidden" name="func" value="insert">
                    <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
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
                            <input type="hidden" name="class_id" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('本当に削除しますか？');">削除</button>
                        </form>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    if (calendarEl) {
        var events = <?php echo json_encode($events, JSON_UNESCAPED_UNICODE); ?>;
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ja',
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
});
</script>
