<?php
// session
session_start();

// accept
$show_success = false;

if (isset($_SESSION['accept_success'])) {
    $show_success = true;
    unset($_SESSION['accept_success']);
}

require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

// Get resident ID from query
if (!isset($_GET['id'])) {
    die("No resident ID provided.");
}

$resident_id = $_GET['id'];

// Fetch resident info
$stmt = $conn->prepare("SELECT * FROM tbl_residents WHERE id = ?");
$stmt->execute([$resident_id]);
$resident = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$resident) {
    die("Resident not found.");
}

// Fetch family members of the resident
$stmt_family = $conn->prepare("SELECT * FROM tbl_residents_family_members WHERE resident_id = ?");
$stmt_family->execute([$resident_id]);
$family_members = $stmt_family->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['accept_all'])) {
    $stmt_family_update = $conn->prepare("UPDATE tbl_residents_family_members SET is_approved = 1 WHERE resident_id = ?");
    $stmt_family_update->execute([$resident_id]);

    $stmt_resident_update = $conn->prepare("UPDATE tbl_residents SET is_approved = 1 WHERE id = ?");
    $stmt_resident_update->execute([$resident_id]);

    $_SESSION['success'] = "Successfully registered as verified residents";
    header("Location: manage_residents.php");
    exit();
}


if (isset($_POST['reject_all']) && !empty($_POST['rejection_note'])) {
    $note = $_POST['rejection_note'];
    $stmt = $conn->prepare("DELETE FROM tbl_system_logs_residents WHERE resident_id = ?");
    $stmt->execute([$resident_id]);

    $stmtDeleteFamily = $conn->prepare("DELETE FROM tbl_residents_family_members WHERE resident_id = ?");
    $stmtDeleteFamily->execute([$resident_id]);

    $stmtDeleteResident = $conn->prepare("DELETE FROM tbl_residents WHERE id = ?");
    $stmtDeleteResident->execute([$resident_id]);

    $fullname = $resident['first_name'] . ' ' . $resident['middle_name'] . ' ' . $resident['last_name'];
    $email = $resident['email'];

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'gmanagementtt111@gmail.com';
        $mail->Password = 'skbtosbmkiffrajr';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('gsu-erequest@gmail.com', 'LGU Mataasnakahoy');
        $mail->addAddress($email, $fullname);

        $mail->isHTML(true);
        $mail->Subject = 'Your iBayan Application Has Been Rejected';

        $mail_body = "<p>Dear {$fullname},</p>
                      <p>Your application has been rejected for the following reason:</p>
                      <blockquote><em>{$note}</em></blockquote>
                      <p>If you believe this was a mistake or have further questions, please visit the barangay office.</p>
                      <p>Thank you,<br>Barangay Admin</p>";

        $mail->Body = $mail_body;
        $mail->send();

        $_SESSION['success'] = "Resident and family members rejected, Email sent.";
        header("Location: resident_verifications.php");
        exit();
    } catch (Exception $e) {
        echo "<script>alert('Deletion completed but email failed: {$mail->ErrorInfo}'); window.location.href=window.location.href;</script>";
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

        .swal-wide {
            width: 600px !important;
            font-family: 'Poppins', sans-serif;
            padding: 30px;
        }

        .swal-title {
            font-size: 28px !important;
            font-weight: 600;
            color: #1a49cb;
        }

        .swal-input {
            font-size: 18px;
            padding: 12px;
            border-radius: 6px;
        }

        .swal-confirm-btn {
            background-color: #1a49cb !important;
            font-size: 16px;
            padding: 10px 24px;
            border-radius: 6px;
        }

        .swal-cancel-btn {
            background-color: #aaa !important;
            font-size: 16px;
            padding: 10px 24px;
            border-radius: 6px;
        }
    </style>
</head>

<body>
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
                <ol class="breadcrumb breadcrumb-col-red">
                    <li><a href="index.php"><i class="material-icons">home</i> Dashboard</a></li>
                    <li class="active"><i class="material-icons">visibility</i> View Resident</li>
                </ol>
            </div>

            <!-- Resident Info -->
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header bg-teal">
                            <h2>ACCOUNT OWNER INFORMATION</h2>
                        </div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $valid_id = $resident['valid_id'];
                                    $valid_id_path = "../../public/valid_id/" . $valid_id;
                                    $ext = strtolower(pathinfo($valid_id, PATHINFO_EXTENSION));

                                    echo "<p><strong>Valid ID:</strong> ";

                                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                        echo "<br><img src='$valid_id_path' alt='Valid ID' style='max-width: 100%; max-height: 300px; border: 1px solid #ccc; padding: 5px;'>";
                                    } elseif (in_array($ext, ['pdf', 'doc', 'docx'])) {
                                        echo "<a href='$valid_id_path' download>Download Valid ID</a>";
                                    } else {
                                        echo "No preview available.";
                                    }

                                    echo "</p>";
                                    ?>
                                    <p><strong>Full Name:</strong> <?= htmlspecialchars($resident['first_name'] . ' ' . $resident['middle_name'] . ' ' . $resident['last_name'] . ' ' . $resident['suffix']) ?></p>
                                    <p><strong>Gender:</strong> <?= htmlspecialchars($resident['gender']) ?></p>
                                    <p><strong>Street:</strong> <?= htmlspecialchars($resident['street']) ?></p>
                                    <p><strong>Purok:</strong> <?= htmlspecialchars($resident['purok']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Phone:</strong> <?= htmlspecialchars($resident['phone_number']) ?></p>
                                    <p><strong>Email:</strong> <?= htmlspecialchars($resident['email']) ?></p>
                                    <p><strong>Status:</strong>
                                        <span class="<?= $resident['is_approved'] ? 'badge bg-green' : 'badge bg-orange' ?>">
                                            <?= $resident['is_approved'] ? 'Verified' : 'Pending' ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Members Table -->
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="header bg-red">
                            <h2>FAMILY MEMBERS</h2>
                        </div>
                        <div class="body table-responsive">
                            <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Relationship</th>
                                        <th>Age</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $has_displayed = false;
                                    foreach ($family_members as $member):
                                        if (strtolower($member['relationship']) === 'account owner') continue;
                                        $has_displayed = true;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($member['first_name'] . ' ' . $member['middle_name'] . ' ' . $member['last_name'] . (!empty($member['suffix']) ? ' ' . $member['suffix'] : '')) ?></td>
                                            <td style="text-transform: capitalize;"><?= htmlspecialchars($member['relationship']) ?></td>
                                            <td><?= htmlspecialchars($member['age']) ?></td>
                                            <td>
                                                <span class="badge <?= $member['is_approved'] == 1 ? 'bg-green' : 'bg-orange' ?>">
                                                    <?= $member['is_approved'] == 1 ? 'Verified' : 'Pending' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn bg-red btn-sm" data-toggle="modal" data-target="#viewModal<?= $member['id'] ?>">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (!$has_displayed): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No family members found.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>

                            <!-- Modals for each family member -->
                            <?php foreach ($family_members as $member): ?>
                                <?php if (strtolower($member['relationship']) === 'account owner') continue; ?>
                                <div class="modal fade" id="viewModal<?= $member['id'] ?>" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-red">
                                                <h4 class="modal-title">Family Member Details</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong style="color: black;">Full Name:</strong> <span style="color: black;"><?= htmlspecialchars($member['first_name'] . ' ' . $member['middle_name'] . ' ' . $member['last_name'] . ' ' . $member['suffix']) ?></span></p>
                                                <p style="text-transform: capitalize;"><strong style="color: black;">Relationship:</strong> <span style="color: black;"><?= htmlspecialchars($member['relationship']) ?></span></p>
                                                <p><strong style="color: black;">Gender:</strong> <span style="color: black;"><?= htmlspecialchars($member['gender']) ?></span></p>
                                                <p><strong style="color: black;">Birthdate:</strong> <span style="color: black;"><?= htmlspecialchars($member['date_of_birth']) ?></span></p>
                                                <p><strong style="color: black;">Age:</strong> <span style="color: black;"><?= htmlspecialchars($member['age']) ?></span></p>
                                                <p><strong style="color: black;">Civil Status:</strong> <span style="color: black;"><?= htmlspecialchars($member['civil_status']) ?></span></p>
                                                <p><strong style="color: black;">Status:</strong>
                                                    <span style="color: black;">
                                                        <?php
                                                        if ($member['is_working'] == 1) {
                                                            echo 'Working';
                                                        } elseif ($member['is_working'] == 2) {
                                                            echo 'Student';
                                                        } elseif ($member['is_working'] == 3) {
                                                            echo 'None';
                                                        } else {
                                                            echo 'Unknown';
                                                        }
                                                        ?>
                                                    </span>
                                                </p>
                                                <p><strong style="color: black;">Barangay Voted:</strong> <span style="color: black;"><?= $member['is_barangay_voted'] ? 'Yes' : 'No' ?></span></p>
                                                <p><strong style="color: black;">Years in Barangay:</strong> <span style="color: black;"><?= htmlspecialchars($member['years_in_barangay']) ?></span></p>
                                                <p><strong style="color: black;">Phone Number:</strong> <span style="color: black;"><?= htmlspecialchars($member['phone_number']) ?></span></p>
                                                <p><strong style="color: black;">PhilHealth #:</strong> <span style="color: black;"><?= htmlspecialchars($member['philhealth_number']) ?></span></p>

                                                <?php if ($member['is_working'] == 2): ?>
                                                    <p><strong style="color: black;">School:</strong> <span style="color: black;"><?= htmlspecialchars($member['school']) ?></span></p>
                                                <?php elseif ($member['is_working'] == 1): ?>
                                                    <p><strong style="color: black;">Occupation:</strong> <span style="color: black;"><?= htmlspecialchars($member['occupation']) ?></span></p>
                                                <?php endif; ?>

                                                <p><strong style="color: black;">Status:</strong>
                                                    <span class="badge <?= $member['is_approved'] == 1 ? 'bg-green' : 'bg-orange' ?>" style="color: black;">
                                                        <?= $member['is_approved'] == 1 ? 'Verified' : 'Pending' ?>
                                                    </span>
                                                </p>

                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>


                            <div style="float: right !important;">
                                <form method="POST" id="approvalForm" class="d-flex flex-wrap align-items-center gap-2">
                                    <button type="submit" name="accept_all" class="btn bg-green waves-effect">
                                        <i class="fa fa-check"></i> Accept All
                                    </button>
                                    <button type="button" id="rejectAllBtn" class="btn bg-red waves-effect">
                                        <i class="fa fa-times"></i> Reject All
                                    </button>
                                    <a href="javascript:history.back()" class="btn bg-teal waves-effect">
                                        <i class="fa fa-arrow-left"></i> Back
                                    </a>
                                </form>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php if ($show_success): ?>
            Swal.fire({
                title: 'Approved!',
                text: 'All family members and the account owner have been approved.',
                icon: 'success',
                confirmButtonText: 'OK',
                customClass: {
                    popup: 'swal-wide',
                    title: 'swal-title',
                    confirmButton: 'swal-confirm-btn'
                }
            });
        <?php endif; ?>
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('rejectAllBtn').addEventListener('click', function() {
                Swal.fire({
                    title: 'Reject All?',
                    text: 'Please enter a reason for rejection. This note will be sent to the account owner.',
                    input: 'text',
                    inputPlaceholder: 'E.g., Your family member has a problem verifying...',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to enter a reason!'
                        }
                    },
                    customClass: {
                        popup: 'swal-wide',
                        title: 'swal-title',
                        input: 'swal-input',
                        confirmButton: 'swal-confirm-btn',
                        cancelButton: 'swal-cancel-btn'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const note = result.value;

                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        form.style.display = 'none';

                        const rejectAll = document.createElement('input');
                        rejectAll.type = 'hidden';
                        rejectAll.name = 'reject_all';
                        rejectAll.value = '1';

                        const noteField = document.createElement('input');
                        noteField.type = 'hidden';
                        noteField.name = 'rejection_note';
                        noteField.value = note;

                        form.appendChild(rejectAll);
                        form.appendChild(noteField);
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    </script>



</body>

</html>