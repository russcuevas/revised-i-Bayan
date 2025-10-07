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

// Fetch barangay name
$barangay_stmt = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
$barangay_stmt->execute([$admin_barangay_id]);
$barangay_name = $barangay_stmt->fetchColumn();

// Fetch activity logs with resident names
$activity_logs_stmt = $conn->prepare("
    SELECT r.username AS username, 
           'Resident' AS usertype, 
           a.action, 
           a.created_at
    FROM tbl_activity_logs a
    LEFT JOIN tbl_residents r ON a.resident_id = r.id
    WHERE a.barangay_id = ?
    ORDER BY a.created_at DESC
");
$activity_logs_stmt->execute([$admin_barangay_id]);
$logs = $activity_logs_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Activity Logs</title>
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
        <h2>Barangay <span style="text-transform: capitalize;"><?= htmlspecialchars($barangay_name) ?></span> Activity Logs</h2>
        <p>Generated on: <?= date('F d, Y h:i A') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Usertype</th>
                <th>Action</th>
                <th>Datetime</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['username'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($log['usertype']) ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= date('M/d/Y h:i A', strtotime($log['created_at'])) ?></td>
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