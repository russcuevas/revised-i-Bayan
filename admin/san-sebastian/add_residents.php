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

$stmt = $conn->prepare("SELECT id, first_name, last_name FROM tbl_residents WHERE barangay_address = ?");
$stmt->execute([$admin_barangay_id]);
$residents = $stmt->fetchAll(PDO::FETCH_ASSOC);


$barangay_stmt = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
$barangay_stmt->execute([$admin_barangay_id]);
$admin_barangay_name = $barangay_stmt->fetchColumn();


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

    <!-- JQuery DataTable Css -->
    <link href="../plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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

        .select2-container--default .select2-selection--single {
            height: 33px !important;
            padding: 5px 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 24px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
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
            <div class="block-header">
                <ol style="font-size: 15px;" class="breadcrumb breadcrumb-col-red">
                    <li><a href="index.php"><i style="font-size: 20px;" class="material-icons">home</i>
                            Dashboard</a></li>
                    <li class="active"><i style="font-size: 20px;" class="material-icons">description</i> Manage Residents
                    </li>
                </ol>
            </div>
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>ADD FAMILY MEMBER</h2>
                        </div>
                        <div class="body">
                            <form id="add_family_profiling" action="save_family_member.php" method="POST" style="margin-top: 20px;">
                                <input type="hidden" name="barangay_address" value="<?= htmlspecialchars($admin_barangay_id ?? '') ?>">

                                <h4 class="bold span-or mb-4" style="font-weight: 900; color: #B6771D;">
                                    Relationship to Family Member
                                </h4> <br>

                                <label>Choose Resident</label>
                                <select id="resident-select" name="resident_id" class="form-control select-form" required>
                                    <option value="" disabled selected>Select Resident</option>
                                    <?php foreach ($residents as $resident): ?>
                                        <option value="<?= $resident['id'] ?>">
                                            <?= htmlspecialchars($resident['first_name'] . ' ' . $resident['last_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <label class="form-label">Relationship <span style="color: red;">*</span></label>
                                            <select class="form-control select-form" name="relationship" required>
                                                <option value="" disabled selected>CHOOSE RELATIONSHIP</option>
                                                <option value="grandfather">Grandfather</option>
                                                <option value="grandmother">Grandmother</option>
                                                <option value="father">Father</option>
                                                <option value="mother">Mother</option>
                                                <option value="sibling">Sibling</option>
                                                <option value="son">Son</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="bold span-or mb-4" style="font-weight: 900; color: #B6771D;">
                                    Personal Information
                                </h4> <br>

                                <div class="row">
                                    <?php
                                    $fields = [
                                        ['first_name', 'First name'],
                                        ['middle_name', 'Middle name'],
                                        ['last_name', 'Last name'],
                                        ['suffix', 'Suffix'],
                                    ];
                                    foreach ($fields as $field) {
                                        [$name, $label] = $field;
                                        echo '
                                        <div class="col-md-6" style="margin-top: 10px;">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="' . $name . '"' . ($name !== 'suffix' ? ' required' : '') . '>
                                                    <label class="form-label">' . $label . ($name !== 'suffix' ? ' <span style="color: red;">*</span>' : '') . '</label>
                                                </div>
                                            </div>
                                        </div>';
                                    }
                                    ?>

                                    <div class="col-md-6" style="margin-top: 10px; margin-bottom: 25px">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="purok">
                                                <label class="form-label">Purok <span style="color: red;">*</span></label>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-md-6" style="margin-top: 10px; margin-bottom: 25px">
                                        <div class="form-group">
                                            <label for="gender">Gender <span style="color: red;">*</span></label><br>
                                            <input type="radio" name="gender" id="male" value="Male" checked>
                                            <label for="male">Male</label>
                                            <input type="radio" name="gender" id="female" value="Female" class="m-l-20">
                                            <label for="female">Female</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <?php
                                    $infos = [
                                        ['birthday', 'Date of birth', 'date'],
                                        ['birthplace', 'Birthplace', 'text'],
                                        ['age', 'Age', 'number'],
                                    ];
                                    foreach ($infos as $info) {
                                        [$name, $label, $type] = $info;
                                        echo '
                                    <div class="col-md-4">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="' . $type . '" class="form-control" name="' . $name . '" required>
                                                <label class="form-label">' . $label . ' <span style="color: red;">*</span></label>
                                            </div>
                                        </div>
                                    </div>';
                                    }
                                    ?>
                                    <div class="col-md-4">
                                        <div class="form-group form-float">
                                            <label class="form-label">Civil status <span style="color: red;">*</span></label>
                                            <select class="form-control select-form" name="civil_status" required>
                                                <option value="" disabled selected>CHOOSE CIVIL STATUS</option>
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                                <option value="widowed">Widowed</option>
                                                <option value="separated">Separated</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- CURRENT STATUS -->
                                <div class="row" style="margin-bottom: 20px;">
                                    <div class="col-md-12">
                                        <label><strong>Current Status</strong></label><br>
                                        <input type="radio" name="is_working" value="1" id="working">
                                        <label for="working">Working</label>
                                        <input type="radio" name="is_working" value="2" id="student" style="margin-left: 15px;">
                                        <label for="student">Student</label>
                                        <input type="radio" name="is_working" value="4" id="senior_citizen" style="margin-left: 15px;">
                                        <label for="senior_citizen">Senior Citizen</label>
                                        <input type="radio" name="is_working" value="3" id="none" style="margin-left: 15px;" checked>
                                        <label for="none">None</label>
                                    </div>
                                </div>

                                <div class="row" id="occupationDiv" style="display: none; margin-top: 10px;">
                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="occupation">
                                                <label class="form-label">Occupation <span style="color: red;">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" id="schoolDiv" style="display: none; margin-top: 10px;">
                                    <div class="col-md-12">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="school">
                                                <label class="form-label">School <span style="color: red;">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- VOTER AND YEARS -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Is a voter here? <span style="color: red;">*</span></label><br>
                                        <input type="radio" name="is_barangay_voted" value="1" id="yes" checked>
                                        <label for="yes">Yes</label>
                                        <input type="radio" name="is_barangay_voted" value="0" id="no" class="m-l-20">
                                        <label for="no">No</label>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px !important;">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="years_in_barangay" required>
                                                <label class="form-label">Years in Barangay <span style="color: red;">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="number" class="form-control" name="phone_number">
                                                <label class="form-label">Mobile #</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="philhealth_number">
                                                <label class="form-label">PhilHealth #</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- HIDDEN FIELDS -->
                                <input type="hidden" name="is_approved" value="1">

                                <!-- Submit -->
                                <div class="text-right">
                                    <button class="btn bg-teal waves-effect" type="submit">SAVE</button>
                                    <button type="button" class="btn btn-link waves-effect" onclick="window.location.href = 'manage_residents.php'">BACK</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

            <!-- #END# Basic Validation -->
        </div>
        </div>
        <?php include('footer.php')?>    

    </section>

    <!-- Jquery Core Js -->
    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#resident-select').select2({
                placeholder: "Select Resident",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
    <!-- Jquery Validation Plugin Css -->
    <script src="../plugins/jquery-validation/jquery.validate.js"></script>
    <script src="../js/pages/forms/form-validation.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="../plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="../plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="../plugins/node-waves/waves.js"></script>

    <!-- Jquery DataTable Plugin Js -->
    <script src="../plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="../plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="../plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

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
    <script src="../plugins/chartjs/Chart.bundle.js"></script>

    <!-- Custom Js -->
    <script src="../js/admin.js"></script>
    <script src="../js/pages/tables/jquery-datatable.js"></script>
    <script src="../js/pages/charts/chartjs.js"></script>
    <script src="../js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="../js/demo.js"></script>
    <script src="../plugins/sweetalert/sweetalert.min.js"></script>
    <script>
        document.querySelectorAll('input[name="is_working"]').forEach((elem) => {
            elem.addEventListener('change', function() {
                const occupationDiv = document.getElementById('occupationDiv');
                const schoolDiv = document.getElementById('schoolDiv');

                if (this.value === "1") {
                    occupationDiv.style.display = 'block';
                    schoolDiv.style.display = 'none';
                } else if (this.value === "2") {
                    occupationDiv.style.display = 'none';
                    schoolDiv.style.display = 'block';
                } else {
                    occupationDiv.style.display = 'none';
                    schoolDiv.style.display = 'none';
                }
            });
        });
    </script>

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