<?php
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

include '../database/connection.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Start transaction to ensure both deletes succeed together
        $conn->beginTransaction();

        // Delete related logs first
        $stmt = $conn->prepare("DELETE FROM tbl_system_logs_admin WHERE admin_id = ?");
        $stmt->execute([$id]);

        // Delete admin
        $stmt = $conn->prepare("DELETE FROM tbl_admin WHERE id = ?");
        $stmt->execute([$id]);

        $conn->commit();

        $_SESSION['success'] = "Admin deleted successfully!";
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Failed to delete admin: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid Admin ID.";
}

header("Location: admin_management.php");
exit;
