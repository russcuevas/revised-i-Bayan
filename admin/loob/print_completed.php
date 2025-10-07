<?php
// session
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

// if not logged in
if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// database connection
include '../../database/connection.php';

// Fetch admin's barangay
$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

// Get barangay name
$barangay_stmt = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
$barangay_stmt->execute([$admin_barangay_id]);
$barangay_name = $barangay_stmt->fetchColumn();

// Tables and configuration
$claimed_tables = [
    'tbl_certificates_claimed' => ['amount_column' => 'total_amount_paid', 'name_column' => 'fullname', 'purok_column' => 'purok'],
    'tbl_cedula_claimed'       => ['amount_column' => 'total_amount', 'name_column' => 'fullname', 'purok_column' => 'purok'],
    'tbl_closure_claimed'      => ['amount_column' => 'total_amount', 'name_column' => 'owner_name', 'purok_column' => 'owner_purok'],
    'tbl_operate_claimed'      => ['amount_column' => 'total_amount', 'name_column' => 'owner_name', 'purok_column' => 'owner_purok'],
];

$all_claimed = [];

foreach ($claimed_tables as $table => $columns) {
    $amount_column = $columns['amount_column'];
    $name_column = $columns['name_column'];
    $purok_column = $columns['purok_column'];

    $stmt = $conn->prepare("
        SELECT 
            document_number, 
            certificate_type, 
            $name_column AS fullname, 
            $amount_column AS total_amount,
            $purok_column AS purok,
            status
        FROM $table 
        WHERE for_barangay = ?
    ");

    $stmt->execute([$admin_barangay_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $all_claimed = array_merge($all_claimed, $results);
}
// Optional: Sort by document_number
usort($all_claimed, function ($a, $b) {
    return strcmp($a['document_number'], $b['document_number']);
});
?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Completed Certificate Emails</title>
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        th {
            background-color: #f0f0f0;
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <h2>Barangay <span style="text-transform: capitalize;"><?= htmlspecialchars($barangay_name) ?></span> <br> Completed Certificate Email Report</h2>
    <p class="text-center">Generated on: <?= date('F d, Y h:i A') ?></p>

    <table>
        <thead>
            <tr>
                <th>Document ID</th>
                <th>Certificate Type</th>
                <th>Fullname</th>
                <th>Purok</th>
                <th>Amount Paid</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($all_claimed as $claim): ?>
                <tr>
                    <td><?= htmlspecialchars($claim['document_number']) ?></td>
                    <td><?= htmlspecialchars($claim['certificate_type']) ?></td>
                    <td><?= htmlspecialchars($claim['fullname']) ?></td>
                    <td><?= htmlspecialchars($claim['purok']) ?></td>
                    <td>₱<?= number_format((float)$claim['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($claim['status']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-primary">Print this page</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>

</html>