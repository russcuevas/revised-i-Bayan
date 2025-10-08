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

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$closure_id = $_GET['id'];

$stmt = $conn->prepare("SELECT o.*, t.name AS business_type_name, b.barangay_name 
                        FROM tbl_closure o
                        LEFT JOIN tbl_business_trade t ON o.business_trade = t.id
                        LEFT JOIN tbl_barangay b ON o.for_barangay = b.id
                        WHERE o.id = ? AND o.resident_id = ?");
$stmt->execute([$closure_id, $resident_id]);
$closure = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$closure) {
    echo "Closure not found or access denied.";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iBayan</title>
    <link rel="icon" href="../img/logo.png" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../plugins/node-waves/waves.css" rel="stylesheet" />
    <link href="../plugins/animate-css/animate.css" rel="stylesheet" />
    <link href="../plugins/morrisjs/morris.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <link href="../css/themes/all-themes.css" rel="stylesheet" />
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />
</head>

<body class="theme-teal">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-teal">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <!-- Top Bar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a id="app-title" style="display:flex;align-items:center;" class="navbar-brand" href="index.php">
                    <img id="bcas-logo" style="width:45px;display:inline;margin-right:10px;" src="../img/logo.png" />
                    <div>
                        <div style="color: white;">iBayan</div>
                    </div>
                </a>

            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <!-- #END# Tasks -->
                    <li class="pull-right"><a href="javascript:void(0);" class="js-right-sidebar" data-close="true"><i
                                class="material-icons">account_circle</i></a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section>
        <?php include('left_sidebar.php'); ?>
        <?php include('right_sidebar.php'); ?>
    </section>
    <section class="content">
        <div class="container-fluid" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
            <div class="row clearfix" style="width: 100%; max-width: 800px;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card shadow" style="border-radius: 12px;">
                        <div class="body p-4">
                            <h4 class="text-left mb-4" style="font-weight: 800; color: #B6771D;">Closure Details - <span class="badge bg-blue"><?= ucfirst($closure['status'] ?? 'Pending') ?></span></h4>

                            <div class="row">
                                <div class="col-md-6 mb-3"><strong>Document Number:</strong><br><?= htmlspecialchars($closure['document_number']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Barangay:</strong><span style="text-transform: capitalize;"><br><?= htmlspecialchars($closure['barangay_name']) ?></span></div>

                                <div class="col-md-6 mb-3"><strong>Certificate Type:</strong><br><?= htmlspecialchars($closure['certificate_type']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Purpose:</strong><br><?= htmlspecialchars($closure['purpose']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Business Name:</strong><br><?= htmlspecialchars($closure['business_name']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Business Type:</strong><br><?= htmlspecialchars($closure['business_type_name']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Business Address:</strong><br><?= htmlspecialchars($closure['business_address']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Owner Name:</strong><br><?= htmlspecialchars($closure['owner_name']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Purok:</strong><br><?= htmlspecialchars($closure['owner_purok']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Email:</strong><br><?= htmlspecialchars($closure['email']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Contact:</strong><br><?= htmlspecialchars($closure['contact']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Picked Up By:</strong><br><?= htmlspecialchars($closure['picked_up_by']) ?> - <span style="text-transform: capitalize;"><?= htmlspecialchars($closure['relationship']) ?></span></div>

                                <div class="col-md-6 mb-3"><strong>Resident:</strong><br><?= htmlspecialchars($closure['is_resident']) ?></div>

                                <div class="col-md-6 mb-3">
                                    <strong>Total Amount:</strong><br>
                                    <span class="text-success">₱<?= number_format($closure['total_amount'], 2) ?>
                                        <?php if ($closure['status'] === 'Claimed'): ?>
                                            / Paid
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>

                            <hr>
                            <h5 style="color: #B6771D;">Uploaded Documents</h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <span class="label-title">Valid ID:</span><br>
                                    <?php if (!empty($closure['valid_id'])): ?>
                                        <a class="btn btn-sm bg-red" target="_blank" href="../../public/request/valid_id/<?= $closure['valid_id'] ?>">View Valid ID</a>
                                    <?php else: ?>
                                        <span class="text-danger">Not Uploaded</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <span class="label-title">Birth Certificate:</span><br>
                                    <?php if (!empty($closure['birth_certificate'])): ?>
                                        <a class="btn btn-sm bg-red" target="_blank" href="../../public/request/birth_certificate/<?= $closure['birth_certificate'] ?>">View Birth Certificate</a>
                                    <?php else: ?>
                                        <span class="text-danger">Not Uploaded</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <a href="certificate_closure.php" class="btn bg-red">← Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.js"></script>
    <script src="../plugins/jquery-validation/jquery.validate.js"></script>
    <script src="../js/pages/forms/form-validation.js"></script>
    <script src="../plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="../plugins/node-waves/waves.js"></script>
    <script src="../plugins/sweetalert/sweetalert.min.js"></script>
    <script src="../js/admin.js"></script>
</body>

</html>