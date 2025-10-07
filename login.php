<?php
session_start();

foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'resident_id_') === 0) {
        $barangay = str_replace('resident_id_', '', $key);
        header("Location: dashboard/$barangay/index.php");
        exit();
    }
}

include 'database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = sha1($password);

    $stmt = $conn->prepare("SELECT * FROM tbl_residents WHERE username = :username AND password = :password");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashed_password
    ]);

    $resident = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resident) {
        // Set resident as online
        $update_status = $conn->prepare("UPDATE tbl_residents SET is_online = 'online' WHERE id = ?");
        $update_status->execute([$resident['id']]);

        // Get barangay info
        $barangay_id = $resident['barangay_address'];
        $stmt_b = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
        $stmt_b->execute([$barangay_id]);
        $barangay = $stmt_b->fetch(PDO::FETCH_ASSOC);

        if ($barangay) {
            $barangay_name_raw = $barangay['barangay_name'];
            $barangay_key = strtolower(str_replace([' ', '-', '/'], '_', $barangay_name_raw));

            $_SESSION["resident_id_$barangay_key"] = $resident['id'];
            $_SESSION["resident_name_$barangay_key"] = $resident['first_name'] . ' ' . $resident['last_name'];
            $_SESSION["is_approved_$barangay_key"] = $resident['is_approved'];

            $_SESSION["barangay_id_$barangay_key"] = $barangay_id;
            $_SESSION["barangay_name_$barangay_key"] = $barangay_name_raw;

            $insert_activity = $conn->prepare("INSERT INTO tbl_activity_logs (resident_id, action, barangay_id, created_at) VALUES (?, ?, ?, NOW())");
            $insert_activity->execute([
                $resident['id'],
                'Logged in to the system',
                $barangay_id
            ]);

            $insert_log = $conn->prepare("INSERT INTO tbl_system_logs_residents (resident_id, logged_in) VALUES (?, NOW())");
            $insert_log->execute([$resident['id']]);
            $log_id = $conn->lastInsertId();

            // Store log ID in session for logout update
            $_SESSION["log_id_resident_$barangay_key"] = $log_id;

            $redirect_path = "dashboard/$barangay_key/index.php";
            if (file_exists($redirect_path)) {
                header("Location: $redirect_path?success");
                exit();
            } else {
                $_SESSION['error'] = "Barangay dashboard not found.";
            }
        } else {
            $_SESSION['error'] = "Barangay not found.";
        }
    } else {
        $_SESSION['error'] = "Invalid credentials";
    }

    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iBayan</title>
    <link rel="icon" href="images/logo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/sweetalert.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <div class="container text-center login-container">
        <img src="images/logo.png" alt="Municipality Seal" class="login-seal">
        <h3 class="mt-3 mb-3" style="font-weight: 900;">Welcome back!</h3>

        <form class="needs-validation" action="" method="POST" novalidate>
            <div class="form-group position-relative mb-3 text-start">
                <input type="text" class="form-control rounded-pill px-4" style="font-weight: 900" id="username" name="username" placeholder="Username" required>
                <div class="invalid-feedback ms-2">
                    Please enter your username.
                </div>
            </div>

            <div class="form-group position-relative mb-3 text-start">
                <input type="password" class="form-control rounded-pill px-4" style="font-weight: 900" id="password" name="password" placeholder="Password" required>
                <div class="invalid-feedback ms-2">
                    Please enter your password.
                </div>
            </div>
            <button class="btn btn-primary w-100 rounded-pill mt-3 mb-3" style="font-weight: 900;" name="" type="submit">Login ➜</button>
            <a href="home.php" class="btn btn-secondary w-100 rounded-pill mb-3" style="font-weight: 900;">Go back</a>

            <div class="d-flex justify-content-center align-items-center">
                <div>
                    <small class="small text-center">
                        Don't have an account yet?
                        <a class="fw-bold text-primary" href="register.php" style="text-decoration: none;">Register</a> </small>
                </div>
            </div>
            <div class="d-flex justify-content-center align-items-center">
                <div>
                    <small class="small text-center">
                        Forgot Password?
                        <a class="fw-bold text-primary" href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" style="text-decoration: none;">Click here to read</a>
                </div>
            </div>
        </form>
    </div>

    <!-- MODAL FORGOT PASSWORD -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="forgotPasswordModalLabel">How to Forgot Your Password?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ol class="fw-semibold text-start">
                        <li>Visit your Barangay Hall for verification.</li>
                        <li>Bring a valid ID for identification.</li>
                        <li>Request a password reset form from the staff.</li>
                        <li>Wait for confirmation the staff to change your password</li>
                        <li>Use the temporary password and change it immediately.</li>
                    </ol>
                    <p class="mt-3 mb-0 text-muted">For more help, contact the iBayan support desk at your LGU office.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>



    <div class="relative flex items-center justify-center d-md-none">
        <img class="mt-0 img-fluid" src="images/city-mobile.png" alt="" style="max-width: 100%; height: auto; color: transparent;">
    </div>
    <div class="relative flex items-center justify-center d-none d-md-block">
        <img class="mt-0 img-fluid" src="images/city-desktop.png" alt="" style="max-width: 100%; height: auto; color: transparent;">
    </div>

    <?php include 'components/footer.php' ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <script src="assets/js/sweetalert.js"></script>
    <script src="assets/js/time.js"></script>

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
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>



</body>

</html>