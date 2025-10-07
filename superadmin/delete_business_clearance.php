<?php
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

include '../database/connection.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM tbl_business_trade WHERE id = :id";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([':id' => $id]);
        $_SESSION['success'] = "Business deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete barangay: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid Business ID.";
}

header("Location: business_clearance.php");
exit;
