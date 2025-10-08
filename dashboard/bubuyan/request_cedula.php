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

$stmt = $conn->prepare("SELECT is_approved FROM tbl_residents WHERE id = ?");
$stmt->execute([$resident_id]);
$resident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resident) {
    $_SESSION['error'] = "Resident not found.";
    header("Location: ../../login.php");
    exit();
}

$is_approved = $resident['is_approved'];
$_SESSION["is_approved_$barangay"] = $is_approved;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $certificate_type = $_POST['certificate_type'];
        $fullname = $_POST['fullname'];
        $civil_status = $_POST['civil_status'];
        $gender = $_POST['gender'];
        $tin = $_POST['tin'];
        $purok = $_POST['purok'];
        $profession = $_POST['profession'];
        $email = $_POST['email'];
        $contact = $_POST['contact'];
        $purpose = $_POST['purpose'];
        $is_resident = ($_POST['botante'] === 'yes') ? 1 : 0;
        $picked_up_by = $_POST['fullname_relatives'] ?? null;
        $relationship = $_POST['relationship'] ?? null;
        $total_amount = $_POST['total_amount'] ?? null;
        $status = 'Pending';

        // Upload valid ID
        $valid_id_path = '';
        if (isset($_FILES['valid_id']) && $_FILES['valid_id']['error'] === 0) {
            $valid_id_name = uniqid() . "_" . basename($_FILES['valid_id']['name']);
            $valid_id_dest = "../../public/request/valid_id/" . $valid_id_name;
            move_uploaded_file($_FILES['valid_id']['tmp_name'], $valid_id_dest);
            $valid_id_path = $valid_id_name;
        } else {
            throw new Exception("Valid ID upload failed.");
        }

        // Upload birth certificate
        $birth_cert_path = '';
        if (isset($_FILES['birth_certificate']) && $_FILES['birth_certificate']['error'] === 0) {
            $birth_cert_name = uniqid() . "_" . basename($_FILES['birth_certificate']['name']);
            $birth_cert_dest = "../../public/request/birth_certificate/" . $birth_cert_name;
            move_uploaded_file($_FILES['birth_certificate']['tmp_name'], $birth_cert_dest);
            $birth_cert_path = $birth_cert_name;
        } else {
            throw new Exception("Birth Certificate upload failed.");
        }

        // Get for_barangay id
        $barangay_stmt = $conn->prepare("SELECT id FROM tbl_barangay WHERE LOWER(REPLACE(barangay_name, ' ', '')) = ?");
        $barangay_stmt->execute([strtolower($barangay)]);
        $barangay_data = $barangay_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$barangay_data) {
            throw new Exception("Barangay not found.");
        }
        $for_barangay_id = $barangay_data['id'];

        // Generate unique document number (CEDULA-xxxxxx)
        $document_number = strtoupper(uniqid('CEDULA-'));

        // Insert into tbl_cedula
        $stmt = $conn->prepare("INSERT INTO tbl_cedula ( certificate_type,
            resident_id, document_number, fullname, civil_status, gender, tin, purok, profession,
            email, contact, valid_id, birth_certificate, is_resident, purpose, for_barangay,
            total_amount, status, picked_up_by, relationship
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $certificate_type,
            $resident_id,
            $document_number,
            $fullname,
            $civil_status,
            $gender,
            $tin,
            $purok,
            $profession,
            $email,
            $contact,
            $valid_id_path,
            $birth_cert_path,
            $is_resident,
            $purpose,
            $for_barangay_id,
            $total_amount,
            $status,
            $picked_up_by,
            $relationship
        ]);

        $insert_activity = $conn->prepare("INSERT INTO tbl_activity_logs (resident_id, action, barangay_id, created_at)
        VALUES (:resident_id, :action, :barangay_id, NOW())");

        $insert_activity->execute([
            ':resident_id' => $resident_id,
            ':action' => 'Requested Certificate (Cedula)',
            ':barangay_id' => $for_barangay_id
        ]);

        $_SESSION['success'] = "Cedula request submitted successfully!";
        header("Location: certificate_cedula.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: certificate_cedula.php");
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
                                    <!-- Left Column: Request Info + Personal Info -->
                                    <div class="col-md-6 pr-4">
                                        <!-- Request Information -->
                                        <h4 class="bold span-or" style="font-weight: 900; color: #B6771D;">Request Information</h4>
                                        <br>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input style="background-color: #555; color: #ccc !important; padding: 10px !important"
                                                    type="text" class="form-control" name="certificate_type" value="Cedula" readonly>
                                                <label class="form-label">Certificate Type <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="purpose" required>
                                                <label class="form-label">Purpose <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <!-- Personal Information -->
                                        <h4 class="bold span-or mt-4" style="font-weight: 900; color: #B6771D;">Personal Information</h4>

                                        <div class="form-group form-float mt-3" style="margin-top: 30px">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="fullname" required>
                                                <label class="form-label">Fullname <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float">
                                            <label class="form-label">Civil Status <span style="color: red">*</span></label>
                                            <select class="form-control select-form" name="civil_status">
                                                <option value="" disabled selected>SELECT CIVIL STATUS</option>
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="gender">Gender <span style="color: red;">*</span></label><br>
                                            <input type="radio" name="gender" value="male" id="male" required checked>
                                            <label for="male">Male</label>
                                            <input type="radio" name="gender" value="female" id="female" class="m-l-20">
                                            <label for="female">Female</label>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="purok" required>
                                                <label class="form-label">Purok <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="tin" required>
                                                <label class="form-label">Tin # <span style="color: red;">*</span></label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="profession" required>
                                                <label class="form-label">Profession <span style="color: red;">*</span></label>
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

                                    <!-- Right Column: Requirements + Payment + Pickup -->
                                    <div class="col-md-6 pl-4">
                                        <!-- Requirements -->
                                        <h4 class="bold span-or mb-4" style="font-weight: 900; color: #B6771D;">Requirements</h4>
                                        <br>

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

                                        <!-- Payment Info -->
                                        <h5 class="bold span-or mt-4" style="font-weight: 900; color: #B6771D;">Payment Information</h5>

                                        <div class="form-group form-float mt-3" style="margin-top: 20px;">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="basic_tax" value="5.00" readonly style="background-color: #555; padding: 10px; color: #ccc !important">
                                                <label class="form-label">A. Basic Community Tax</label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="additional_tax">
                                                <label class="form-label">B. Additional Community Tax</label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="business_income">
                                                <label class="form-label">1. Gross Receipts or Earnings from Business (Annual)</label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="professional_income">
                                                <label class="form-label">2. Salaries/Gross Earnings from Profession (Annual)</label>
                                            </div>
                                        </div>

                                        <div class="form-group form-float mt-3">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="property_income">
                                                <label class="form-label">3. Income from Real Property (Annual)</label>
                                            </div>
                                        </div>

                                        <!-- Pickup By -->
                                        <h5 class="bold span-or mt-4" style="font-weight: 900; color: #B6771D;"> Pickup by: (leave blank if you will pick it up)
                                        </h5>

                                        <div class="form-group form-float mt-3" style="margin-top: 20px;">
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

                                <!-- Totals + Buttons -->
                                <div style="display: flex; justify-content: end; margin-top: 20px;">
                                    <div>
                                        <h5 style="font-weight: bold; color: brown;">Total: <span data-id="total">₱50.00</span></h5>
                                        <h5 style="font-weight: bold; color: brown;">Interest: <span data-id="interest">₱50.00</span></h5>
                                        <h5 style="font-weight: bold; color: brown;">Extra for certificate: <span data-id="extra">₱50.00</span></h5>
                                        <h5 style="font-weight: bold; color: brown;">Amount To Pay: <span data-id="due">₱50.00</span></h5>

                                    </div>
                                </div>

                                <input type="hidden" name="total_amount" id="total_amount" value="0">

                                <div style="display: flex; justify-content: end; gap: 5px; margin-top: 10px;">
                                    <button class="btn bg-teal waves-effect" type="submit">Request</button>
                                    <button class="btn btn-link waves-effect" type="button" onclick="window.location.href = 'certificate_cedula.php'">Go back</button>
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
        document.addEventListener('DOMContentLoaded', () => {
            // Get payment inputs
            const basicTaxInput = document.querySelector('input[name="basic_tax"]');
            const additionalTaxInput = document.querySelector('input[name="additional_tax"]');
            const businessIncomeInput = document.querySelector('input[name="business_income"]');
            const professionalIncomeInput = document.querySelector('input[name="professional_income"]');
            const propertyIncomeInput = document.querySelector('input[name="property_income"]');

            // Get summary spans (you can add IDs for easy targeting)
            const totalSpan = document.querySelector('h5 span[data-id="total"]');
            const interestSpan = document.querySelector('h5 span[data-id="interest"]');
            const dueSpan = document.querySelector('h5 span[data-id="due"]');
            const extraSpan = document.querySelector('h5 span[data-id="extra"]');

            // Hidden input for submission
            const totalAmountInput = document.getElementById('total_amount');

            // For this example, let's assume fixed interest and extra fees
            const fixedInterest = 50.00;
            const fixedExtra = 50.00;

            // Helper function to parse float safely
            function parseFloatSafe(value) {
                return parseFloat(value) || 0;
            }

            function calculateTax() {
                const basicTax = parseFloatSafe(basicTaxInput.value);
                const additionalTax = parseFloatSafe(additionalTaxInput.value);
                const businessIncome = parseFloatSafe(businessIncomeInput.value);
                const professionalIncome = parseFloatSafe(professionalIncomeInput.value);
                const propertyIncome = parseFloatSafe(propertyIncomeInput.value);

                // Calculate income tax (1% of total income as example)
                const incomeTax = 0.01 * (businessIncome + professionalIncome + propertyIncome);

                // Total tax
                const totalTax = basicTax + additionalTax + incomeTax;

                // Total amount due including interest and extra fees
                const totalDue = totalTax + fixedInterest + fixedExtra;

                // Update UI
                if (totalSpan) totalSpan.textContent = `₱${totalTax.toFixed(2)}`;
                if (interestSpan) interestSpan.textContent = `₱${fixedInterest.toFixed(2)}`;
                if (dueSpan) dueSpan.textContent = `₱${totalDue.toFixed(2)}`;
                if (extraSpan) extraSpan.textContent = `₱${fixedExtra.toFixed(2)}`;

                // Set hidden input for form submission
                totalAmountInput.value = totalDue.toFixed(2);
            }

            // Add event listeners to payment inputs to recalc on input
            [additionalTaxInput, businessIncomeInput, professionalIncomeInput, propertyIncomeInput].forEach(input => {
                input.addEventListener('input', calculateTax);
            });

            // Initialize on page load
            calculateTax();
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