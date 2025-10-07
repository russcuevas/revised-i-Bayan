<?php
// session
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

// if not logged in
if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// database connection
include '../../database/connection.php';

// if no existing id invalid request
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// fetch the data
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT profile_picture FROM tbl_barangay_officials WHERE id = :id");
$stmt->execute([':id' => $id]);
$official = $stmt->fetch(PDO::FETCH_ASSOC);

// if no official barangay official no found
if (!$official) {
    $_SESSION['error'] = "Barangay official not found.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

if (!empty($official['profile_picture']) && $official['profile_picture'] !== 'profile_picture/default_profile.png') {
    $picture_path = '../../' . $official['profile_picture'];
    if (file_exists($picture_path)) {
        unlink($picture_path);
    }
}

$delete_stmt = $conn->prepare("DELETE FROM tbl_barangay_officials WHERE id = :id");
$delete_stmt->execute([':id' => $id]);


$_SESSION['success'] = "Barangay official deleted successfully.";
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
