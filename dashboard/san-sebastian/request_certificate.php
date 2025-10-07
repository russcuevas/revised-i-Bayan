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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $certificate_type = $_POST['certificate_type'];
        $resident_id = $_POST['resident_id'];
        $purok = $_POST['purok'];
        $purpose = $_POST['purpose'];
        $fullname = $_POST['fullname'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $is_resident = $_POST['botante'];
        $picked_up_by = $_POST['fullname_relatives'] ?? null;
        $relationship = $_POST['relationship'] ?? null;
        $status = 'Pending';

        // Determine amount
        $total_amount = ($certificate_type === 'Certificate of Indigency') ? 0 : 50;

        $valid_id_path = '';
        $birth_cert_path = '';

        // Handle valid ID upload
        if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] == 0) {
            $valid_id_name = basename($_FILES['valid_id']['name']);
            $valid_id_dest = "../../public/request/valid_id/" . $valid_id_name;
            move_uploaded_file($_FILES['valid_id']['tmp_name'], $valid_id_dest);
            $valid_id_path = $valid_id_name;
        }

        // Handle birth certificate upload
        if (isset($_FILES['birth_certificate']) && $_FILES['birth_certificate']['error'] == 0) {
            $birth_cert_name = basename($_FILES['birth_certificate']['name']);
            $birth_cert_dest = "../../public/request/birth_certificate/" . $birth_cert_name;
            move_uploaded_file($_FILES['birth_certificate']['tmp_name'], $birth_cert_dest);
            $birth_cert_path = $birth_cert_name;
        }

        // Fetch barangay ID
        $stmt_brg = $conn->prepare("SELECT id FROM tbl_barangay WHERE LOWER(REPLACE(barangay_name, ' ', '')) = ?");
        $stmt_brg->execute([strtolower($barangay)]);
        $barangay_data = $stmt_brg->fetch(PDO::FETCH_ASSOC);

        if (!$barangay_data) {
            throw new Exception("Barangay not found.");
        }

        $barangay_id = $barangay_data['id'];

        // Insert certificate request
        $stmt = $conn->prepare("INSERT INTO tbl_certificates (
            resident_id, purok, certificate_type, purpose, fullname, gender, email, contact,
            valid_id, birth_certificate, is_resident, picked_up_by, relationship,
            for_barangay, total_amount, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $resident_id,
            $purok,
            $certificate_type,
            $purpose,
            $fullname,
            $gender,
            $email,
            $contact,
            $valid_id_path,
            $birth_cert_path,
            $is_resident,
            $picked_up_by,
            $relationship,
            $barangay_id,
            $total_amount,
            $status
        ]);

        // ✅ Insert activity log
        $insert_log = $conn->prepare("INSERT INTO tbl_activity_logs (resident_id, action, barangay_id, created_at)
            VALUES (:resident_id, :action, :barangay_id, NOW())");

        $insert_log->execute([
            ':resident_id' => $resident_id,
            ':action' => 'Requested Certificate (' . htmlspecialchars($certificate_type) . ')',
            ':barangay_id' => $barangay_id
        ]);

        $_SESSION['success'] = "Certificate request submitted successfully!";
        header("Location: certificate_issuance.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: certificate_issuance.php");
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
                                <input type="hidden" name="resident_id" value="<?= htmlspecialchars($resident_id) ?>">
                                <div class="row">
                                    <!-- Left Column: Request Info and Personal Info -->
                                    <div class="col-md-6 pr-4">
                                        <!-- Request Information -->
                                        <h4 class="bold span-or" style="font-weight: 900; color: #1a49cb;">Request Information</h4>
                                        <div class="form-group form-float">
                                            <label class="form-label">Certificate Type <span style="color: red;">*</span></label>
                                            <select class="form-control select-form" name="certificate_type" required>
                                                <option value="" disabled selected>CHOOSE CERTIFICATE</option>
                                                <option value="Barangay Clearance">Barangay Clearance</option>
                                                <option value="Barangay Functionaries">Barangay Functionaries</option>
                                                <option value="Certificate of Indigency">Certificate of Indigency</option>
                                                <option value="Certificate of Relationship">Certificate of Relationship</option>
                                                <option value="Certificate of Residency">Certificate of Residency</option>
                                                <option value="Certificate of Non-Residency">Certificate of Non-Residency</option>
                                                <option value="Certificate of Good Moral">Certificate of Good Moral</option>
                                                <option value="Certificate of Transient">Certificate of Transient</option>
                                                <option value="Certificate of Low Income">Certificate of Low Income</option>
                                                <option value="Certificate of No Income">Certificate of No Income</option>
                                                <option value="Certificate of Permit">Certificate of Permit</option>
                                                <option value="Certificate of Solo Parents">Certificate of Solo Parents</option>
                                                <option value="Certificate of Guardianship">Certificate of Guardianship</option>
                                            </select>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="purpose" required>
                                                <label class="form-label">Purpose <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <!-- Personal Information -->
                                        <h4 class="bold span-or" style="font-weight: 900; color: #1a49cb;">Personal Information</h4>
                                        <div class="form-group form-float" style="margin-top: 30px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="fullname" required>
                                                <label class="form-label">Fullname <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float">
                                            <div class="form-line">
                                                <input type="number" class="form-control" name="purok" required>
                                                <label class="form-label">Purok <span style="color: red;">*</span></label>
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

                                    <!-- Right Column: Requirements and Pickup Info -->
                                    <div class="col-md-6 pl-4">
                                        <h4 class="bold span-or mb-4" style="font-weight: 900; color: #1a49cb;">Requirements</h4>
                                        <div class="form-group form-float" style="margin-top: 30px;">
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

                                        <h5 class="bold span-or" style="font-weight: 900; color: #1a49cb;">Pickup by: (leave blank if you will pick it up)</h5>

                                        <div class="form-group form-float" style="margin-top: 30px;">
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

                                <!-- Total Price Display -->
                                <div style="display: flex; justify-content: end; margin-top: 20px;">
                                    <h5 style="font-weight: bold; color: brown;">
                                        AMOUNT TO PAY: <span id="amount-to-pay" style="color: #000;">₱50.00 pesos</span>
                                    </h5>
                                </div>


                                <!-- Buttons -->
                                <div style="display: flex; justify-content: end; gap: 5px; margin-top: 10px;">
                                    <button class="btn bg-teal waves-effect" type="submit">Request</button>
                                    <button class="btn btn-link waves-effect" type="button" onclick="window.location.href = 'certificate_issuance.php'">Go back</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const certificateSelect = document.querySelector('select[name="certificate_type"]');
            const amountSpan = document.querySelector('#amount-to-pay');

            const freeCertificates = [
                "Certificate of Indigency"
            ];

            certificateSelect.addEventListener('change', function() {
                const selected = this.value;

                if (freeCertificates.includes(selected)) {
                    amountSpan.innerText = '₱0.00 pesos';
                } else {
                    amountSpan.innerText = '₱50.00 pesos';
                }
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