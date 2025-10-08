<?php
// session
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

// database connection
include '../database/connection.php';

// add admin function
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $barangay_id = $_POST['barangay_id'];
        $position = $_POST['position'];
        $fullname = trim($_POST['fullname']);
        $gender = $_POST['gender'];
        $email = trim($_POST['email']);
        $contact = trim($_POST['contact']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $status = 'offline';

        // Hash password using sha1 (not recommended for production!)
        $hashed_password = sha1($password);

        // Check if username or email already exists
        $check_stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE username = :username OR email = :email");
        $check_stmt->execute([':username' => $username, ':email' => $email]);

        if ($check_stmt->rowCount() > 0) {
            $_SESSION['error'] = "Username or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO tbl_admin 
                (barangay_id, position, fullname, gender, email, contact_number, username, password, status)
                VALUES 
                (:barangay_id, :position, :fullname, :gender, :email, :contact, :username, :password, :status)");

            $stmt->execute([
                ':barangay_id' => $barangay_id,
                ':position' => $position,
                ':fullname' => $fullname,
                ':gender' => $gender,
                ':email' => $email,
                ':contact' => $contact,
                ':username' => $username,
                ':password' => $hashed_password,
                ':status' => $status
            ]);

            $_SESSION['success'] = "Admin added successfully!";
            header("Location: admin_management.php");
            exit();
        }
    } catch (PDOException $e) {
        // Log error if needed: error_log($e->getMessage());
        $_SESSION['error'] = "An error occurred while adding the admin. Please try again.";
    }
}

// Get barangay list
$stmt = $conn->query("SELECT * FROM tbl_barangay ORDER BY id DESC");
$barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Select -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
            <div class="block-header">
                <ol style="font-size: 15px;" class="breadcrumb breadcrumb-col-red">
                    <li><a href="index.php"><i style="font-size: 20px;" class="material-icons">home</i>
                            Dashboard</a></li>
                    <li class="active"><i style="font-size: 20px;" class="material-icons">description</i> Admin Management
                    </li>
                    <li class="active"><i style="font-size: 20px;" class="material-icons">description</i> Add Admin
                    </li>
                </ol>
            </div>
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>ADD ADMIN</h2>
                        </div>
                        <div class="body">
                            <form id="add_admin_validation" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- Left Column: Request Info and Personal Info -->
                                    <div class="col-md-6 pr-4">
                                        <!-- Personal Information -->
                                        <h4 class="bold span-or" style="font-weight: 900; color: #B6771D;">Personal Information</h4>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group form-float">
                                                    <label class="form-label">Barangay <span style="color: red;">*</span></label>
                                                    <select class="form-control select-form select2" name="barangay_id" required>
                                                        <option value="" disabled selected>SELECT BARANGAY</option>
                                                        <?php foreach ($barangays as $barangay): ?>
                                                            <option value="<?= $barangay['id']; ?>">
                                                                <?= htmlspecialchars($barangay['barangay_name']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group form-float">
                                                    <label class="form-label">Type <span style="color: red;">*</span></label>
                                                    <select class="form-control select-form select2" name="position" required>
                                                        <option value="" disabled selected>SELECT POSITION</option>
                                                        <option value="administrator">Administrator</option>
                                                        <option value="barangay official">Barangay Official</option>
                                                        <option value="staff">Staff</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="fullname" required>
                                                <label class="form-label">Fullname <span style="color: red;">*</span></label>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="gender">Gender <span style="color: red;">*</span></label><br>
                                            <input type="radio" name="gender" id="male" value="Male" required checked>
                                            <label for="male">Male</label>
                                            <input type="radio" name="gender" id="female" value="Female" class="m-l-20">
                                            <label for="female">Female</label>
                                        </div>

                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="email" class="form-control" name="email" id="email" required>
                                                <label class="form-label">Email <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="number" class="form-control" name="contact" required>
                                                <label class="form-label">Mobile # <span style="color: red;">*</span></label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column: Requirements and Pickup Info -->
                                    <div class="col-md-6 pl-4">
                                        <h4 class="bold span-or mb-4" style="font-weight: 900; color: #B6771D;">Account Details</h4>
                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="username" id="username" required>
                                                <label class="form-label">Username <span style="color: red;">*</span></label>
                                            </div>
                                        </div>


                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="password" class="form-control" name="password" required>
                                                <label class="form-label">Password <span style="color: red;">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div style="display: flex; justify-content: end; gap: 5px; margin-top: 10px;">
                                    <button class="btn bg-teal waves-effect" type="submit">Save</button>
                                    <button class="btn btn-link waves-effect" type="button" onclick="window.location.href = 'admin_management.php'">Go back</button>
                                </div>
                            </form>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        $('#add_admin_validation').validate({
            rules: {
                username: {
                    required: true,
                    minlength: 3,
                    remote: {
                        url: 'validation/check_admin_unique.php',
                        type: 'POST',
                        data: {
                            type: 'username',
                            value: function() {
                                return $('[name="username"]').val();
                            }
                        }
                    }
                },
                email: {
                    required: true,
                    email: true,
                    remote: {
                        url: 'validation/check_admin_unique.php',
                        type: 'POST',
                        data: {
                            type: 'email',
                            value: function() {
                                return $('[name="email"]').val();
                            }
                        }
                    }
                }
            },
            messages: {
                username: {
                    required: "Username is required",
                    minlength: "Username must be at least 3 characters",
                    remote: "Username already exists"
                },
                email: {
                    required: "Email is required",
                    email: "Please enter a valid email address",
                    remote: "Email already exists"
                }
            },
            highlight: function(input) {
                $(input).parents('.form-line').addClass('error');
            },
            unhighlight: function(input) {
                $(input).parents('.form-line').removeClass('error');
            },
            errorPlacement: function(error, element) {
                $(element).parents('.form-group').append(error);
            }
        });
    </script>



    <script>
        $(document).ready(function() {
            $('.select2').select2({
                allowClear: true
            });
        });
    </script>


</body>

</html>