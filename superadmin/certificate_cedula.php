<?php
// session
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

// database connection
include '../database/connection.php';

$get_certificates_query = "
    SELECT c.*, b.barangay_name
    FROM tbl_cedula c
    LEFT JOIN tbl_barangay b ON c.for_barangay = b.id
    ORDER BY c.id DESC
";

$stmt = $conn->prepare($get_certificates_query);
$stmt->execute();
$certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iBayan</title>
    <!-- Favicon-->
    <link rel="icon" href="img/logo.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- JQuery DataTable Css -->
    <link href="plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom Css -->
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <!-- Sweetalert Css -->
    <link href="plugins/sweetalert/sweetalert.css" rel="stylesheet" />

    <link href="css/themes/all-themes.css" rel="stylesheet" />
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
                    <li class="active"><i style="font-size: 20px;" class="material-icons">description</i> Certificate Issuance
                    </li>
                </ol>
            </div>
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>CERTIFICATE CEDULA STATUS</h2>
                        </div>
                        <div class="body">
                            <div class="modal fade" id="requestCertificateModal" tabindex="-1" role="dialog" style="display: none;">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="defaultModalLabel">Request</h4>
                                        </div>
                                        <div class="modal-body" style="max-height: 100vh; overflow-y: auto;">
                                            <div class="row">
                                                <div class="col-sm-6 col-md-3 col-lg-4" onclick="window.location.href = 'request_certificate.php'">
                                                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                                        <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                                        <div class="caption">
                                                            <h3>Certificates</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 col-md-3 col-lg-4" onclick="window.location.href = 'request_operate.php'">
                                                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                                        <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                                        <div class="caption">
                                                            <h3>Operate</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 col-md-3 col-lg-4" onclick="window.location.href = 'request_closure.php'">
                                                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                                        <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                                        <div class="caption">
                                                            <h3>Closure</h3>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-sm-6 col-md-3 col-lg-4" onclick="window.location.href = 'request_cedula.php'">
                                                    <div class="thumbnail text-center d-flex flex-column align-items-center justify-content-center" style="padding: 50px;">
                                                        <i class="fas fa-file fa-3x mb-3 icon-style"></i>
                                                        <div class="caption">
                                                            <h3>Cedula</h3>
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

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Barangay</th>
                                            <th>Fullname</th>
                                            <th>Certificate</th>
                                            <th>Price</th>
                                            <th>Purpose</th>
                                            <th>Email</th>
                                            <th>Mobile</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($certificates as $certificate): ?>
                                            <tr>
                                                <td><span style="text-transform: capitalize;"><?php echo htmlspecialchars($certificate['barangay_name']); ?></span></td>
                                                <td><?php echo $certificate['fullname'] ?></td>
                                                <td><?php echo $certificate['certificate_type'] ?></td>
                                                <td><?php echo $certificate['total_amount'] ?></td>
                                                <td><?php echo $certificate['purpose'] ?></td>
                                                <td><?php echo $certificate['email'] ?></td>
                                                <td><?php echo $certificate['contact'] ?></td>
                                                <td><?= date('F d, Y', strtotime($certificate['created_at'] ?? 'now')) ?></td>
                                                <td>
                                                    <?php
                                                    $status = ucfirst($certificate['status'] ?? 'Pending');
                                                    $badgeClass = 'btn-info'; // default for Pending

                                                    if ($status === 'To Pick Up') {
                                                        $badgeClass = 'btn-warning text-dark';
                                                    } elseif ($status === 'Claimed') {
                                                        $badgeClass = 'btn-success';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                                </td>
                                                <td>
                                                    <a href="cedula_view_information.php?id=<?= $certificate['id'] ?>" class="btn bg-teal waves-effect" style="margin-bottom: 5px;">
                                                        <i class="fa-solid fa-eye"></i> VIEW INFORMATION
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- #END# Basic Validation -->
        </div>
    </section>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Jquery Validation Plugin Css -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>
    <script src="js/pages/forms/form-validation.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->

    <!-- Slimscroll Plugin Js -->
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>

    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Jquery DataTable Plugin Js -->
    <script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/tables/jquery-datatable.js"></script>
    <!-- SweetAlert Plugin Js -->
    <script src="plugins/sweetalert/sweetalert.min.js"></script>
    <!-- Demo Js -->
    <script src="js/demo.js"></script>

    <script>
        $('#request_certifacte_validation').validate({
            rules: {},
            highlight: function(input) {
                $(input).parents('.form-line').addClass('error');
            },
            unhighlight: function(input) {
                $(input).parents('.form-line').removeClass('error');
            },
            errorPlacement: function(error, element) {
                $(element).parents('.form-group').append(error);
            },
        });
    </script>
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

</body>

</html>