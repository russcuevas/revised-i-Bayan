<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION[$session_key];

// Fetch barangay of admin
$stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$stmt->execute([$admin_id]);
$barangay_id = $stmt->fetchColumn();

// Fetch only approved residents in the same barangay
$resident_stmt = $conn->prepare("SELECT id, CONCAT(last_name, ', ', first_name) AS name FROM tbl_residents WHERE barangay_address = ? AND is_approved = 1");
$resident_stmt->execute([$barangay_id]);
$residents = $resident_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unread message counts
$unreadCounts = [];
$unread_stmt = $conn->prepare("SELECT resident_id, COUNT(*) AS unread_count FROM tbl_chats WHERE admin_id = ? AND sender_type = 'resident' AND is_read = 0 GROUP BY resident_id");
$unread_stmt->execute([$admin_id]);
foreach ($unread_stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $unreadCounts[$row['resident_id']] = $row['unread_count'];
}

// Handle selected resident
$selected_resident_id = $_GET['resident_id'] ?? null;
$room_id = null;
$chat_messages = [];
$selected_resident_name = null;

if ($selected_resident_id) {
    // Fetch selected resident's name
    foreach ($residents as $res) {
        if ($res['id'] == $selected_resident_id) {
            $selected_resident_name = $res['name'];
            break;
        }
    }

    // Get or generate room ID
    $room_stmt = $conn->prepare("SELECT room_id FROM tbl_chats WHERE resident_id = ? AND admin_id = ? LIMIT 1");
    $room_stmt->execute([$selected_resident_id, $admin_id]);
    $room_id = $room_stmt->fetchColumn();

    if (!$room_id) {
        $room_id = uniqid("room_");
    }

    // Mark messages as read
    $mark_read_stmt = $conn->prepare("UPDATE tbl_chats SET is_read = 1 WHERE room_id = ? AND sender_type = 'resident' AND admin_id = ? AND resident_id = ?");
    $mark_read_stmt->execute([$room_id, $admin_id, $selected_resident_id]);

    // Fetch messages
    $chat_stmt = $conn->prepare("SELECT * FROM tbl_chats WHERE room_id = ? ORDER BY chat_at ASC");
    $chat_stmt->execute([$room_id]);
    $chat_messages = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iBayan - Admin Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <div class="col-md-3 border-end">
                <h5 class="text-left mb-3">Residents</h5>
                <div class="list-group" id="resident-list" 
                    style="max-height: 70vh; overflow-y: auto;">
                    <?php foreach ($residents as $res): ?>
                        <?php $unread = $unreadCounts[$res['id']] ?? 0; ?>
                        <a href="?resident_id=<?= $res['id'] ?>"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= ($selected_resident_id == $res['id']) ? 'active' : '' ?>"
                            data-resident-id="<?= $res['id'] ?>">
                            <span><?= htmlspecialchars($res['name']) ?></span>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($unread > 0): ?>
                                    <span class="badge bg-danger"><?= $unread ?></span>
                                <?php endif; ?>
                                <span class="status-dot" 
                                    style="width: 10px; height: 10px; border-radius: 50%; background-color: gray; display: inline-block;">
                                </span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>


            <!-- Chat Area -->
            <div class="col-md-9 d-flex flex-column" style="height: 80vh;">
                <?php if ($room_id): ?>
                    <div class="border-bottom p-2 bg-primary text-white d-flex justify-content-between align-items-center">
                        <strong>Chat with <?= htmlspecialchars($selected_resident_name) ?> (Room: <?= $room_id ?>)</strong>
                        <form method="POST" action="delete_chat.php" onsubmit="return confirm('Delete chat?')">
                            <input type="hidden" name="room_id" value="<?= $room_id ?>">
                            <button class="btn btn-sm btn-danger">Delete Chat</button>
                        </form>
                    </div>

                    <div class="flex-grow-1 overflow-auto p-3 bg-light" id="chat-box">
                        <?php foreach ($chat_messages as $msg): ?>
                            <div class="mb-2 <?= $msg['sender_type'] === 'admin' ? 'text-end' : 'text-start' ?>">
                                <div class="d-inline-block p-2 rounded <?= $msg['sender_type'] === 'admin' ? 'bg-primary text-white' : 'bg-white border' ?>">
                                    <?= htmlspecialchars($msg['message']) ?>
                                </div>
                                <div class="small text-muted">
                                    <?= date('Y-m-d - h:i A', strtotime($msg['chat_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Chat Input -->
                    <form action="send_chat.php" method="POST" class="mt-2 d-flex">
                        <input type="hidden" name="room_id" value="<?= $room_id ?>">
                        <input type="hidden" name="resident_id" value="<?= $selected_resident_id ?>">
                        <input type="hidden" name="admin_id" value="<?= $admin_id ?>">
                        <input type="hidden" name="sender_type" value="admin">
                        <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                        <button class="btn btn-primary ms-2"><i class="bi bi-send"></i> Send</button>
                    </form>
                <?php else: ?>
                    <div class="p-4 text-muted">Select a resident to start chatting.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        const box = document.getElementById("chat-box");
        if (box) box.scrollTop = box.scrollHeight;
    </script>

    <script>
        function fetchChats() {
            const roomId = "<?= $room_id ?>";
            if (!roomId) return;

            fetch('fetch_chat.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'room_id=' + encodeURIComponent(roomId)
                })
                .then(response => response.json())
                .then(data => {
                    const chatBox = document.getElementById('chat-box');
                    if (!chatBox) return;

                    chatBox.innerHTML = '';

                    data.forEach(msg => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'mb-2 ' + (msg.sender_type === 'admin' ? 'text-end' : 'text-start');
                        messageDiv.innerHTML = `
                            <div class="d-inline-block p-2 rounded ${msg.sender_type === 'admin' ? 'bg-primary text-white' : 'bg-white border'}">
                                ${escapeHTML(msg.message)}
                            </div>
                            <div class="small text-muted">
                                ${formatTime(msg.chat_at)}
                            </div>
                        `;
                        chatBox.appendChild(messageDiv);
                    });

                    chatBox.scrollTop = chatBox.scrollHeight;
                });
        }

        // Utility function to escape HTML (avoid XSS)
        function escapeHTML(str) {
            return str.replace(/[&<>'"]/g, tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            } [tag]));
        }

        // Format timestamp to hh:mm AM/PM
        function formatTime(datetime) {
            const date = new Date(datetime);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            let hours = date.getHours();
            const minutes = String(date.getMinutes()).padStart(2, '0');
            const ampm = hours >= 12 ? 'PM' : 'AM';

            hours = hours % 12;
            hours = hours ? hours : 12; // 0 becomes 12
            const formattedHours = String(hours).padStart(2, '0');

            return `${year}-${month}-${day} - ${formattedHours}:${minutes} ${ampm}`;
        }


        // Auto-refresh every 3 seconds
        setInterval(fetchChats, 1000);
    </script>

    <script>
        function updateResidentStatus() {
            fetch('fetch_resident_status.php')
                .then(res => res.json())
                .then(data => {
                    data.forEach(resident => {
                        const anchor = document.querySelector(`[data-resident-id='${resident.id}']`);
                        if (anchor) {
                            const dot = anchor.querySelector('.status-dot');
                            if (dot) {
                                dot.style.backgroundColor = (resident.is_online === 'online') ? 'green' : 'red';
                            }
                        }
                    });
                });
        }

        // Initial and interval-based refresh
        updateResidentStatus();
        setInterval(updateResidentStatus, 3000);
    </script>

</body>

</html>