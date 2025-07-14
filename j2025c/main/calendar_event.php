<?php
require_once('common/dbmanager.php');
require_once('common/session.php');

$user = get_login_user();
if (!$user) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new cdb();
    if (isset($_POST['func']) && $_POST['func'] === 'insert') {
        $title = trim($_POST['event_title'] ?? '');
        $description = trim($_POST['event_desc'] ?? '');
        $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
        $start_date = $_POST['event_date'] ?? date('Y-m-d');

        // デバッグ用: class_idの値を確認
        // echo "class_id: $class_id"; exit;

        if ($title && $class_id > 0 && $start_date) {
            // クラスIDが存在するかチェック
            $stmt = $db->prepare('SELECT 1 FROM classes WHERE class_id = ?');
            $stmt->execute([$class_id]);
            if ($stmt->fetch()) {
                $stmt = $db->prepare('INSERT INTO calendar_events (class_id, title, description, start_date) VALUES (?, ?, ?, ?)');
                $stmt->execute([$class_id, $title, $description, $start_date]);
            }
        }
    } elseif (isset($_POST['func']) && $_POST['func'] === 'delete') {
        $event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
        $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
        if ($event_id > 0 && $class_id > 0) {
            $stmt = $db->prepare('DELETE FROM calendar_events WHERE event_id = ? AND class_id = ?');
            $stmt->execute([$event_id, $class_id]);
        }
    }
}
header('Location: class_calender.php?id=' . ($class_id ?? ''));
exit;
