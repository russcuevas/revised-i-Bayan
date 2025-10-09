<?php
// session
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

// database connection
include '../database/connection.php';

$superadmin_id = $_SESSION['superadmin_id'];

// fetch profile data
$stmt = $conn->prepare("SELECT * FROM tbl_superadmin WHERE id = :id");
$stmt->execute([':id' => $superadmin_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// update profile function
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $age = $_POST['age'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $check_email = $conn->prepare("SELECT * FROM tbl_superadmin WHERE email = :email AND id != :id");
    $check_email->execute([
        ':email' => $email,
        ':id' => $superadmin_id
    ]);

    if ($check_email->rowCount() > 0) {
        $_SESSION['error'] = "Email already exists.";
    } else {
        $password_hash = !empty($password) ? sha1($password) : $profile['password'];

        $update = $conn->prepare("UPDATE tbl_superadmin SET first_name = :first_name, last_name = :last_name, age = :age, phone_number = :phone_number, email = :email, password = :password WHERE id = :id");
        $update->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':age' => $age,
            ':phone_number' => $phone_number,
            ':email' => $email,
            ':password' => $password_hash,
            ':id' => $superadmin_id
        ]);

        $_SESSION['superadmin_name'] = $first_name . ' ' . $last_name;
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: edit_profile.php");
        exit();
    }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

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
        <div class="container-fluid" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
            <div class="row clearfix" style="width: 100%; max-width: 600px;">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="body">
                            <h4 class="text-center" style="font-weight: 900; color: #B6771D;">Update Profile</h4>
                            <form id="form_validation" method="POST">
                                <div class="form-group form-float mt-3" style="margin-top: 30px">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="first_name" required value="<?= htmlspecialchars($profile['first_name']) ?>">
                                        <label class="form-label">First Name</label>
                                    </div>
                                </div>

                                <div class="form-group form-float mt-3" style="margin-top: 30px">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="last_name" required value="<?= htmlspecialchars($profile['last_name']) ?>">
                                        <label class="form-label">Last Name</label>
                                    </div>
                                </div>

                                <div class="form-group form-float mt-3" style="margin-top: 30px">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="age" required value="<?= htmlspecialchars($profile['age']) ?>">
                                        <label class="form-label">Age</label>
                                    </div>
                                </div>

                                <div class="form-group form-float mt-3" style="margin-top: 30px">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="phone_number" required value="<?= htmlspecialchars($profile['phone_number']) ?>">
                                        <label class="form-label">Phone number</label>
                                    </div>
                                </div>

                                <div class="form-group form-float mt-3" style="margin-top: 30px">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="email" required value="<?= htmlspecialchars($profile['email']) ?>">
                                        <label class="form-label">Email</label>
                                    </div>
                                </div>

<div class="form-group form-float mt-3 position-relative" style="margin-top: 30px;">
    <div class="form-line position-relative">
        <input type="password" class="form-control pe-5" id="updatePasswordField" name="password"
               placeholder="Leave blank to keep current password">
        <label class="form-label">Password</label>

        <!-- 👁️ Toggle Icon -->
        <i class="bi bi-eye-slash" id="toggleUpdatePasswordField"
           style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer;"></i>
    </div>
</div>

                                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                                    <button type="submit" class="btn bg-teal waves-effect">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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

    <!-- Select Plugin Js -->
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>

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
    <script src="js/admin.js"></script>
    <script src="js/pages/index.js"></script>

    <!-- Demo Js -->
    <script src="js/demo.js"></script>
    <script src="plugins/sweetalert/sweetalert.min.js"></script>
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

    <script>
    const toggleUpdatePasswordField = document.getElementById("toggleUpdatePasswordField");
    const updatePasswordField = document.getElementById("updatePasswordField");

    toggleUpdatePasswordField.addEventListener("click", function () {
        const type = updatePasswordField.getAttribute("type") === "password" ? "text" : "password";
        updatePasswordField.setAttribute("type", type);
        this.classList.toggle("bi-eye");
        this.classList.toggle("bi-eye-slash");
    });
    </script>
</body>
</html>