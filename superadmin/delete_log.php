<?php
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

include '../database/connection.php';

if (!isset($_GET['id'], $_GET['type'])) {
    die("Invalid request.");
}

$id = (int)$_GET['id'];
$type = strtolower(trim($_GET['type']));

$tables = [
    'superadmin' => ['table' => 'tbl_system_logs_superadmin', 'id_col' => 'id'],
    'admin' => ['table' => 'tbl_system_logs_admin', 'id_col' => 'id'],
    'resident' => ['table' => 'tbl_system_logs_residents', 'id_col' => 'id'],
];

// Map aliases to main keys
$aliases = [
    'staff' => 'admin',
    'barangay official' => 'admin',
];

// Normalize type to main key
$type = strtolower(trim($_GET['type']));
if (isset($aliases[$type])) {
    $type = $aliases[$type];
}

if (!array_key_exists($type, $tables)) {
    die("Invalid log type.");
}

$table = $tables[$type]['table'];
$id_col = $tables[$type]['id_col'];

try {
    $sql = "DELETE FROM `$table` WHERE `$id_col` = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Log deleted successfully.";
    } else {
        $_SESSION['error'] = "Log not found or already deleted.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Error deleting log: " . $e->getMessage();
}

header("Location: system_logs.php");
exit();
