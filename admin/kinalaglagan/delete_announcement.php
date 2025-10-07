<?php
session_start();
include '../../database/connection.php';

// auth check
$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// get announcement ID
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    die('Invalid request.');
}

// fetch announcement to get image filename
$stmt = $conn->prepare("SELECT announcement_image FROM tbl_announcement WHERE id = ?");
$stmt->execute([$id]);
$announcement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$announcement) {
    die('Announcement not found.');
}

// delete image if exists
if ($announcement['announcement_image']) {
    $image_path = '../../public/announcement/' . $announcement['announcement_image'];
    if (file_exists($image_path)) {
        unlink($image_path); // delete image file
    }
}

// delete announcement from database
$stmt = $conn->prepare("DELETE FROM tbl_announcement WHERE id = ?");
$stmt->execute([$id]);

// optional: add a session success message
$_SESSION['success'] = "Announcement deleted successfully.";

// redirect back
header("Location: announcements.php");
exit();
