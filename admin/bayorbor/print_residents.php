<?php
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../../database/connection.php';
$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

$filter = isset($_GET['filter']) ? strtolower(trim($_GET['filter'])) : '';
$purok_filter = $_GET['purok'] ?? '';
$gender_filter = $_GET['gender'] ?? '';
$age_filter = $_GET['age'] ?? '';

$query = "SELECT *, TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) AS age 
          FROM tbl_residents_family_members 
          WHERE barangay_address = ? AND is_approved = 1";
$params = [$admin_barangay_id];

if ($filter === 'working') {
    $query .= " AND is_working = 1";
} elseif ($filter === 'student') {
    $query .= " AND is_working = 2";
} elseif ($filter === 'none') {
    $query .= " AND is_working = 3";
} elseif ($filter === 'senior') {
    $query .= " AND is_working = 4";
} elseif ($filter === 'ofw') {
    $query .= " AND is_working = 1 AND LOWER(occupation) LIKE '%ofw%'";
}

if (!empty($purok_filter)) {
    $query .= " AND purok = ?";
    $params[] = $purok_filter;
}

if (!empty($gender_filter)) {
    $query .= " AND gender = ?";
    $params[] = $gender_filter;
}

if (!empty($age_filter)) {
    if ($age_filter === 'below18') {
        $query .= " AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) < 18";
    } elseif ($age_filter === '18to30') {
        $query .= " AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 18 AND 30";
    } elseif ($age_filter === '31to50') {
        $query .= " AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) BETWEEN 31 AND 50";
    } elseif ($age_filter === '51plus') {
        $query .= " AND TIMESTAMPDIFF(YEAR, date_of_birth, CURDATE()) >= 51";
    }
}

// Execute query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get barangay name
$barangay_stmt = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
$barangay_stmt->execute([$admin_barangay_id]);
$barangay_name = $barangay_stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Residents Report</title>
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
        <h2>Barangay <?= htmlspecialchars(ucwords($barangay_name)) ?> Residents Report</h2>
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
            <?php if (count($members) > 0): ?>
                <?php foreach ($members as $member): ?>
                    <tr>
                        <td>
                            <?php
                            $full_name = htmlspecialchars(
                                trim(
                                    $member['first_name'] . ' ' .
                                    ($member['middle_name'] ? $member['middle_name'][0] . '. ' : '') .
                                    $member['last_name'] . ' ' .
                                    ($member['suffix'] ?? '')
                                )
                            );
                            echo $full_name;
                            ?>
                        </td>
                        <td><?= htmlspecialchars($member['purok'] ?? '') ?></td>
                        <td><?= htmlspecialchars($member['gender'] ?? '') ?></td>
                        <td><?= htmlspecialchars($member['date_of_birth'] ?? '') ?></td>
                        <td><?= htmlspecialchars($member['birthplace'] ?? '') ?></td>
                        <td><?= htmlspecialchars($member['age'] ?? '') ?></td>
                        <td><?= htmlspecialchars($member['civil_status'] ?? '') ?></td>
                        <td>
                            <?php
                            $is_working = $member['is_working'];
                            $occupation = strtolower(trim($member['occupation'] ?? ''));

                            if ($is_working == 1) {
                                echo (str_contains($occupation, 'ofw')) ? 'OFW' : 'Working';
                            } elseif ($is_working == 2) {
                                echo 'Student';
                            } elseif ($is_working == 3) {
                                echo 'None';
                            } elseif ($is_working == 4) {
                                echo 'Senior Citizen';
                            } else {
                                echo 'Unknown';
                            }
                            ?>
                        </td>
                        <td><?= $member['is_barangay_voted'] ? 'Yes' : 'No' ?></td>
                        <td><?= htmlspecialchars($member['years_in_barangay'] ?? '') ?></td>
                        <td><?= htmlspecialchars($member['phone_number'] ?? '') ?></td>
                        <td><?= htmlspecialchars($member['philhealth_number'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="12" class="text-center text-danger">No residents found for this filter.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="no-print">
        <button onclick="window.print()" class="btn btn-danger">🖨 Print</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>

</body>
</html>
