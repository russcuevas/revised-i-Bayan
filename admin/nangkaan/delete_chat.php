<?php
session_start();
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'] ?? null;

    if (!$room_id) {
        $_SESSION['error'] = "Missing room ID.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    try {
        // Delete all messages from the specified room_id
        $stmt = $conn->prepare("DELETE FROM tbl_chats WHERE room_id = ?");
        $stmt->execute([$room_id]);

        // $_SESSION['success'] = "Chat history successfully deleted.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Failed to delete chat: " . $e->getMessage();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
