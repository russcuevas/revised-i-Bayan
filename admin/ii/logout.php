<?php
include '../../database/connection.php';

session_start();

$barangay = basename(__DIR__);
$admin_id_key = "admin_id_$barangay";
$log_id_key = "log_id_admin_$barangay";

$admin_id = $_SESSION[$admin_id_key] ?? null;
$log_id = $_SESSION[$log_id_key] ?? null;

if ($admin_id) {
    // Update admin status to 'offline'
    $stmt = $conn->prepare("UPDATE tbl_admin SET status = 'offline' WHERE id = ?");
    $stmt->execute([$admin_id]);
}

if ($log_id) {
    // Update logged_out datetime for the session log
    $update_log = $conn->prepare("UPDATE tbl_system_logs_admin SET logged_out = NOW() WHERE id = ?");
    $update_log->execute([$log_id]);
}

// Clear session variables
unset($_SESSION["admin_id_$barangay"]);
unset($_SESSION["admin_name_$barangay"]);
unset($_SESSION["admin_position_$barangay"]);
unset($_SESSION["barangay_id_$barangay"]);
unset($_SESSION["barangay_name_$barangay"]);
unset($_SESSION[$log_id_key]);

header("Location: ../login.php");
exit();
