<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "resident_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../../login.php");
    exit();
}

$resident_id = $_SESSION[$session_key];

// Fetch resident info
$stmt = $conn->prepare("SELECT barangay_address FROM tbl_residents WHERE id = ?");
$stmt->execute([$resident_id]);
$resident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resident) {
    $_SESSION['error'] = "Resident not found.";
    header("Location: ../../login.php");
    exit();
}

$barangay_id = $resident['barangay_address'];

// Fetch all admins in the same barangay with unread count
$admin_stmt = $conn->prepare("SELECT a.id, a.fullname AS name, a.status, COUNT(c.id) AS unread_count
    FROM tbl_admin a
    LEFT JOIN tbl_chats c ON a.id = c.admin_id AND c.resident_id = ? AND c.sender_type = 'admin' AND c.is_read = 0
    WHERE a.barangay_id = ?
    GROUP BY a.id");
$admin_stmt->execute([$resident_id, $barangay_id]);
$admin_list = $admin_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected admin
$selected_admin_id = $_GET['admin_id'] ?? null;

$room_id = null;
$chat_messages = [];
$selected_admin_name = 'No Admin Selected';

if ($selected_admin_id) {
    // Get selected admin's name
    foreach ($admin_list as $admin) {
        if ($admin['id'] == $selected_admin_id) {
            $selected_admin_name = $admin['name'];
            break;
        }
    }

    // Get or generate room ID for this resident-admin pair
    $room_stmt = $conn->prepare("SELECT room_id FROM tbl_chats WHERE resident_id = ? AND admin_id = ? LIMIT 1");
    $room_stmt->execute([$resident_id, $selected_admin_id]);
    $room_id = $room_stmt->fetchColumn();

    if (!$room_id) {
        $room_id = uniqid("room_");
    }

    // Mark messages as read
    $update_read = $conn->prepare("UPDATE tbl_chats SET is_read = 1 WHERE room_id = ? AND sender_type = 'admin'");
    $update_read->execute([$room_id]);

    // Fetch chat messages
    $chat_stmt = $conn->prepare("SELECT * FROM tbl_chats WHERE room_id = ? ORDER BY chat_at ASC");
    $chat_stmt->execute([$room_id]);
    $chat_messages = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iBayan - Resident Chat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Sweetalert Css -->
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />
</head>

<body>
    <div class="container-fluid mt-3">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 border-end">
                <h5 class="text-left">Barangay Admins</h5>
                <a href="index.php" class="btn btn-primary mb-3">Go back</a>
                <div class="list-group" id="admin-list">
                    <?php foreach ($admin_list as $admin): ?>
                        <a href="?admin_id=<?= $admin['id'] ?>"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= ($selected_admin_id == $admin['id']) ? 'active' : '' ?>"
                            data-admin-id="<?= $admin['id'] ?>">
                            <span><?= htmlspecialchars($admin['name']) ?></span>
                            <span class="d-flex align-items-center gap-1">
                                <?php if ($admin['unread_count'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill"><?= $admin['unread_count'] ?></span>
                                <?php endif; ?>
                                <span class="status-dot" style="width:10px; height:10px; border-radius:50%; background-color:gray; display:inline-block;"></span>

                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>

            <!-- Chat Area -->
            <div class="col-md-9 d-flex flex-column" style="height: 80vh;">
                <?php if ($room_id): ?>
                    <div class="border-bottom p-2 bg-primary text-white d-flex justify-content-between align-items-center">
                        <strong>Chat with: <?= htmlspecialchars($selected_admin_name) ?> (Room ID: <?= $room_id ?>)</strong>
                        <form method="POST" action="delete_chat.php" onsubmit="return confirm('Delete chat?')">
                            <input type="hidden" name="room_id" value="<?= $room_id ?>">
                            <button class="btn btn-sm btn-danger">Delete Chat</button>
                        </form>
                    </div>

                    <div class="flex-grow-1 overflow-auto p-3 bg-light" id="chat-box">
                        <?php foreach ($chat_messages as $msg): ?>
                            <div class="mb-2 <?= $msg['sender_type'] === 'resident' ? 'text-end' : 'text-start' ?>">
                                <div class="d-inline-block p-2 rounded <?= $msg['sender_type'] === 'resident' ? 'bg-primary text-white' : 'bg-white border' ?>">
                                    <?= htmlspecialchars($msg['message']) ?>
                                </div>
                                <div class="small text-muted">
                                    <?= date('Y-m-d - h:i A', strtotime($msg['chat_at'])) ?>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Chat Form -->
                    <form action="send_chat.php" method="POST" class="mt-2 d-flex">
                        <input type="hidden" name="room_id" value="<?= $room_id ?>">
                        <input type="hidden" name="resident_id" value="<?= $resident_id ?>">
                        <input type="hidden" name="admin_id" value="<?= $selected_admin_id ?>">
                        <input type="hidden" name="sender_type" value="resident">
                        <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                        <button class="btn btn-primary ms-2"><i class="bi bi-send"></i> Send</button>
                    </form>
                <?php else: ?>
                    <div class="p-4 text-muted">Select an admin to start chatting.</div>
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
                        messageDiv.className = 'mb-2 ' + (msg.sender_type === 'resident' ? 'text-end' : 'text-start');

                        messageDiv.innerHTML = `
                    <div class="d-inline-block p-2 rounded ${msg.sender_type === 'resident' ? 'bg-primary text-white' : 'bg-white border'}">
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
            hours = hours ? hours : 12; // convert 0 to 12 for 12-hour format
            const formattedHours = String(hours).padStart(2, '0');

            return `${year}-${month}-${day} - ${formattedHours}:${minutes} ${ampm}`;
        }




        // Auto-refresh every 3 seconds
        setInterval(fetchChats, 1000);
    </script>

    <script>
        function updateAdminStatus() {
            fetch('fetch_admin_status.php')
                .then(res => res.json())
                .then(data => {
                    data.forEach(admin => {
                        const anchor = document.querySelector(`[data-admin-id='${admin.id}']`);
                        if (anchor) {
                            const dot = anchor.querySelector('.status-dot');
                            if (dot) {
                                dot.style.backgroundColor = (admin.status === 'online') ? 'green' : 'red';
                            }
                        }
                    });
                });
        }

        updateAdminStatus();
        setInterval(updateAdminStatus, 5000);
    </script>
    <!-- SweetAlert Plugin Js -->
    <script src="../plugins/sweetalert/sweetalert.min.js"></script>
    <script>
        <?php if (isset($_SESSION['success'])): ?>
            swal({
                type: 'success',
                title: 'Success!',
                text: '<?php echo $_SESSION['success']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            swal({
                type: 'error',
                title: 'Oops...',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </script>
</body>

</html>