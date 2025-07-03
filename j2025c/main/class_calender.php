<?php
include 'includes/header.php';
include 'includes/sidebar.php';
?>
<head>
    <title>クラスカレンダー</title>
    <link rel="stylesheet" href="../main/css/class_calender.css">
    <script src="../main/js/class_calender.js"></script>
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
                    <input type="hidden" name="event_date" id="event_date">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="eventModalLabel">予定を追加</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="event_date" class="form-label">日付</label>
                                <input type="text" class="form-control" id="event_date" name="event_date" readonly required>
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
    </main>
</div>
