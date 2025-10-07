<?php
// session
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

// database connection
include '../database/connection.php';

// to get total count of Barangay
$barangay_query = "SELECT COUNT(*) AS total_barangay FROM tbl_barangay";
$barangay_stmt = $conn->query($barangay_query);
$barangay_data = $barangay_stmt->fetch(PDO::FETCH_ASSOC);
$total_barangay = $barangay_data['total_barangay'];

// to get total count of Admin
$admin_query = "SELECT COUNT(*) AS total_admin FROM tbl_admin";
$admin_stmt = $conn->query($admin_query);
$admin_data = $admin_stmt->fetch(PDO::FETCH_ASSOC);
$total_admin = $admin_data['total_admin'];

// to get total count of Admin
$announcement_query = "SELECT COUNT(*) AS total_announcement FROM tbl_announcement";
$announcement_stmt = $conn->query($announcement_query);
$announcement_data = $announcement_stmt->fetch(PDO::FETCH_ASSOC);
$total_announcement = $announcement_data['total_announcement'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iBayan</title>
    <!-- Favicon-->
    <link rel="icon" href="img/logo.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet"
        type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- Morris Chart Css-->
    <link href="plugins/morrisjs/morris.css" rel="stylesheet" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">

    <link href="css/themes/all-themes.css" rel="stylesheet" />
    <!-- Sweetalert Css -->
    <link href="plugins/sweetalert/sweetalert.css" rel="stylesheet" />
    <!-- Toastr Css -->
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

        /* Toast */
        .toast-success {
            background-color: #ffffff !important;
            color: #1a49cb !important;
            border-left: 5px solid #1a49cb;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }

        .toast-success .toast-message::before {
            content: "\f00c";
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            margin-right: 10px;
            color: #1a49cb;
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
                    <img id="bcas-logo" style="width:45px;display:inline;margin-right:10px;" src="img/logo.png" />
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
                <h3 style="color: #1a49cb;">Dashboard</h3>
            </div>
            <div class="row clearfix">
                <div class="col-sm-6 col-md-3 col-lg-4" onclick="window.location.href = 'barangay_management.php'">
                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                        <h1><?php echo $total_barangay; ?></h1>
                        <div class="caption">
                            <h3>Total Barangay</h3>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-4" onclick="window.location.href = 'admin_management.php'">
                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                        <h1><?php echo $total_admin; ?></h1>
                        <div class="caption">
                            <h3>Total Admin</h3>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3 col-lg-4" onclick="window.location.href = 'emergency_update.php'">
                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                        <h1><?php echo $total_announcement; ?></h1>
                        <div class="caption">
                            <h3>Total Emergency</h3>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Widgets -->

            <div class="block-header text-left">
                <h3 style="color: #1a49cb;">Analytics</h3>
            </div>

            <!-- GRAPHS SHOWING  -->
            <div class="row clearfix">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header" style="background-color: #1a49cb;">
                            <h2 style="color: white !important">TOTAL RESIDENTS (ALL BARANGAY)</h2>
                        </div>
                        <div class="col-md-6">
                            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 15px;">
                                <label class="form-label" style="white-space: nowrap;">SELECT YEAR:</label>
                                <select id="yearSelector" class="form-control select-form" required style="padding: 5px; flex: 1;">
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                </select>

                            </div>
                        </div>
                        <div class="body">
                            <canvas style="border-color:#1a49cb;" id="line_chart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <!-- #END# Line Chart -->
                <!-- Bar Chart -->
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header" style="background-color: #1a49cb;">
                            <h2 style="color: white !important">TOTAL CERTIFICATE FEES (ALL BARANGAY)</h2>
                        </div>
                        <div class="col-md-6">
                            <div style="display: flex; justify-content: flex-end; align-items: center; gap: 10px; padding: 15px;">
                                <label class="form-label" style="white-space: nowrap;">SELECT YEAR:</label>
                                <select id="yearSelectorCertificates" class="form-control select-form" required style="padding: 5px; flex: 1;">
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                    <option value="2027">2027</option>
                                    <option value="2028">2028</option>
                                    <option value="2029">2029</option>
                                    <option value="2030">2030</option>
                                </select>
                            </div>
                        </div>
                        <div class="body">
                            <canvas id="bar_chart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <!-- #END# Bar Chart -->
            </div>
        </div>
    </section>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Jquery Validation Plugin Css -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>
    <script src="js/pages/forms/form-validation.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Jquery CountTo Plugin Js -->
    <script src="plugins/jquery-countto/jquery.countTo.js"></script>

    <!-- Morris Plugin Js -->
    <script src="plugins/raphael/raphael.min.js"></script>
    <script src="plugins/morrisjs/morris.js"></script>

    <!-- ChartJs -->
    <script src="plugins/chartjs/Chart.bundle.js"></script>

    <!-- Flot Charts Plugin Js -->
    <script src="plugins/flot-charts/jquery.flot.js"></script>
    <script src="plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="plugins/flot-charts/jquery.flot.time.js"></script>

    <!-- Sparkline Chart Plugin Js -->
    <script src="plugins/jquery-sparkline/jquery.sparkline.js"></script>

    <!-- Custom Js -->
    <script src="plugins/chartjs/Chart.bundle.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>

    <script>
        async function fetchTotalCertificateFees(year) {
            try {
                const response = await fetch(`total_fees.php?year=${year}`);
                const result = await response.json();

                if (!result.success) {
                    console.error(result.message);
                    return;
                }

                const labels = result.data.map(item =>
                    item.barangay_name
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ')
                );

                const data = result.data.map(item => Number(item.total_fees));

                const ctx = document.getElementById('bar_chart').getContext('2d');

                if (window.certificateFeesChart) {
                    window.certificateFeesChart.destroy();
                }

                window.certificateFeesChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `Total Certificate Fees (${year})`,
                            data: data,
                            backgroundColor: '#1a49cb'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: value => `₱${value.toLocaleString()}`
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });

            } catch (error) {
                console.error('Fetch error:', error);
            }
        }

        $(function() {
            const defaultYear = $('#yearSelectorCertificates').val();
            fetchTotalCertificateFees(defaultYear);

            $('#yearSelectorCertificates').on('change', function() {
                const selectedYear = $(this).val();
                fetchTotalCertificateFees(selectedYear);
            });
        });
    </script>


    <script>
        async function fetchTotalResidents(year) {
            try {
                const response = await fetch(`total_residents.php?year=${year}`);
                const result = await response.json();

                if (!result.success) {
                    console.error(result.message);
                    return;
                }

                const labels = result.data.map(item =>
                    item.barangay_name
                    .toLowerCase()
                    .split(' ')
                    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                    .join(' ')
                );
                const data = result.data.map(item => Number(item.total_residents));

                const ctx = document.getElementById('line_chart').getContext('2d');

                if (window.residentChart) {
                    window.residentChart.destroy();
                }

                window.residentChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: `Total Residents (${year})`,
                            data: data,
                            borderColor: '#1a49cb',
                            backgroundColor: 'rgba(0, 61, 245, 0.71)',
                            pointBorderColor: 'rgba(0, 188, 212, 0)',
                            pointBackgroundColor: 'black',
                            pointBorderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        legend: false
                    }
                });

            } catch (error) {
                console.error('Fetch error:', error);
            }
        }

        // On page load
        $(function() {
            const currentYear = $('#yearSelector').val();
            fetchTotalResidents(currentYear);

            $('#yearSelector').on('change', function() {
                const selectedYear = $(this).val();
                fetchTotalResidents(selectedYear);
            });

            new Chart(document.getElementById("bar_chart").getContext("2d"), getChartJs('bar'));
            new Chart(document.getElementById("radar_chart").getContext("2d"), getChartJs('radar'));
            new Chart(document.getElementById("pie_chart").getContext("2d"), getChartJs('pie'));
        });
    </script>

    <script src="js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="js/demo.js"></script>
    <script src="plugins/sweetalert/sweetalert.min.js"></script>
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
            toastr.success("Welcome superadmin!");
        </script>
    <?php endif; ?>



</body>

</html>