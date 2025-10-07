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

// details welcome
$barangay_name_key = "barangay_name_$barangay";
$admin_name_key = "admin_name_$barangay";
$admin_position_key = "admin_position_$barangay";

// database connection
include '../../database/connection.php';

// fetching residents where in same of the barangay of the admin
$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

// Fetch all family members
$stmt = $conn->prepare("SELECT * FROM tbl_residents_family_members WHERE barangay_address = ? AND is_approved = 1");
$stmt->execute([$admin_barangay_id]);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

$barangay_stmt = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
$barangay_stmt->execute([$admin_barangay_id]);
$barangay_name = $barangay_stmt->fetchColumn();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Print Residents</title>
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
            font-size: 12px;
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
        <h2>Barangay <span style="text-transform: capitalize;"><?= htmlspecialchars($barangay_name) ?></span> Residents Report</h2>
        <p>Generated on: <?= date('F d, Y h:i A') ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Purok</th>
                <th>Gender</th>
                <th>Date of Birth</th>
                <th>Birthplace</th>
                <th>Age</th>
                <th>Civil Status</th>
                <th>Working?</th>
                <th>Barangay Voted?</th>
                <th>Years in Barangay</th>
                <th>Phone Number</th>
                <th>PhilHealth Number</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td>
                        <?php
                        $full_name = htmlspecialchars(
                            $member['first_name'] . ' ' .
                                $member['last_name'] . ' ' .
                                ($member['middle_name'] ? $member['middle_name'][0] . '. ' : '') .
                                ($member['suffix'] ? $member['suffix'] : '')
                        );
                        echo $full_name;
                        ?>
                    </td>
                    <td><?= htmlspecialchars($member['purok']) ?></td>
                    <td><?= htmlspecialchars($member['gender']) ?></td>
                    <td><?= htmlspecialchars($member['date_of_birth']) ?></td>
                    <td><?= htmlspecialchars($member['birthplace']) ?></td>
                    <td><?= htmlspecialchars($member['age']) ?></td>
                    <td><?= htmlspecialchars($member['civil_status']) ?></td>
                    <td>
                        <?php
                        $status_map = [
                            1 => 'Working',
                            2 => 'Student',
                            3 => 'None'
                        ];
                        echo $status_map[$member['is_working']] ?? 'Unknown';
                        ?>
                    </td>
                    <td><?= $member['is_barangay_voted'] ? 'Yes' : 'No' ?></td>
                    <td><?= htmlspecialchars($member['years_in_barangay']) ?></td>
                    <td><?= htmlspecialchars($member['phone_number']) ?></td>
                    <td><?= htmlspecialchars($member['philhealth_number']) ?></td>
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