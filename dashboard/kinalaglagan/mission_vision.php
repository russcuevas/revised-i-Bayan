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

// Fetch resident info, including barangay ID
$stmt = $conn->prepare("SELECT is_approved, barangay_address FROM tbl_residents WHERE id = ?");
$stmt->execute([$resident_id]);
$resident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resident) {
    $_SESSION['error'] = "Resident not found.";
    header("Location: ../../login.php");
    exit();
}

$is_approved = $resident['is_approved'];
$_SESSION["is_approved_$barangay"] = $is_approved;

$barangay_id = $resident['barangay_address'];

// Fetch mission and vision from tbl_barangay
$stmt = $conn->prepare("SELECT mission, vision FROM tbl_barangay WHERE id = ?");
$stmt->execute([$barangay_id]);
$barangay_info = $stmt->fetch(PDO::FETCH_ASSOC);

$mission = $barangay_info['mission'] ?? 'Mission not available.';
$vision = $barangay_info['vision'] ?? 'Vision not available.';

// Fetch barangay officials
$stmt = $conn->prepare("SELECT position, fullname, profile_picture FROM tbl_barangay_officials WHERE barangay = ?");
$stmt->execute([$barangay_id]);
$officials = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            border: 2px solid #B6771D;
            border-radius: 10px;
            padding: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="text-center" style="font-weight: 900; color: #B6771D;">Mission</h4>
                            <p style="text-align: center; font-size: 15px;">
                                <?php echo htmlspecialchars($mission); ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <h4 class="text-center" style="font-weight: 900; color: #B6771D;">Vision</h4>
                            <p style="text-align: center; font-size: 15px;">
                                <?php echo htmlspecialchars($vision); ?>
                            </p>
                        </div>

                    </div>

                    <h4 class="text-center" style="font-weight: 900; color: #B6771D;">Barangay Officials Year (<?= date('Y') ?>)</h4>
                    <div class="row text-center" style="margin-top: 30px;">
                        <?php if ($officials): ?>
                            <?php foreach ($officials as $official): ?>
                                <div class="col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 30px;">
                                    <img src="<?= !empty($official['profile_picture']) ? '../../public/barangay_officials/' . htmlspecialchars($official['profile_picture']) : 'https://pluspng.com/img-png/user-png-icon-big-image-png-2240.png' ?>"
                                        class="img-responsive center-block"
                                        style="width:120px;height:120px;border-radius:50%;object-fit:cover;">
                                    <p style="margin-top: 10px; font-weight: bold;"><?= htmlspecialchars($official['position']) ?></p>
                                    <p><?= htmlspecialchars($official['fullname']) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center">No barangay officials data found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <footer style="background-color: #f5f5f5; padding: 30px 0; margin-top: 50px; border-top: 2px solid #B6771D;">
                <div class="container text-center">
                    <h1>System Developer</h1> <br>
                    <div class="row">
                        <!-- Zyrell Hidalgo -->
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <img src="https://pluspng.com/img-png/user-png-icon-big-image-png-2240.png" alt="Zyrell Hidalgo" class="img-responsive center-block" style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                            <h5 style="margin-top: 10px; font-weight: bold;">Zyrell Hidalgo</h5>
                        </div>

                        <!-- Shaine Inciong -->
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <img src="https://pluspng.com/img-png/user-png-icon-big-image-png-2240.png" alt="Shaine Inciong" class="img-responsive center-block" style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                            <h5 style="margin-top: 10px; font-weight: bold;">Shaine Inciong</h5>
                        </div>

                        <!-- Christine Manalo -->
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <img src="https://pluspng.com/img-png/user-png-icon-big-image-png-2240.png" alt="Christine Manalo" class="img-responsive center-block" style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                            <h5 style="margin-top: 10px; font-weight: bold;">Christine Manalo</h5>
                        </div>
                    </div>
                </div>
            </footer>


            <div class="text-right" style="margin-top: 30px;">
                <a href="about_us.php" class="btn bg-teal waves-effect">Back</a>
            </div>
        </div>
        </div>
        </div>
        <?php include ('footer.php') ?>
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