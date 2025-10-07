<?php
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

include '../database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid ID.');
}

$id = (int)$_GET['id'];
$query = "
    SELECT c.*, 
           b.barangay_name, 
           bt.name AS business_trade_name
    FROM tbl_closure c
    LEFT JOIN tbl_barangay b ON c.for_barangay = b.id
    LEFT JOIN tbl_business_trade bt ON c.business_trade = bt.id
    WHERE c.id = :id
    LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->execute(['id' => $id]);
$closure = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$closure) {
    die('Closure record not found.');
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <title>Closure Certificate Details</title>
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
    <style>
        .info-row {
            margin-bottom: 15px;
        }

        .badge {
            font-size: 1em;
            padding: 0.5em 1em;
        }

        .left-column {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .right-column {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 8px;
        }

        .field-label {
            font-weight: 600;
            color: #333;
        }

        .field-value {
            color: #555;
        }

        .doc-link {
            text-decoration: none;
            color: #007bff;
        }

        .doc-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }
    </style>
</head>

<body class="theme-teal">
    <div class="container" style="margin-top: 30px; margin-bottom: 40px;">
        <h2 class="mb-3">Closure Certificate Details</h2>
        <p class="text-danger">NOTE: To be approved by the admin of the barangay</p>

        <div class="row g-4">
            <div class="col-md-6 left-column">

                <div class="info-row">
                    <div class="field-label">Certificate Type</div>
                    <div class="field-value"><?= htmlspecialchars($closure['certificate_type']) ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Fullname (Owner)</div>
                    <div class="field-value"><?= htmlspecialchars($closure['owner_name']) ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Owner's Purok</div>
                    <div class="field-value"><?= htmlspecialchars($closure['owner_purok']) ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Business Name</div>
                    <div class="field-value"><?= htmlspecialchars($closure['business_name']) ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Business Trade</div>
                    <div class="field-value"><?= htmlspecialchars($closure['business_trade_name'] ?? 'N/A') ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Business Address</div>
                    <div class="field-value"><?= nl2br(htmlspecialchars($closure['business_address'])) ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Purpose</div>
                    <div class="field-value"><?= nl2br(htmlspecialchars($closure['purpose'])) ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Barangay</div>
                    <div class="field-value" style="text-transform: capitalize;"><?= htmlspecialchars($closure['barangay_name'] ?? 'N/A') ?></div>
                </div>
            </div>

            <!-- Right: Status, Contact, Documents -->
            <div class="col-md-6 right-column">
                <div class="info-row">
                    <div class="field-label">Status</div>
                    <?php
                    $status = ucfirst($closure['status'] ?? 'Pending');
                    $badgeClass = 'btn-info'; // default for Pending
                    if ($status === 'Approved') {
                        $badgeClass = 'btn-success';
                    } elseif ($status === 'Rejected') {
                        $badgeClass = 'btn-danger';
                    }
                    ?>
                    <div><span class="badge <?= $badgeClass ?>"><?= $status ?></span></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Date Filed</div>
                    <div class="field-value"><?= date('F d, Y', strtotime($closure['created_at'])) ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Email</div>
                    <div class="field-value"><?= htmlspecialchars($closure['email']) ?: 'N/A' ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Contact Number</div>
                    <div class="field-value"><?= htmlspecialchars($closure['contact']) ?: 'N/A' ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Valid ID</div>
                    <div class="field-value">
                        <?php if (!empty($closure['valid_id'])): ?>
                            <a class="doc-link" href="../public/request/valid_id/<?= htmlspecialchars($closure['valid_id']) ?>" target="_blank">View Document</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="field-label">Birth Certificate</div>
                    <div class="field-value">
                        <?php if (!empty($closure['birth_certificate'])): ?>
                            <a class="doc-link" href="../public/request/birth_certificate/<?= htmlspecialchars($closure['birth_certificate']) ?>" target="_blank">View Document</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>

                <div class="info-row">
                    <div class="field-label">Picked Up By</div>
                    <div class="field-value"><?= htmlspecialchars($closure['picked_up_by']) ?: 'N/A' ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Relationship</div>
                    <div class="field-value" style="text-transform: capitalize;"><?= htmlspecialchars($closure['relationship']) ?: 'N/A' ?></div>
                </div>

                <div class="info-row">
                    <div class="field-label">Total Amount</div>
                    <div class="field-value">₱ <?= number_format($closure['total_amount'], 2) ?></div>
                </div>
            </div>

            <div class="mt-4">
                <a href="certificate_closure.php" class="btn btn-primary" style="float: right; margin-top: 10px;">Back</a>
            </div>
        </div>
    </div>
</body>

</html>