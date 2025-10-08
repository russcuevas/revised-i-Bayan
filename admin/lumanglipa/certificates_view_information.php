<?php
session_start();
include '../../database/connection.php';

$barangay = basename(__DIR__); // e.g., "calingatan"
$session_key = "admin_id_$barangay";

// Check if admin is logged in
if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// Session variables for admin info (optional use)
$barangay_name_key = "barangay_name_$barangay";
$admin_name_key = "admin_name_$barangay";
$admin_position_key = "admin_position_$barangay";

// Validate certificate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$certificate_id = $_GET['id'];

// Fetch certificate
$stmt = $conn->prepare("SELECT * FROM tbl_certificates WHERE id = ?");
$stmt->execute([$certificate_id]);
$certificate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$certificate) {
    echo "Certificate not found.";
    exit();
}

// Handle POST request to update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['certificate_id'])) {
    $new_status = $_POST['status'];
    $certificate_id = $_POST['certificate_id'];

    // Update certificate status
    $stmt = $conn->prepare("UPDATE tbl_certificates SET status = ? WHERE id = ?");
    if ($stmt->execute([$new_status, $certificate_id])) {

        // Fetch certificate details
        $cert_stmt = $conn->prepare("SELECT * FROM tbl_certificates WHERE id = ?");
        $cert_stmt->execute([$certificate_id]);
        $certificate = $cert_stmt->fetch(PDO::FETCH_ASSOC);

        if (!$certificate) {
            $_SESSION['error'] = "Certificate not found.";
            header("Location: certificates_view_information.php?id=" . $certificate_id);
            exit();
        }

        if ($new_status === 'To Pick Up') {
            $resident_stmt = $conn->prepare("SELECT phone_number, first_name FROM tbl_residents WHERE id = ?");
            $resident_stmt->execute([$certificate['resident_id']]);
            $resident = $resident_stmt->fetch(PDO::FETCH_ASSOC);

            if ($resident && !empty($resident['phone_number'])) {
                $apikey = 'b2a42d09e5cd42585fcc90bf1eeff24e';
                $number = $resident['phone_number'];
                $name = ucfirst(strtolower($resident['first_name']));
                $amount = number_format($certificate['total_amount'], 2);
                $certificate_type = ucfirst(strtolower($certificate['certificate_type']));
                $message = "Hi $name, your $certificate_type is ready for pickup. Please bring ₱$amount. Thank you!";
                $sendername = 'BPTOCEANUS';

                $ch = curl_init();
                $parameters = [
                    'apikey' => $apikey,
                    'number' => $number,
                    'message' => $message,
                    'sendername' => $sendername
                ];

                curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                $output = curl_exec($ch);
                curl_close($ch);
            }
        }


        if ($new_status === 'Claimed') {
            $document_number = strtoupper(uniqid('DOC'));

            $barangay = $certificate['barangay']; // Make sure this is set correctly
            $barangay_stmt = $conn->prepare("SELECT id FROM tbl_barangay WHERE LOWER(REPLACE(barangay_name, ' ', '')) = ?");
            $barangay_stmt->execute([strtolower(str_replace(' ', '', $barangay))]);
            $barangay_data = $barangay_stmt->fetch(PDO::FETCH_ASSOC);

            if (!$barangay_data) {
                $_SESSION['error'] = "Barangay not found in tbl_barangay.";
                header("Location: certificates_view_information.php?id=" . $certificate_id);
                exit();
            }

            $for_barangay_id = $barangay_data['id'];
            $insert = $conn->prepare("INSERT INTO tbl_certificates_claimed (
                resident_id, purok, document_number, picked_up_by, relationship, 
                certificate_type, purpose, fullname, email, gender, contact, 
                valid_id, total_amount_paid, for_barangay, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $insert->execute([
                $certificate['resident_id'],
                $certificate['purok'],
                $document_number,
                $certificate['picked_up_by'],
                $certificate['relationship'],
                $certificate['certificate_type'],
                $certificate['purpose'],
                $certificate['fullname'],
                $certificate['email'],
                $certificate['gender'],
                $certificate['contact'],
                $certificate['valid_id'],
                $certificate['total_amount'],
                $for_barangay_id,
                'Claimed'
            ]);
        }

        $_SESSION['success'] = "Certificate status updated successfully!";
    } else {
        $_SESSION['error'] = "Failed to update certificate status.";
    }

    header("Location: certificates_view_information.php?id=" . $certificate_id);
    exit();
}
?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>iBayan</title>
    <link rel="icon" href="../img/logo.png" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" crossorigin="anonymous" />
    <link href="../plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../plugins/node-waves/waves.css" rel="stylesheet" />
    <link href="../plugins/animate-css/animate.css" rel="stylesheet" />
    <link href="../plugins/morrisjs/morris.css" rel="stylesheet" />
    <link href="../css/style.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <link href="../css/themes/all-themes.css" rel="stylesheet" />
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />
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
        <?php include('left_sidebar.php'); ?>
        <?php include('right_sidebar.php'); ?>
    </section>
    <section class="content">
        <div class="container-fluid" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
            <div class="row clearfix" style="width: 100%; max-width: 800px;">
                <div class="card shadow" style="border-radius: 12px;">
                    <div class="body p-4">
                        <h4 class="text-left mb-4" style="font-weight: 800; color: #B6771D;">
                            Certificate Details -
                            <span class="badge bg-blue"><?= ucfirst($certificate['status'] ?? 'Pending') ?></span>
                        </h4>

                        <div class="row">
                            <div class="col-md-6 mb-3"><strong>Full Name:</strong><br><?= htmlspecialchars($certificate['fullname']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Purok:</strong><br><?= htmlspecialchars($certificate['purok']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Gender:</strong><br><?= htmlspecialchars($certificate['gender']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Email:</strong><br><?= htmlspecialchars($certificate['email']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Contact:</strong><br><?= htmlspecialchars($certificate['contact']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Certificate Type:</strong><br><?= htmlspecialchars($certificate['certificate_type']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Purpose:</strong><br><?= htmlspecialchars($certificate['purpose']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Resident:</strong><br><?= htmlspecialchars($certificate['is_resident']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Picked Up By:</strong><br><?= htmlspecialchars($certificate['picked_up_by']) ?> - <span style="text-transform: capitalize;"><?= htmlspecialchars($certificate['relationship']) ?></span></div>


                            <div class="col-md-6 mb-3"><strong>Total Amount:</strong><br>
                                <span class="text-success">
                                    ₱<?= number_format($certificate['total_amount'], 2) ?>
                                    <?php if ($certificate['status'] === 'Claimed'): ?>
                                        / Paid
                                    <?php endif; ?>
                                </span>

                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3" style="color: #B6771D;"><strong>Uploaded Documents</strong></h5>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Valid ID:</strong><br>
                                <?php if (!empty($certificate['valid_id'])): ?>
                                    <a class="btn btn-sm bg-red" href="../../public/request/valid_id/<?= htmlspecialchars($certificate['valid_id']) ?>" target="_blank">
                                        <i class="fa fa-eye"></i> View Valid ID
                                    </a>
                                <?php else: ?>
                                    <span class="text-danger">Not Uploaded</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Birth Certificate:</strong><br>
                                <?php if (!empty($certificate['birth_certificate'])): ?>
                                    <a class="btn btn-sm bg-red" href="../../public/request/birth_certificate/<?= htmlspecialchars($certificate['birth_certificate']) ?>" target="_blank">
                                        <i class="fa fa-eye"></i> View Birth Certificate
                                    </a>
                                <?php else: ?>
                                    <span class="text-danger">Not Uploaded</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Update Status -->
                        <form action="" method="POST" class="mb-3">
                            <input type="hidden" name="certificate_id" value="<?= $certificate['id'] ?>">
                            <div class="form-group">
                                <label><strong>Change Status:</strong></label>
                                <select name="status" class="form-control" style="border: 2px solid black;" required>
                                    <?php if ($certificate['status'] === 'Pending'): ?>
                                        <option disabled selected>Pending</option>
                                        <option value="To Pick Up">To Pick Up</option>
                                    <?php elseif ($certificate['status'] === 'To Pick Up'): ?>
                                        <option disabled selected>To Pick Up</option>
                                        <option value="Claimed">Claimed</option>
                                    <?php else: ?>
                                        <option disabled selected><?= htmlspecialchars($certificate['status']) ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <?php if ($certificate['status'] !== 'Claimed'): ?>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-block w-100">
                                            <i class="fa fa-check"></i> Update Status
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="certificates_reject_request.php?certificate_id=<?= $certificate['id'] ?>"
                                            class="btn btn-danger btn-block w-100"
                                            onclick="return confirm('Are you sure you want to reject this request?');">
                                            <i class="fa fa-ban"></i> Reject Request
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                        </form>


                        <br>

                        <div class="text-right mt-4">
                            <a href="certificate_issuance.php" class="btn bg-red"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>



    <script src="../plugins/jquery/jquery.min.js"></script>
    <script src="../plugins/bootstrap/js/bootstrap.js"></script>
    <script src="../plugins/jquery-validation/jquery.validate.js"></script>
    <script src="../js/pages/forms/form-validation.js"></script>
    <script src="../plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="../plugins/node-waves/waves.js"></script>
    <script src="../plugins/sweetalert/sweetalert.min.js"></script>
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
    <script src="../js/admin.js"></script>
</body>

</html>