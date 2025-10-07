<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    echo json_encode([]);
    exit();
}

$admin_id = $_SESSION[$session_key];

// Get the barangay_id of this admin
$stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$stmt->execute([$admin_id]);
$barangay_id = $stmt->fetchColumn();

if (!$barangay_id) {
    echo json_encode([]);
    exit();
}

// Fetch residents with online status
$res_stmt = $conn->prepare("SELECT id, is_online FROM tbl_residents WHERE barangay_address = ? AND is_approved = 1");
$res_stmt->execute([$barangay_id]);
$residents = $res_stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($residents);
