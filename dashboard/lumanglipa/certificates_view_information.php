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

$certificate_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM tbl_certificates WHERE id = ? AND resident_id = ?");
$stmt->execute([$certificate_id, $resident_id]);
$certificate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$certificate) {
    echo "Certificate not found or access denied.";
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
                            <h4 class="text-left mb-4" style="font-weight: 800; color: #B6771D;">Certificate Details - <span class="badge bg-blue"><?= ucfirst($certificate['status'] ?? 'Pending') ?></span></h4>

                            <div class="row">
                                <div class="col-md-6 mb-3"><strong>Full Name:</strong><br><?= htmlspecialchars($certificate['fullname']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Purok:</strong><br><?= htmlspecialchars($certificate['purok']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Gender:</strong><br><?= htmlspecialchars($certificate['gender']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Email:</strong><br><?= htmlspecialchars($certificate['email']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Contact:</strong><br><?= htmlspecialchars($certificate['contact']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Certificate Type:</strong><br><?= htmlspecialchars($certificate['certificate_type']) ?></div>
                                <div class="col-md-6 mb-3"><strong>Purpose:</strong><br><?= htmlspecialchars($certificate['purpose']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Resident:</strong><br><?= htmlspecialchars($certificate['is_resident']) ?></div>

                                <div class="col-md-6 mb-3"><strong>Picked Up By:</strong><br><?= htmlspecialchars($certificate['picked_up_by']) ?> - <span style="text-transform: capitalize;"><?= htmlspecialchars($certificate['relationship']) ?></span></div>

                                <div class="col-md-6 mb-3"><strong>Total Amount:</strong><br> <span class="text-success">
                                        ₱<?= number_format($certificate['total_amount'], 2) ?>
                                        <?php if ($certificate['status'] === 'Claimed'): ?>
                                            / Paid
                                        <?php endif; ?>
                                    </span></div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3 text-primary"><strong>Uploaded Documents</strong></h5>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Valid ID:</strong><br>
                                    <?php if (!empty($certificate['valid_id'])): ?>
                                        <a class="btn btn-sm bg-red" href="../../public/request/valid_id/<?= htmlspecialchars($certificate['valid_id']) ?>" target="_blank">
                                            View Valid ID
                                        </a>
                                    <?php else: ?>
                                        <span class="text-danger">Not Uploaded</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Birth Certificate:</strong><br>
                                    <?php if (!empty($certificate['birth_certificate'])): ?>
                                        <a class="btn btn-sm bg-red" href="../../public/request/birth_certificate/<?= htmlspecialchars($certificate['birth_certificate']) ?>" target="_blank">
                                            View Birth Certificate
                                        </a>
                                    <?php else: ?>
                                        <span class="text-danger">Not Uploaded</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="text-right mt-4">
                                <a href="certificate_issuance.php" class="btn bg-red"><i class="fa fa-arrow-left"></i> Back</a>
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