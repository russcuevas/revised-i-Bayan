<?php
include '../database/connection.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($username !== '') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_residents WHERE username = :username");
        $stmt->execute([':username' => $username]);
        $exists = $stmt->fetchColumn();

        $response = $exists > 0
            ? ['status' => 'exists', 'message' => 'Username is already taken']
            : ['status' => 'available', 'message' => 'Username is available'];
    } elseif ($email !== '') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_residents WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $exists = $stmt->fetchColumn();

        $response = $exists > 0
            ? ['status' => 'exists', 'message' => 'Email is already registered']
            : ['status' => 'available', 'message' => 'Email is available'];
    } else {
        $response = ['status' => 'error', 'message' => 'No username or email provided'];
    }
}

echo json_encode($response);
