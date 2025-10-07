<?php
include '../database/connection.php';
session_start();

if (isset($_SESSION['log_id'])) {
    $log_id = $_SESSION['log_id'];

    $stmt = $conn->prepare("UPDATE tbl_system_logs_superadmin SET logged_out = NOW() WHERE id = :log_id");
    $stmt->execute([':log_id' => $log_id]);
}

session_unset();
session_destroy();

header('Location: login.php');
exit();
