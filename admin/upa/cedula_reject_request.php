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
if (!isset($_GET['cedula_id']) || !is_numeric($_GET['cedula_id'])) {
    $_SESSION['error'] = "Invalid request ID.";
    header("Location: certificate_issuance.php");
    exit();
}

$cedula_id = $_GET['cedula_id'];

// Delete the certificate request
$stmt = $conn->prepare("DELETE FROM tbl_cedula WHERE id = ?");
if ($stmt->execute([$cedula_id])) {
    $_SESSION['success'] = "Cedula request rejected successfully.";
} else {
    $_SESSION['error'] = "Failed to reject the certificate request.";
}

header("Location: certificate_issuance.php");
exit();
