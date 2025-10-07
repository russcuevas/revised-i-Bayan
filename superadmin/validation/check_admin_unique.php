<?php
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $value = $_POST['value'] ?? '';
    $id = $_POST['id'] ?? 0;

    if ($type === 'username') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_admin WHERE username = ? AND id != ?");
        $stmt->execute([$value, $id]);
        echo json_encode($stmt->fetchColumn() == 0);
        exit;
    }

    if ($type === 'email') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_admin WHERE email = ? AND id != ?");
        $stmt->execute([$value, $id]);
        echo json_encode($stmt->fetchColumn() == 0);
        exit;
    }
}

echo json_encode(false);
