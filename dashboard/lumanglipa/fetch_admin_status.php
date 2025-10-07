<?php
include '../../database/connection.php';

session_start();
$barangay = basename(__DIR__);
$session_key = "resident_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    echo json_encode([]);
    exit();
}

$resident_id = $_SESSION[$session_key];

// Get resident's barangay
$stmt = $conn->prepare("SELECT barangay_address FROM tbl_residents WHERE id = ?");
$stmt->execute([$resident_id]);
$barangay_id = $stmt->fetchColumn();

if (!$barangay_id) {
    echo json_encode([]);
    exit();
}

$admin_stmt = $conn->prepare("SELECT id, fullname AS name, status FROM tbl_admin WHERE barangay_id = ?");
$admin_stmt->execute([$barangay_id]);
$admins = $admin_stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($admins);
