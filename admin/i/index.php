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

// fetch the barangay of the admin
$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

// fetch total number of residents with the particular barangay
$resident_count_stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM tbl_residents_family_members 
    WHERE barangay_address = ? AND is_approved = 1
");
$resident_count_stmt->execute([$admin_barangay_id]);
$total_residents = $resident_count_stmt->fetchColumn();


// fetch pending approvals number of residents to approved
$pending_resident_count_stmt = $conn->prepare("
    SELECT COUNT(*) 
    FROM tbl_residents_family_members 
    WHERE barangay_address = ? 
      AND is_approved = 0
      AND resident_id NOT IN (
          SELECT resident_id
          FROM tbl_residents_family_members
          GROUP BY resident_id
          HAVING COUNT(*) = 1 AND MAX(relationship) = 'Account Owner'
      )
");
$pending_resident_count_stmt->execute([$admin_barangay_id]);
$pending_residents = $pending_resident_count_stmt->fetchColumn();


// 
$tables = ['tbl_certificates', 'tbl_cedula', 'tbl_closure', 'tbl_operate'];
$total_pending = 0;

foreach ($tables as $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE status = 'Pending' AND for_barangay = ?");
    $stmt->execute([$admin_barangay_id]);
    $count = $stmt->fetchColumn();
    $total_pending += (int)$count;
}

$tables = [
    'tbl_certificates' => 'Certificates',
    'tbl_cedula' => 'Cedula',
    'tbl_closure' => 'Closure',
    'tbl_operate' => 'Operate'
];

$pending_counts = [];

foreach ($tables as $table => $label) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE status = 'Pending' AND for_barangay = ?");
    $stmt->execute([$admin_barangay_id]);
    $pending_counts[$label] = (int)$stmt->fetchColumn();
}

//
$claimed_tables = [
    'tbl_certificates_claimed',
    'tbl_cedula_claimed',
    'tbl_closure_claimed',
    'tbl_operate_claimed'
];

$total_claimed = 0;

foreach ($claimed_tables as $table) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE for_barangay = ?");
    $stmt->execute([$admin_barangay_id]);
    $count = $stmt->fetchColumn();
    $total_claimed += (int)$count;
}

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iBayan</title>
    <!-- Favicon-->
    <link rel="icon" href="../img/logo.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap Core Css -->
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="../plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="../plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="../plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">

    <link href="../css/themes/all-themes.css" rel="stylesheet" />
    <!-- Sweetalert Css -->
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />
    <!-- Toast -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

        body {
            font-family: 'Poppins', sans-serif !important;
        }

        .select-form {
            display: block !important;
            width: 100% !important;
            height: 34px !important;
            padding: 6px 12px !important;
            font-size: 14px !important;
            line-height: 1.42857143 !important;
            color: #555 !important;
            background-color: #fff !important;
            background-image: none !important;
            border: 1px solid #ccc !important;
            border-radius: 4px !important;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075) !important;
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075) !important;
            -webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s !important;
            -o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s !important;
            transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s !important;
        }

        /* HOMEPAGE */
        .thumbnail {
            background-color: #ffffff;
            border: 2px solid #B6771D;
            border-radius: 10px;
            padding: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .thumbnail:hover {
            background-color: #B6771D;
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .thumbnail:hover .icon-style,
        .thumbnail:hover h3 {
            color: #ffffff;
            transition: color 0.3s ease;
        }

        .icon-style {
            transition: color 0.3s ease;
        }

        /* Toast */
        .toast-success {
            background-color: #ffffff !important;
            color: #B6771D !important;
            border-left: 5px solid #B6771D;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .toast-success .toast-message::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 10px;
            color: #B6771D;
        }


        .toast {
            border-radius: 6px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .toast-message {
            font-size: 14px;
        }
    </style>
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
    <!-- #Top Bar -->
    <section>
        <?php include('left_sidebar.php') ?>
        <?php include('right_sidebar.php') ?>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="block-header text-left">
                <h3 style="color: #B6771D;">Dashboard</h3>
            </div>
            <div class="row clearfix">
                <div class="col-sm-6 col-md-3 col-lg-6" onclick="window.location.href = 'manage_residents.php'">
                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                        <h1><?= $total_residents ?></h1>
                        <div class="caption">
                            <h3>Total Residents</h3>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-6" onclick="window.location.href = 'resident_verifications.php'">
                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                        <h1><?= $pending_residents ?></h1>
                        <div class="caption">
                            <h3>Pending Approvals</h3>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-6" data-toggle="modal" data-target="#requestCertificateModal">
                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                        <h1><?= $total_pending ?></h1>
                        <div class="caption">
                            <h3>Certificate Requests</h3>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="requestCertificateModal" tabindex="-1" role="dialog" style="display: none;">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="defaultModalLabel">View Requests</h4>
                            </div>
                            <div class="modal-body" style="max-height: 100vh; overflow-y: auto;">
                                <div class="row">
                                    <div class="col-sm-6 col-md-3 col-lg-6" onclick="window.location.href = 'certificate_issuance.php'">
                                        <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                            <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                            <div class="caption">
                                                <h3>Certificates</h3>
                                                <p>Pending requests: <strong><?= $pending_counts['Certificates'] ?? 0 ?></strong></p>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-3 col-lg-6" onclick="window.location.href = 'certificate_operate.php'">
                                        <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                            <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                            <div class="caption">
                                                <h3>Operate</h3>
                                                <p>Pending requests: <strong><?= $pending_counts['Operate'] ?? 0 ?></strong></p>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-3 col-lg-6" onclick="window.location.href = 'certificate_closure.php'">
                                        <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                            <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                            <div class="caption">
                                                <h3>Closure</h3>
                                                <p>Pending requests: <strong><?= $pending_counts['Closure'] ?? 0 ?></strong></p>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-3 col-lg-6" onclick="window.location.href = 'certificate_cedula.php'">
                                        <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                            <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                            <div class="caption">
                                                <h3>Cedula</h3>
                                                <p>Pending requests: <strong><?= $pending_counts['Cedula'] ?? 0 ?></strong></p>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Footer Buttons aligned to bottom right -->
                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- END ADD MODAL -->

                <div class="col-sm-6 col-md-3 col-lg-6" onclick="window.location.href = 'email_sent.php'">
                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                        <h1><?= $total_claimed ?></h1>
                        <div class="caption">
                            <h3>Certificate Completed</h3>
                        </div>
                    </div>
                </div>

            </div>
            <!-- #END# Widgets -->
        </div>
    
    <?php include('footer.php')?>    
    </section>

    
    <!-- Jquery Core Js -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <!-- Jquery Validation Plugin Css -->
    <script src="../plugins/jquery-validation/jquery.validate.js"></script>
    <script src="../js/pages/forms/form-validation.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="../plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="../plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="../plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="../plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="../plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="../plugins/raphael/raphael.min.js"></script>
    <script src="../plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="../plugins/chartjs/Chart.bundle.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="../plugins/flot-charts/jquery.flot.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="../plugins/flot-charts/jquery.flot.time.js"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="../plugins/jquery-sparkline/jquery.sparkline.js"></script>

    <!-- Custom Js -->
    <script src="../js/admin.js"></script>
    <script src="../js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="../js/demo.js"></script>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <?php if (isset($_GET['success'])): ?>
        <script>
            toastr.options = {
                "closeButton": false,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "onclick": null,
                "showDuration": "300",
                "hideDuration": "1000",
                "timeOut": "3000",
                "extendedTimeOut": "1000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
            toastr.success("Welcome administrator!");
        </script>
    <?php endif; ?>

    <script>
    let chatLoaded = false;

    $('#openChatBtn').on('click', function() {
    $('#chatPopup').modal('show');

    if (!chatLoaded) {
        $('#chatContent').html(`
        <iframe src="live_chat.php" 
                style="width:100%; height:100%; border:none;"></iframe>
        `);
        chatLoaded = true;
    }
    });
    </script>


</body>

</html>