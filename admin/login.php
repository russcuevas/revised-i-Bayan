<?php
session_start();


foreach ($_SESSION as $key => $value) {
    if (strpos($key, 'admin_id_') === 0) {
        $barangay = str_replace('admin_id_', '', $key);
        header("Location: $barangay/index.php");
        exit();
    }
}

include '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashed_password = sha1($password);

    $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE username = :username AND password = :password");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashed_password
    ]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {

        $update_status_stmt = $conn->prepare("UPDATE tbl_admin SET status = 'online' WHERE id = :id");
        $update_status_stmt->execute([':id' => $admin['id']]);

        $barangay_id = $admin['barangay_id'];
        $stmt_barangay = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = :id");
        $stmt_barangay->execute([':id' => $barangay_id]);
        $barangay = $stmt_barangay->fetch(PDO::FETCH_ASSOC);
        $barangay_name_raw = $barangay['barangay_name'] ?? 'unknown';

        $barangay_key = strtolower(str_replace(' ', '_', $barangay_name_raw));

        $_SESSION["admin_id_$barangay_key"] = $admin['id'];
        $_SESSION["admin_name_$barangay_key"] = $admin['fullname'];
        $_SESSION["admin_position_$barangay_key"] = $admin['position'];
        $_SESSION["barangay_id_$barangay_key"] = $admin['barangay_id'];
        $_SESSION["barangay_name_$barangay_key"] = $barangay_name_raw;

        $insert_log_stmt = $conn->prepare("INSERT INTO tbl_system_logs_admin (admin_id, logged_in) VALUES (:admin_id, NOW())");
        $insert_log_stmt->execute([':admin_id' => $admin['id']]);

        $log_id = $conn->lastInsertId();
        $_SESSION["log_id_admin_$barangay_key"] = $log_id;

        $redirect_navigate = [
            28 => 'i',
            29 => 'ii',
            30 => 'ii-a',
            31 => 'iii',
            32 => 'iv',
            35 => 'bayorbor',
            36 => 'bubuyan',
            37 => 'calingatan',
            38 => 'kinalaglagan',
            39 => 'loob',
            40 => 'lumanglipa',
            41 => 'upa',
            42 => 'manggahan',
            43 => 'nangkaan',
            44 => 'san-sebastian',
            45 => 'santol',
        ];

        $folder = $redirect_navigate[$barangay_id] ?? null;

        if ($folder) {
            header("Location: $folder/index.php?success");
            exit();
        } else {
            $_SESSION['error'] = "Barangay not configured.";
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Invalid credentials.";
        header("Location: login.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>iBayan</title>
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="plugins/sweetalert/sweetalert.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/login.css">
</head>

<body>

    <div class="container text-center login-container">
        <img src="img/logo.png" alt="Municipality Seal" class="login-seal">
        <h3 class="mt-3 mb-3" style="font-weight: 900;">Welcome admin!</h3>

        <form method="POST" action="" class="needs-validation" novalidate>
            <div class="form-group position-relative mb-3 text-start">
                <input type="text" class="form-control rounded-pill px-4" style="font-weight: 900" id="username" name="username" placeholder="Username" required>
                <div class="invalid-feedback ms-2">
                    Please enter your username.
                </div>
            </div>

            <div class="form-group position-relative mb-3 text-start">
                <input type="password" class="form-control rounded-pill px-4" style="font-weight: 900" id="password" name="password" placeholder="Password" required>
                <i class="bi bi-eye-slash position-absolute" id="togglePassword"
                    style="top: 50%; right: 10px; transform: translateY(-50%); cursor: pointer;"></i>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button class="btn btn-primary w-100 rounded-pill mt-3 mb-3" style="font-weight: 900;" name="" type="submit">Login ➜</button>
            <a href="../home.php" class="btn btn-secondary w-100 rounded-pill mb-3" style="font-weight: 900;">Go back</a>

            <div class="d-flex justify-content-center align-items-center">
                <div>
                    <small class="small text-center">
                        Don't have an account yet?
                        <a class="fw-bold text-primary" href="#" data-bs-toggle="modal" data-bs-target="#contactAdminModal" style="text-decoration: none; color: #B6771D !important">How to register</a>
                    </small>
                </div>
            </div>

            <div class="d-flex justify-content-center align-items-center">
                <div>
                    <small class="small text-center">
                        Forgot Password?
                        <a class="fw-bold text-primary" href="#" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" style="text-decoration: none; color: #B6771D !important">Click here to read</a>
                </div>
            </div>
        </form>
    </div>

    <!-- CONTACT SUPERADMIN MODAL -->
    <div class="modal fade" id="contactAdminModal" tabindex="-1" aria-labelledby="contactAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-3">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="contactAdminModalLabel">Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="text-align: left;">To create a new account, please contact the Super Admin using the following details:</p>
                    <ol class="fw-semibold text-start">
                        <li><strong>Email:</strong> admin@example.com</li>
                        <li><strong>Phone:</strong> +63 912 345 6789</li>
                    </ol>
                    <p>Alternatively, you may visit the admin office <br> during working hours to request an account.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
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
    
    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle("bi-eye");
            this.classList.toggle("bi-eye-slash");
        });
    </script>


</body>

</html>