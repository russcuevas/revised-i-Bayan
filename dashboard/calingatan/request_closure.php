<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "resident_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    $_SESSION['error'] = "Please log in first.";
    header("Location: ../../login.php");
    exit();
}

$resident_id = $_SESSION[$session_key];
$is_approved = $_SESSION["is_approved_$barangay"] ?? null;

if ($is_approved != 1) {
    $_SESSION['error'] = "You must be approved to request a certificate.";
    header("Location: certificate_issuance.php");
    exit();
}

$businessTypes = [];
$stmt = $conn->prepare("SELECT id, name, price FROM tbl_business_trade ORDER BY name ASC");
$stmt->execute();
$businessTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $certificate_type = $_POST['certificate_type'];
        $purpose = $_POST['purpose'];
        $business_name = $_POST['business_name'];
        $business_trade = $_POST['business_type'];
        $business_address = $_POST['business_address'];
        $owner_name = $_POST['owner_name'];
        $owner_purok = $_POST['owner_purok'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $is_resident = $_POST['botante'];
        $picked_up_by = $_POST['fullname_relatives'] ?? null;
        $relationship = $_POST['relationship'] ?? null;
        $status = 'Pending';

        $total_amount = 50.00;

        $valid_id_path = '';
        $birth_cert_path = '';

        // Upload valid ID
        if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] == 0) {
            $valid_id_name = basename($_FILES['valid_id']['name']);
            $valid_id_dest = "../../public/request/valid_id/" . $valid_id_name;
            move_uploaded_file($_FILES['valid_id']['tmp_name'], $valid_id_dest);
            $valid_id_path = $valid_id_name;
        }

        // Upload birth certificate
        if (isset($_FILES['birth_certificate']) && $_FILES['birth_certificate']['error'] == 0) {
            $birth_cert_name = basename($_FILES['birth_certificate']['name']);
            $birth_cert_dest = "../../public/request/birth_certificate/" . $birth_cert_name;
            move_uploaded_file($_FILES['birth_certificate']['tmp_name'], $birth_cert_dest);
            $birth_cert_path = $birth_cert_name;
        }

        // Get for_barangay id
        $barangay_stmt = $conn->prepare("SELECT id FROM tbl_barangay WHERE LOWER(REPLACE(barangay_name, ' ', '')) = ?");
        $barangay_stmt->execute([strtolower($barangay)]);
        $barangay_data = $barangay_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$barangay_data) {
            $_SESSION['error'] = "Barangay not found in tbl_barangay.";
            header("Location: certificate_closure.php");
            exit();
        }

        $for_barangay_id = $barangay_data['id'];

        // Generate random document number
        $document_number = strtoupper(uniqid('CLOSURE-'));

        // Insert into tbl_operate
        $stmt = $conn->prepare("INSERT INTO tbl_closure (
        resident_id, document_number, picked_up_by, relationship, 
        certificate_type, purpose, business_name, business_trade, business_address,
        owner_name, owner_purok, email, contact, for_barangay, 
        valid_id, birth_certificate, is_resident, total_amount, status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");



        $stmt->execute([
            $resident_id,
            $document_number,
            $picked_up_by,
            $relationship,
            $certificate_type,
            $purpose,
            $business_name,
            $business_trade,
            $business_address,
            $owner_name,
            $owner_purok,
            $email,
            $contact,
            $for_barangay_id,
            $valid_id_path,
            $birth_cert_path,
            $is_resident,
            $total_amount,
            $status
        ]);

        // ✅ Insert activity log
        $insert_log = $conn->prepare("INSERT INTO tbl_activity_logs (resident_id, action, barangay_id, created_at)
            VALUES (:resident_id, :action, :barangay_id, NOW())");

        $insert_log->execute([
            ':resident_id' => $resident_id,
            ':action' => 'Requested Certificate (' . htmlspecialchars($certificate_type) . ')',
            ':barangay_id' => $for_barangay_id
        ]);


        $_SESSION['success'] = "Clearance to Closure request submitted successfully!";
        header("Location: certificate_closure.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: certificate_closure.php");
        exit();
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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Custom Css -->
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <!-- Sweetalert Css -->
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />

    <link href="../css/themes/all-themes.css" rel="stylesheet" />
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
                    <li class="active"><i style="font-size: 20px;" class="material-icons">description</i> Request Certificates
                    </li>
                </ol>
            </div>
            <!-- Basic Validation -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>REQUEST CERTIFICATE</h2>
                        </div>
                        <div class="body">
                            <form id="request_certifacte_validation" method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <!-- Left Column -->
                                    <div class="col-md-6 pr-4">
                                        <!-- Request Information -->
                                        <h4 class="bold span-or" style="font-weight: 900; color: #B6771D; margin-bottom: 0px !important;">
                                            Request Information
                                        </h4><br>

                                        <div class="form-group form-float" style="margin-top: 10px;">
                                            <div class="form-line">
                                                <input style="background-color: #555; color: #ccc !important; padding: 10px !important"
                                                    type="text" class="form-control" name="certificate_type"
                                                    value="Certificate of Business Closure" readonly>
                                                <label class="form-label">Certificate Type <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="purpose" required>
                                                <label class="form-label">Purpose <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <!-- Business Information -->
                                        <h4 class="bold span-or" style="font-weight: 900; color: #B6771D;">
                                            Business Information
                                        </h4>

                                        <div class="form-group form-float">
                                            <label class="form-label">Business Type <span style="color: red;">*</span></label>
                                            <select class="form-control select2" id="business_type" name="business_type" required>
                                                <option value="" disabled selected>SELECT BUSINESS TYPE</option>
                                                <?php foreach ($businessTypes as $type): ?>
                                                    <option
                                                        value="<?= htmlspecialchars($type['id']) ?>"
                                                        data-price="<?= htmlspecialchars($type['price']) ?>">
                                                        <?= htmlspecialchars($type['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="business_name" required>
                                                <label class="form-label">Business Name <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="business_address" required>
                                                <label class="form-label">Business Address <span style="color: red;">*</span></label>
                                            </div>
                                        </div>


                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="owner_name" required>
                                                <label class="form-label">Owner Name <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="owner_purok" required>
                                                <label class="form-label">Purok <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="email" class="form-control" name="email" required>
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

                                    <!-- RIGHT COLUMN -->
                                    <div class="col-md-6 pl-4">

                                        <!-- Requirements -->
                                        <h4 class="bold span-or mb-4" style="font-weight: 900; color: #B6771D;">
                                            Requirements
                                        </h4>
                                        <div class="alert alert-success mt-4" role="alert" style="border-left: 5px solid #B6771D;">
                                        <strong>Data Privacy Notice:</strong> In compliance with Republic Act No. 10173, otherwise known as the Data Privacy Act of 2012, 
                                        all personal data collected will be treated with the highest level of confidentiality. 
                                        Information provided will only be used for legitimate and authorized purposes related to this registration and will not be disclosed to third parties without your consent, unless required by law.
                                        </div>
                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="file" class="form-control" name="valid_id" required>
                                                <label class="form-label">VALID ID <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="file" class="form-control" name="birth_certificate" required>
                                                <label class="form-label">BIRTH CERTIFICATE <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float">
                                            <label for="botante">Resident? <span style="color: red;">*</span></label><br>
                                            <input type="radio" name="botante" id="yes" value="yes" required checked>
                                            <label for="yes">Yes</label>
                                            <input type="radio" name="botante" id="no" value="no" class="m-l-20">
                                            <label for="no">No</label>
                                        </div>

                                        <!-- Pickup Info -->
                                        <h5 class="bold span-or mt-4" style="font-weight: 900; color: #B6771D;">
                                            Pickup by: (leave blank if you will pick it up)
                                        </h5><br>

                                        <div class="form-group form-float" style="margin-top: 10px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="fullname_relatives">
                                                <label class="form-label">Fullname</label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float">
                                            <label class="form-label">Relationship</label>
                                            <select class="form-control select-form" name="relationship">
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

                                <!-- Footer: Amount and Buttons -->
                                <div style="display: flex; justify-content: end; margin-top: 20px;">
                                    <h5 style="font-weight: bold; color: brown;">
                                        AMOUNT TO PAY: <span style="color: #000;">₱50.00 pesos</span>
                                    </h5>
                                </div>

                                <div style="display: flex; justify-content: end; gap: 5px; margin-top: 10px;">
                                    <button class="btn bg-teal waves-effect" type="submit">Request</button>
                                    <button class="btn btn-link waves-effect" type="button" onclick="window.location.href = 'certificate_closure.php'">Go back</button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#business_type').on('change', function() {
                const selectedOption = $(this).find(':selected');
                const businessPrice = parseFloat(selectedOption.data('price')) || 0;
                const certificateCharge = 50;
                const total = businessPrice + certificateCharge;

                $('#total_display').text(total.toFixed(2));
                $('#total_amount').val(total);
            });
        });
    </script>
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