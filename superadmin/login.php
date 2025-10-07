<?php
session_start();
if (isset($_SESSION['superadmin_id'])) {
    header("Location: index.php");
    exit();
}
// database connection
include '../database/connection.php';


// login functions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $hashed_password = sha1($password);

    $stmt = $conn->prepare("SELECT * FROM tbl_superadmin WHERE username = :username AND password = :password");
    $stmt->execute([
        ':username' => $username,
        ':password' => $hashed_password
    ]);

    $superadmin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($superadmin) {
        $_SESSION['superadmin_id'] = $superadmin['id'];
        $_SESSION['superadmin_name'] = $superadmin['first_name'] . ' ' . $superadmin['last_name'];

        // Insert login log with logged_out = NULL
        $stmt_log = $conn->prepare("INSERT INTO tbl_system_logs_superadmin (superadmin_id, logged_in, logged_out) VALUES (:superadmin_id, NOW(), NULL)");
        $stmt_log->execute([':superadmin_id' => $superadmin['id']]);

        // Store the log ID in session to update logged_out later
        $_SESSION['log_id'] = $conn->lastInsertId();

        header("Location: index.php?success");
        exit();
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
    <link rel="stylesheet" href="css/login.css">
    <link href="plugins/sweetalert/sweetalert.css" rel="stylesheet" />

</head>

<body>

    <div class="container text-center login-container">
        <img src="img/logo.png" alt="Municipality Seal" class="login-seal">
        <h3 class="mt-3 mb-3" style="font-weight: 900;">Welcome superadmin!</h3>

        <form action="" method="POST" class="needs-validation" novalidate>
            <div class="form-group position-relative mb-3 text-start">
                <input type="text" class="form-control rounded-pill px-4" name="username" style="font-weight: 900" id="username" placeholder="Username" required>
                <div class="invalid-feedback ms-2">
                    Please enter your username.
                </div>
            </div>

            <div class="form-group position-relative mb-3 text-start">
                <input type="password" class="form-control rounded-pill px-4" name="password" style="font-weight: 900" id="password" placeholder="Password" required>
                <div class="invalid-feedback ms-2">
                    Please enter your password.
                </div>
            </div>
            <button class="btn btn-primary w-100 rounded-pill mt-3 mb-3" style="font-weight: 900;" name="" type="submit">Login ➜</button>
            <a href="../choose_type.php" class="btn btn-secondary w-100 rounded-pill mb-3" style="font-weight: 900;">Go back</a>

        </form>
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



</body>

</html>