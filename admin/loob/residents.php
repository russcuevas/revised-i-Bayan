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
include '../../database/connection.php';
// details welcome
$barangay_name_key = "barangay_name_$barangay";
$admin_name_key = "admin_name_$barangay";
$admin_position_key = "admin_position_$barangay";

// fetching residents where in same of the barangay of the admin
$admin_id = $_SESSION[$session_key];
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();

// 🔹 Get current filter (default: all)
$filter = isset($_GET['filter']) ? strtolower(trim($_GET['filter'])) : 'all';
$purok = trim($_GET['purok'] ?? '');
$gender = trim($_GET['gender'] ?? '');
$age = trim($_GET['age'] ?? '');

// 🔹 Base query
$query = "SELECT * FROM tbl_residents_family_members WHERE barangay_address = ? AND is_approved = 1";
$params = [$admin_barangay_id];

// 🔹 Main category filters
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

// 🔹 Additional filters
if (!empty($purok)) {
    $query .= " AND purok LIKE ?";
    $params[] = "%$purok%";
}

if (!empty($gender)) {
    $query .= " AND gender = ?";
    $params[] = $gender;
}

if (!empty($age)) {
    $query .= " AND age = ?";
    $params[] = $age;
}

// 🔹 Execute query
$family_stmt = $conn->prepare($query);
$family_stmt->execute($params);
$family_members = $family_stmt->fetchAll(PDO::FETCH_ASSOC);
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

        /* Tag List Styling */
        .report-tags {
            list-style-type: none;
            padding-left: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .report-tags li a {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background-color: #ffffff;
            border: 2px solid #B6771D;
            color: #B6771D;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            border-radius: 30px;
            transition: all 0.3s ease;
        }

        .report-tags li a:hover {
            background-color: #B6771D;
            color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .report-tags li a.active {
            background-color: #B6771D;
            color: #ffffff;
            border-color: #B6771D;
            box-shadow: 0 4px 12px rgba(26, 73, 203, 0.3);
            transform: translateY(-1px);
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

        <!-- 🔹 Breadcrumb -->
        <div class="block-header">
            <ol class="breadcrumb breadcrumb-col-red" style="font-size: 15px;">
                <li>
                    <a href="index.php"><i class="material-icons" style="font-size: 20px;">home</i> Dashboard</a>
                </li>
                <li class="active">
                    <i class="material-icons" style="font-size: 20px;">description</i> Reports
                </li>
            </ol>
        </div>

        <div class="block-header text-left">
            <h3 style="color: #B6771D;">Generate Reports</h3>
        </div>

        <div class="row clearfix">

            <!-- 🔹 LEFT PANEL -->
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>CATEGORIES</h2>
                    </div>
                    <div class="body">
                        <ul id="tagList" class="report-tags">
                            <li><a href="residents.php" class="active"><i class="fa-solid fa-users"></i> Residents</a></li>
                            <li><a href="email_sent.php"><i class="fa-solid fa-envelope"></i> Email Sent</a></li>
                            <li><a href="announcement_list.php"><i class="fa-solid fa-bullhorn"></i> Announcement</a></li>
                            <li><a href="logs.php"><i class="fa-solid fa-list-check"></i> Activity Logs</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 🔹 RIGHT PANEL -->
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>RESIDENTS LIST</h2>
                    </div>

                    <!-- 🔹 Filter Row -->
                    <div class="body" style="padding-top: 10px;">
                        

                        <!-- 🔹 Print Button (aligned right) -->
                        <div class="text-right" style="margin-bottom: 15px;">
                            <a href="print_residents.php?filter=<?= urlencode($filter) ?>&purok=<?= urlencode($_GET['purok'] ?? '') ?>&gender=<?= urlencode($_GET['gender'] ?? '') ?>&age=<?= urlencode($_GET['age'] ?? '') ?>" 
                               target="_blank" class="btn" style="background-color: #337ab7; color: white;">
                                <i class="fa fa-print"></i> Print
                            </a>
                        </div>
<form id="filterForm" method="GET" class="row g-2 align-items-center" style="margin-bottom: 15px;">
                            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                                <select name="filter" class="form-control show-tick">
                                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
                                    <option value="working" <?= $filter === 'working' ? 'selected' : '' ?>>Working</option>
                                    <option value="ofw" <?= $filter === 'ofw' ? 'selected' : '' ?>>OFW</option>
                                    <option value="student" <?= $filter === 'student' ? 'selected' : '' ?>>Student</option>
                                    <option value="none" <?= $filter === 'none' ? 'selected' : '' ?>>None</option>
                                    <option value="senior" <?= $filter === 'senior' ? 'selected' : '' ?>>Senior Citizen</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <input type="text" name="purok" value="<?= htmlspecialchars($_GET['purok'] ?? '') ?>" class="form-control" placeholder="Filter by Purok">
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <select name="gender" class="form-control show-tick">
                                    <option value="">All Genders</option>
                                    <option value="Male" <?= (($_GET['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= (($_GET['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                                </select>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <input type="number" name="age" value="<?= htmlspecialchars($_GET['age'] ?? '') ?>" class="form-control" placeholder="Age">
                            </div>

                            <div class="col-lg-3 col-md-12 col-sm-12 col-xs-12 d-flex justify-content-start" style="gap: 5px;">
                                <button type="submit" class="btn" style="background-color: #B6771D; color: white;">
                                    <i class="fa fa-filter"></i> Apply
                                </button>
                                <a href="residents.php" class="btn btn-default">
                                    <i class="fa fa-times"></i> Reset
                                </a>
                            </div>
                        </form>
                        <!-- 🔹 Residents Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
                                        <th>Fullname</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Purok</th>
                                        <th>Mobile</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($family_members) > 0): ?>
                                        <?php foreach ($family_members as $member): ?>
                                            <?php
                                            $full_name = htmlspecialchars(
                                                $member['first_name'] . ' ' .
                                                ($member['middle_name'] ? $member['middle_name'][0] . '. ' : '') .
                                                $member['last_name'] .
                                                ($member['suffix'] ? ', ' . $member['suffix'] : '')
                                            );

                                            $status_map = [
                                                1 => 'Working',
                                                2 => 'Student',
                                                3 => 'None',
                                                4 => 'Senior Citizen'
                                            ];
                                            $status = $status_map[$member['is_working']] ?? 'Unknown';
                                            if ($member['is_working'] == 1 && strtolower($member['occupation']) === 'ofw') {
                                                $status = 'OFW';
                                            }
                                            ?>
                                            <tr>
                                                <td><?= $full_name ?></td>
                                                <td><?= htmlspecialchars($member['gender']) ?></td>
                                                <td><?= htmlspecialchars($member['age']) ?></td>
                                                <td><?= htmlspecialchars($member['purok']) ?></td>
                                                <td><?= htmlspecialchars($member['phone_number']) ?></td>
                                                <td><?= htmlspecialchars($status) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-danger">No residents found for this filter.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
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