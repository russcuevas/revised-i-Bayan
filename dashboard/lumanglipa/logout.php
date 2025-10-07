<?php
include '../../database/connection.php';
session_start();

$barangay = basename(__DIR__);
$resident_key = "resident_id_$barangay";
$log_id_key = "log_id_resident_$barangay";

if (isset($_SESSION[$resident_key])) {
    $resident_id = $_SESSION[$resident_key];

    // Set user offline
    $stmt = $conn->prepare("UPDATE tbl_residents SET is_online = 'offline' WHERE id = ?");
    $stmt->execute([$resident_id]);

    // Log system logout time
    if (isset($_SESSION[$log_id_key])) {
        $log_id = $_SESSION[$log_id_key];
        $update_log = $conn->prepare("UPDATE tbl_system_logs_residents SET logged_out = NOW() WHERE id = ?");
        $update_log->execute([$log_id]);
    }

    // ✅ Insert activity log for logout
    $barangay_stmt = $conn->prepare("SELECT id FROM tbl_barangay WHERE LOWER(REPLACE(barangay_name, ' ', '')) = ?");
    $barangay_stmt->execute([strtolower($barangay)]);
    $barangay_data = $barangay_stmt->fetch(PDO::FETCH_ASSOC);

    $barangay_id = $barangay_data['id'] ?? null;

    if ($barangay_id) {
        $insert_log = $conn->prepare("INSERT INTO tbl_activity_logs (resident_id, action, barangay_id, created_at)
            VALUES (:resident_id, :action, :barangay_id, NOW())");

        $insert_log->execute([
            ':resident_id' => $resident_id,
            ':action' => 'Logged out to the system',
            ':barangay_id' => $barangay_id
        ]);
    }
}

// Clear all session data
session_unset();
session_destroy();

// Redirect to login page
header('Location: ../../login.php');
exit;
