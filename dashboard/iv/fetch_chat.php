<?php
session_start();
include '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = $_POST['room_id'] ?? null;

    if (!$room_id) {
        echo json_encode(['error' => 'No room_id provided']);
        exit;
    }

    // Fetch all chat messages for the given room_id
    $stmt = $conn->prepare("SELECT * FROM tbl_chats WHERE room_id = ? ORDER BY chat_at ASC");
    $stmt->execute([$room_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($messages);
}
