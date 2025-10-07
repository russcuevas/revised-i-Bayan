<?php
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

include '../../database/connection.php';

$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

$announcement_stmt = $conn->prepare("SELECT * FROM tbl_announcement WHERE barangay = ? AND status = 'active'");
$announcement_stmt->execute([$admin_barangay_id]);
$announcements = $announcement_stmt->fetchAll(PDO::FETCH_ASSOC);

$barangay_stmt = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
$barangay_stmt->execute([$admin_barangay_id]);
$barangay_name = $barangay_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Announcements</title>
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 13px;
        }

        th {
            background-color: #f2f2f2;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>

    <div class="text-center">
        <h2>Barangay <span style="text-transform: capitalize;"><?= htmlspecialchars($barangay_name) ?></span> Announcements</h2>
        <p>Generated on: <?= date('F d, Y h:i A') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Content</th>
                <th>Venue</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($announcements as $announcement): ?>
                <tr>
                    <td><?= htmlspecialchars($announcement['announcement_title']) ?></td>
                    <td><?= htmlspecialchars($announcement['announcement_content']) ?></td>
                    <td><?= htmlspecialchars($announcement['announcement_venue']) ?></td>
                </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

    <br>
    <div class="no-print text-center">
        <button onclick="window.print()" class="btn btn-primary">Print this page</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>

</html>