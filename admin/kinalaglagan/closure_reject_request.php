<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['closure_id']) || !is_numeric($_GET['closure_id'])) {
    echo "Invalid request.";
    exit();
}

$closure_id = $_GET['closure_id'];

$stmt = $conn->prepare("SELECT * FROM tbl_closure WHERE id = ?");
$stmt->execute([$closure_id]);
$closure = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$closure) {
    $_SESSION['error'] = "Request not found.";
    header("Location: certificate_closure.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM tbl_closure WHERE id = ?");
if ($stmt->execute([$closure_id])) {
    $_SESSION['success'] = "Request has been rejected and deleted.";
} else {
    $_SESSION['error'] = "Failed to reject request.";
}

header("Location: certificate_closure.php");
exit();
