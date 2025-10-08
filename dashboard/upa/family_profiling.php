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
$resident_id = $_SESSION["resident_id_$barangay"] ?? null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && $resident_id) {
    $relationship = $_POST['relationship'];
    $barangay_address = $_POST['barangay_address'] ?? null;
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $suffix = $_POST['suffix'] ?? null;
    $purok = $_POST['purok'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['birthday'];
    $birthplace = $_POST['birthplace'];
    $age = $_POST['age'];
    $civil_status = $_POST['civil_status'];
    $is_working = $_POST['is_working'];
    $occupation = $_POST['occupation'] ?? null;
    $school = $_POST['school'] ?? null;
    $is_barangay_voted = ($_POST['botante'] === 'yes') ? 1 : 0;
    $years_in_barangay = $_POST['gaano_katagal'];
    $phone_number = $_POST['mobile'] ?? null;
    $philhealth_number = $_POST['philhealth'] ?? null;

    $stmt = $conn->prepare("INSERT INTO tbl_residents_family_members (
        resident_id, barangay_address, first_name, middle_name, last_name, suffix, purok, relationship, gender, date_of_birth, birthplace, age,
        civil_status, is_working, is_approved, is_barangay_voted, years_in_barangay, phone_number, philhealth_number, school, occupation
    ) VALUES (
        :resident_id, :barangay_address, :first_name, :middle_name, :last_name, :suffix, :purok, :relationship, :gender, :date_of_birth, :birthplace, :age,
        :civil_status, :is_working, 0, :is_barangay_voted, :years_in_barangay, :phone_number, :philhealth_number, :school, :occupation
    )");

    $success = $stmt->execute([
        ':resident_id' => $resident_id,
        ':barangay_address' => $barangay_address,
        ':first_name' => $first_name,
        ':middle_name' => $middle_name,
        ':last_name' => $last_name,
        ':suffix' => $suffix,
        ':purok' => $purok,
        ':relationship' => $relationship,
        ':gender' => $gender,
        ':date_of_birth' => $date_of_birth,
        ':birthplace' => $birthplace,
        ':age' => $age,
        ':civil_status' => $civil_status,
        ':is_working' => $is_working,
        ':is_barangay_voted' => $is_barangay_voted,
        ':years_in_barangay' => $years_in_barangay,
        ':phone_number' => $phone_number,
        ':philhealth_number' => $philhealth_number,
        ':school' => $school,
        ':occupation' => $occupation
    ]);

    if ($success) {
        $_SESSION['success'] = "Family members added succesfully please check account owner email and wait for the approval of the admin";
        header("Location: family_profiling.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding family members";
        header("Location: family_profiling.php");
        exit();
    }
}


// fetch family members
$family_members = [];

if ($resident_id) {
    $stmt = $conn->prepare("SELECT * FROM tbl_residents_family_members WHERE resident_id = :resident_id");
    $stmt->execute([':resident_id' => $resident_id]);
    $family_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$is_verified_member_exist = false;

foreach ($family_members as $member) {
    if ($member['is_approved'] == 1) {
        $is_verified_member_exist = true;
        break;
    }
}



?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iBayan</title>
    <!-- Favicon-->
    <link rel="icon" href="../img/logo.png" type="image/x-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Waves Effect Css -->
    <link href="../plugins/node-waves/waves.css" rel="stylesheet" />

    <!-- Animation Css -->
    <link href="../plugins/animate-css/animate.css" rel="stylesheet" />

    <!-- JQuery DataTable Css -->
    <link href="../plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">

    <!-- Bootstrap Select Css -->

    <!-- Custom Css -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <!-- Sweetalert Css -->
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />

    <link href="../css/themes/all-themes.css" rel="stylesheet" />
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
                    <li class="active"><i style="font-size: 20px;" class="material-icons">description</i> Family Profiling
                    </li>
                </ol>
            </div>
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>FAMILY PROFILING LIST</h2>
                        </div>
                        <div class="body">
                            <div class="alert bg-red" role="alert">
                                <strong>Note:</strong> Once your family members are verified,
                                you will no longer be able to add or modify them. Please contact your <strong>Barangay Admin</strong>
                                if changes are needed or if you want to add new family members.
                            </div>
                            <ul id="family-profiling-list">
                                <?php if (!empty($family_members)): ?>
                                    <?php foreach ($family_members as $member): ?>
                                        <li>
                                            <?= htmlspecialchars($member['first_name'] . ' ' . $member['middle_name'] . ' ' . $member['last_name'] .
                                                (!empty($member['suffix']) ? ' ' . $member['suffix'] : '')) ?>
                                            (<span style="text-transform: capitalize;"><?= htmlspecialchars($member['relationship']) ?></span>)
                                            -
                                            <span style="font-weight: bold; color: <?= $member['is_approved'] ? 'green' : 'red' ?>;">
                                                <?= $member['is_approved'] ? 'Verified' : 'Not Verified' ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>No family members found.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>


                <?php if (!$is_verified_member_exist): ?>

                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>ADD FAMILY MEMBERS</h2>
                            </div>
                            <div class="body">
                                <form id="add_family_profiling" action="" method="POST" style="margin-top: 20px;">
                                    <input type="hidden" name="resident_id" value="<?= htmlspecialchars($_SESSION["resident_id_" . basename(__DIR__)] ?? '') ?>">
                                    <input type="hidden" name="barangay_address" value="<?= htmlspecialchars($_SESSION["barangay_id_" . basename(__DIR__)] ?? '') ?>">

                                    <h4 class="bold span-or mb-4" style="font-weight: 900; color: #B6771D;">
                                        Relationship to Family Member
                                    </h4> <br>

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

                                        <div class="col-md-6" style="margin-top: 10px;">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="first_name" required>
                                                    <label class="form-label">First name <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top: 10px;">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="middle_name" required>
                                                    <label class="form-label">Middle name <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top: 10px;">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="last_name" required>
                                                    <label class="form-label">Last name <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top: 10px; margin-bottom: 25px">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="suffix">
                                                    <label class="form-label">Suffix</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6" style="margin-top: 10px;">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="purok" required>
                                                    <label class="form-label">Purok <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6" style="margin-bottom: 20px;">
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
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="date" class="form-control" name="birthday" required>
                                                    <label class="form-label">Date of birth <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="birthplace" required>
                                                    <label class="form-label">Birthplace <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6" style="margin-top: 13px;">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="number" class="form-control" name="age" required>
                                                    <label class="form-label">Age <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <label class="form-label">Civil status <span style="color: red;">*</span></label>
                                                <select class="form-control select-form" name="civil_status" required>
                                                    <option value="" disabled selected>CHOOSE CIVIL STATUS</option>
                                                    <option value="single">Single</option>
                                                    <option value="married">Married</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-bottom: 20px;">
                                        <div class="col-md-12">
                                            <label><strong>Current Status</strong></label><br>
                                            <input type="radio" name="is_working" value="1" id="working">
                                            <label for="working">Working</label>

                                            <input type="radio" name="is_working" value="2" id="student" style="margin-left: 15px;">
                                            <label for="student">Student</label>

                                            <input type="radio" name="is_working" value="3" id="none" style="margin-left: 15px;" checked>
                                            <label for="none">None</label>
                                        </div>
                                    </div>

                                    <!-- Occupation Input -->
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

                                    <!-- School Input -->
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

                                    <h4 class="bold span-or mb-4" style="font-weight: 900; color: #B6771D;">
                                        Other Information
                                    </h4> <br>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="botante">Botante ba dito? <span style="color: red;">*</span></label><br>

                                                <input type="radio" name="botante" id="yes" value="yes" checked>
                                                <label for="yes">Yes</label>

                                                <input type="radio" name="botante" id="no" value="no" class="m-l-20">
                                                <label for="no">No</label>

                                            </div>
                                        </div>
                                        <div class="col-md-6" style="margin-top: 15px !important;">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="gaano_katagal" required>
                                                    <label class="form-label">Gaano katagal ka sa barangay? <span style="color: red;">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="number" class="form-control" name="mobile">
                                                    <label class="form-label">Mobile #</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group form-float">
                                                <div class="form-line">
                                                    <input type="text" class="form-control" name="philhealth">
                                                    <label class="form-label">Philhealth #</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right">
                                        <button class="btn bg-teal waves-effect" type="submit">SAVE</button>
                                        <button type="button" class="btn btn-link waves-effect" onclick="window.location.href = 'all_clients.php'">BACK</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                <?php endif; ?>

            </div>
            <!-- #END# Basic Validation -->
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

    <!-- Custom Js -->
    <script src="../js/admin.js"></script>
    <script src="../js/pages/tables/jquery-datatable.js"></script>
    <!-- SweetAlert Plugin Js -->
    <script src="../plugins/sweetalert/sweetalert.min.js"></script>
    <!-- Demo Js -->
    <script src="../js/demo.js"></script>

    <!-- CURRENT STATUS SCRIPT -->
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
        $('#add_family_profiling').validate({
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