<?php
session_start();
include '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room_id = $_POST['room_id'] ?? null;
    $resident_id = $_POST['resident_id'] ?? null;
    $admin_id = $_POST['admin_id'] ?? null;
    $sender_type = $_POST['sender_type'] ?? 'admin'; // default: admin
    $message = trim($_POST['message'] ?? '');

    if ($room_id && $resident_id && $admin_id && $message !== '') {
        // Save the message
        $stmt = $conn->prepare("INSERT INTO tbl_chats (room_id, resident_id, admin_id, message, sender_type, chat_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$room_id, $resident_id, $admin_id, $message, $sender_type]);

        // Redirect back to the chat with the selected resident
        header("Location: live_chat.php?resident_id=$resident_id");
        exit();
    } else {
        // Incomplete form data
        echo "Missing information. Message not sent.";
    }
} else {
    // Invalid request
    header("Location: live_chat.php");
    exit();
}
