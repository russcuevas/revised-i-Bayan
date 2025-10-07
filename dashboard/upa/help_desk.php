<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "resident_id_$barangay";



if (!isset($_SESSION[$session_key])) {
    header("Location: ../../login.php");
    exit();
}

$resident_name = $_SESSION["resident_name_$barangay"] ?? 'Resident';
$resident_id = $_SESSION[$session_key];

$stmt = $conn->prepare("SELECT is_approved FROM tbl_residents WHERE id = ?");
$stmt->execute([$resident_id]);
$resident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resident) {
    $_SESSION['error'] = "Resident not found.";
    header("Location: ../../login.php");
    exit();
}

$is_approved = $resident['is_approved'];
$_SESSION["is_approved_$barangay"] = $is_approved;


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
            border: 2px solid #1a49cb;
            border-radius: 10px;
            padding: 50px;
            transition: all 0.3s ease;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .thumbnail:hover {
            background-color: #1a49cb;
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

    <section class="content" style="padding: 20px;">
        <div class="container">
            <div class="card">
                <div class="body">
                    <h4 class="text-center" style="font-weight: 900; color: #1a49cb;">Help Desk</h4>
                    <p class="text-center">
                        The Help Desk is your go-to support center for all concerns and inquiries related to barangay services. Whether you need assistance with document requests, family profiling, live chat communication, or providing feedback, our Help Desk is here to ensure a fast, reliable, and user-friendly experience for every resident.
                    </p>
                    <div class="row text-center" style="margin-top: 30px;">
                        <!-- Family Profiling -->
                        <div class="col-sm-6 col-md-3 col-lg-4" data-toggle="modal" data-target="#familyProfilingModal">
                            <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                <i class="fas fa-users fa-3x mb-3 icon-style"></i>
                                <div class="caption">
                                    <h3>Family Profiling</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Document Request -->
                        <div class="col-sm-6 col-md-3 col-lg-4" data-toggle="modal" data-target="#documentRequestModal">
                            <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                <div class="caption">
                                    <h3>Document Request</h3>
                                </div>
                            </div>
                        </div>

                        <!-- Live Chat -->
                        <div class="col-sm-6 col-md-3 col-lg-4" data-toggle="modal" data-target="#liveChatModal">
                            <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                <i class="fas fa-comment fa-3x mb-3 icon-style"></i>
                                <div class="caption">
                                    <h3>Live Chat</h3>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2">

                        </div>

                        <!-- Feedback -->
                        <div class="col-sm-6 col-md-3 col-lg-4" data-toggle="modal" data-target="#feedbackModal">
                            <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                <i class="fas fa-thumbs-up fa-3x mb-3 icon-style"></i>
                                <div class="caption">
                                    <h3>Feedback</h3>
                                </div>
                            </div>
                        </div>

                        <!-- About Us -->
                        <div class="col-sm-6 col-md-3 col-lg-4" data-toggle="modal" data-target="#aboutUsModal">
                            <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                <i class="fas fa-book-open fa-3x mb-3 icon-style"></i>
                                <div class="caption">
                                    <h3>About Us</h3>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="familyProfilingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Family Profiling</h4>
                    </div>
                    <div class="modal-body">
                        This module allows you to profile and manage family records, including household members and related demographic data.
                    </div>
                    <div class="modal-footer">
                        <button class="btn bg-teal waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Document Request Modal -->
        <div class="modal fade" id="documentRequestModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Document Request</h4>
                    </div>
                    <div class="modal-body">
                        Submit a request for official barangay documents. This may include Barangay Clearance, Certificate of Residency, and more.
                    </div>
                    <div class="modal-footer">
                        <button class="btn bg-teal waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Chat Modal -->
        <div class="modal fade" id="liveChatModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Live Chat</h4>
                    </div>
                    <div class="modal-body">
                        Talk to a barangay officer directly for assistance.
                    </div>
                    <div class="modal-footer">
                        <button class="btn bg-teal waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedback Modal -->
        <div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Feedback</h4>
                    </div>
                    <div class="modal-body">
                        Share your experience or suggestions to help improve our services.
                    </div>
                    <div class="modal-footer">
                        <button class="btn bg-teal waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- About Us Modal -->
        <div class="modal fade" id="aboutUsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">About Us</h4>
                    </div>
                    <div class="modal-body">
                        Learn more about our mission, services, and the team behind iBayan.
                    </div>
                    <div class="modal-footer">
                        <button class="btn bg-teal waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

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
</body>

</html>