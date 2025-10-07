<?php
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

include '../../database/connection.php';

$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid family member ID.";
    header("Location: manage_residents.php");
    exit();
}

$id = (int)$_GET['id'];

// Check if family member exists and belongs to this barangay
$stmt = $conn->prepare("SELECT id FROM tbl_residents_family_members WHERE id = ? AND barangay_address = ?");
$stmt->execute([$id, $admin_barangay_id]);
$member = $stmt->fetchColumn();

if (!$member) {
    $_SESSION['error'] = "Family member not found or you don't have permission.";
    header("Location: manage_residents.php");
    exit();
}

// Delete the member
$del_stmt = $conn->prepare("DELETE FROM tbl_residents_family_members WHERE id = ? AND barangay_address = ?");
if ($del_stmt->execute([$id, $admin_barangay_id])) {
    $_SESSION['success'] = "Resident deleted successfully.";
} else {
    $_SESSION['error'] = "Failed to delete family member.";
}

header("Location: manage_residents.php");
exit();
