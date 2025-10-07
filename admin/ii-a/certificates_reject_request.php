<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

// Check admin session
if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// Check if certificate_id is provided
if (!isset($_GET['certificate_id']) || !is_numeric($_GET['certificate_id'])) {
    $_SESSION['error'] = "Invalid request ID.";
    header("Location: certificate_issuance.php");
    exit();
}

$certificate_id = $_GET['certificate_id'];

// Delete the certificate request
$stmt = $conn->prepare("DELETE FROM tbl_certificates WHERE id = ?");
if ($stmt->execute([$certificate_id])) {
    $_SESSION['success'] = "Certificate request rejected successfully.";
} else {
    $_SESSION['error'] = "Failed to reject the certificate request.";
}

header("Location: certificate_issuance.php");
exit();
