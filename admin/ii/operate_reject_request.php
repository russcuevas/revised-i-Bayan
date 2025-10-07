<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['operate_id']) || !is_numeric($_GET['operate_id'])) {
    echo "Invalid request.";
    exit();
}

$operate_id = $_GET['operate_id'];

$stmt = $conn->prepare("SELECT * FROM tbl_operate WHERE id = ?");
$stmt->execute([$operate_id]);
$operate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$operate) {
    $_SESSION['error'] = "Request not found.";
    header("Location: certificate_operate.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM tbl_operate WHERE id = ?");
if ($stmt->execute([$operate_id])) {
    $_SESSION['success'] = "Request has been rejected and deleted.";
} else {
    $_SESSION['error'] = "Failed to reject request.";
}

header("Location: certificate_operate.php");
exit();
