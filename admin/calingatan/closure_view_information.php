<?php
session_start();
include '../../database/connection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

$barangay_name_key = "barangay_name_$barangay";
$admin_name_key = "admin_name_$barangay";
$admin_position_key = "admin_position_$barangay";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid request.";
    exit();
}

$closure_id = $_GET['id'];
$stmt = $conn->prepare("
    SELECT o.*, b.barangay_name, t.name AS business_type_name
    FROM tbl_closure AS o
    LEFT JOIN tbl_barangay AS b ON o.for_barangay = b.id
    LEFT JOIN tbl_business_trade AS t ON o.business_trade = t.id
    WHERE o.id = ?
");
$stmt->execute([$closure_id]);
$closure = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$closure) {
    echo "closure not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'], $_POST['closure_id'])) {
    $new_status = $_POST['status'];
    $closure_id = $_POST['closure_id'];

    $update = $conn->prepare("UPDATE tbl_closure SET status = ?, updated_at = NOW() WHERE id = ?");
    $updated = $update->execute([$new_status, $closure_id]);

    if ($updated && $new_status === 'Claimed') {
        // For the "Claimed" status, insert the data into tbl_closure_claimed
        $stmt = $conn->prepare("SELECT * FROM tbl_closure WHERE id = ?");
        $stmt->execute([$closure_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $insert = $conn->prepare("
                INSERT INTO tbl_closure_claimed (
                    resident_id, document_number, picked_up_by, relationship, certificate_type,
                    purpose, business_name, business_trade, business_address, owner_name,
                    owner_purok, email, contact, for_barangay, valid_id,
                    birth_certificate, is_resident, total_amount, status, created_at, updated_at
                ) VALUES (
                    :resident_id, :document_number, :picked_up_by, :relationship, :certificate_type,
                    :purpose, :business_name, :business_trade, :business_address, :owner_name,
                    :owner_purok, :email, :contact, :for_barangay, :valid_id,
                    :birth_certificate, :is_resident, :total_amount, :status, :created_at, :updated_at
                )
            ");

            $insert->execute([
                ':resident_id' => $data['resident_id'],
                ':document_number' => $data['document_number'],
                ':picked_up_by' => $data['picked_up_by'],
                ':relationship' => $data['relationship'],
                ':certificate_type' => $data['certificate_type'],
                ':purpose' => $data['purpose'],
                ':business_name' => $data['business_name'],
                ':business_trade' => $data['business_trade'],
                ':business_address' => $data['business_address'],
                ':owner_name' => $data['owner_name'],
                ':owner_purok' => $data['owner_purok'],
                ':email' => $data['email'],
                ':contact' => $data['contact'],
                ':for_barangay' => $data['for_barangay'],
                ':valid_id' => $data['valid_id'],
                ':birth_certificate' => $data['birth_certificate'],
                ':is_resident' => $data['is_resident'],
                ':total_amount' => $data['total_amount'],
                ':status' => $new_status,
                ':created_at' => $data['created_at'],
                ':updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    } elseif ($updated && $new_status === 'To Pick Up') {
        $stmt = $conn->prepare("SELECT * FROM tbl_closure WHERE id = ?");
        $stmt->execute([$closure_id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            $resident_stmt = $conn->prepare("SELECT phone_number, email, first_name FROM tbl_residents WHERE id = ?");
            $resident_stmt->execute([$data['resident_id']]);
            $resident = $resident_stmt->fetch(PDO::FETCH_ASSOC);

            if ($resident) {
                $name = ucfirst(strtolower($resident['first_name']));
                $amount = number_format($data['total_amount'], 2);
                $certificate_type = ucfirst(strtolower($data['certificate_type']));

                // ---- SEND EMAIL FIRST ----
                if (!empty($resident['email'])) {
                    $email = $resident['email'];
                    $fullname = $name;



                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'gmanagementtt111@gmail.com';
                        $mail->Password = 'skbtosbmkiffrajr';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('gsu-erequest@gmail.com', 'iBayan');
                        $mail->addAddress($email, $fullname);

                        $mail->isHTML(true);
                        $mail->Subject = 'Your ' . $certificate_type . ' is Ready for Pickup';

                        $mail_body = "<p>Dear {$fullname},</p>
                                  <p>Your {$certificate_type} is now ready for pickup.</p>
                                  <p>Please bring ₱{$amount} when you claim your document.</p>
                                  <p>Thank you,<br>Barangay Admin</p>";

                        $mail->Body = $mail_body;
                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Email failed to send: {$mail->ErrorInfo}");
                    }
                }

                // ---- THEN SEND SMS ----
                if (!empty($resident['phone_number'])) {
                    $apikey = 'b2a42d09e5cd42585fcc90bf1eeff24e';
                    $number = $resident['phone_number'];
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
        }
    }


    // Success message and redirect
    $_SESSION['success'] = "Status updated successfully.";
    header("Location: closure_view_information.php?id=" . $closure_id);
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
                            Closure Details -
                            <span class="badge bg-blue"><?= ucfirst($closure['status'] ?? 'Pending') ?></span>
                        </h4>

                        <div class="row">
                            <div class="col-md-6 mb-3"><strong>Document Number:</strong><br><?= htmlspecialchars($closure['document_number']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Barangay:</strong><span style="text-transform: capitalize;"><br><?= htmlspecialchars($closure['barangay_name']) ?></span></div>

                            <div class="col-md-6 mb-3"><strong>Certificate Type:</strong><br><?= htmlspecialchars($closure['certificate_type']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Purpose:</strong><br><?= htmlspecialchars($closure['purpose']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Business Name:</strong><br><?= htmlspecialchars($closure['business_name']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Business Type:</strong><br><?= htmlspecialchars($closure['business_type_name']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Business Address:</strong><br><?= htmlspecialchars($closure['business_address']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Owner Name:</strong><br><?= htmlspecialchars($closure['owner_name']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Purok:</strong><br><?= htmlspecialchars($closure['owner_purok']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Email:</strong><br><?= htmlspecialchars($closure['email']) ?></div>
                            <div class="col-md-6 mb-3"><strong>Contact:</strong><br><?= htmlspecialchars($closure['contact']) ?></div>

                            <div class="col-md-6 mb-3"><strong>Picked Up By:</strong><br><?= htmlspecialchars($closure['picked_up_by']) ?> - <span style="text-transform: capitalize;"><?= htmlspecialchars($closure['relationship']) ?></span></div>

                            <div class="col-md-6 mb-3"><strong>Resident:</strong><br><?= htmlspecialchars($closure['is_resident']) ?></div>

                            <div class="col-md-6 mb-3">
                                <strong>Total Amount:</strong><br>
                                <span class="text-success">₱<?= number_format($closure['total_amount'], 2) ?>
                                    <?php if ($closure['status'] === 'Claimed'): ?>
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
                                <?php if (!empty($closure['valid_id'])): ?>
                                    <a class="btn btn-sm bg-red" href="../../public/request/valid_id/<?= htmlspecialchars($closure['valid_id']) ?>" target="_blank">
                                        <i class="fa fa-eye"></i> View Valid ID
                                    </a>
                                <?php else: ?>
                                    <span class="text-danger">Not Uploaded</span>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Birth Certificate:</strong><br>
                                <?php if (!empty($closure['birth_certificate'])): ?>
                                    <a class="btn btn-sm bg-red" href="../../public/request/birth_certificate/<?= htmlspecialchars($closure['birth_certificate']) ?>" target="_blank">
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
                            <input type="hidden" name="closure_id" value="<?= $closure['id'] ?>">
                            <div class="form-group">
                                <label><strong>Change Status:</strong></label>
                                <select name="status" class="form-control" style="border: 2px solid black;" required>
                                    <?php if ($closure['status'] === 'Pending'): ?>
                                        <option disabled selected>Pending</option>
                                        <option value="To Pick Up">To Pick Up</option>
                                    <?php elseif ($closure['status'] === 'To Pick Up'): ?>
                                        <option disabled selected>To Pick Up</option>
                                        <option value="Claimed">Claimed</option>
                                    <?php else: ?>
                                        <option disabled selected><?= htmlspecialchars($closure['status']) ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <?php if ($closure['status'] !== 'Claimed'): ?>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <button type="submit" class="btn btn-primary btn-block w-100">
                                            <i class="fa fa-check"></i> Update Status
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <a href="closure_reject_request.php?closure_id=<?= $closure['id'] ?>"
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
                            <a href="certificate_closure.php" class="btn bg-red"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <?php include('footer.php')?>    

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
    <script>
    let chatLoaded = false;

    $('#openChatBtn').on('click', function() {
    $('#chatPopup').modal('show');

    if (!chatLoaded) {
        $('#chatContent').html(`
        <iframe src="live_chat.php" 
                style="width:100%; height:100%; border:none;"></iframe>
        `);
        chatLoaded = true;
    }
    });
    </script>
</body>

</html>