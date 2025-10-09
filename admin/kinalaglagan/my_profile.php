<?php
// Start session
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

// If not logged in, redirect to login
if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

// Database connection
include '../../database/connection.php';

// Get admin ID from session
$admin_id = $_SESSION[$session_key];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $position = trim($_POST['position'] ?? '');


    if ($fullname && ($gender === 'Male' || $gender === 'Female') && $contact_number && $position) {
        $updateStmt = $conn->prepare("UPDATE tbl_admin SET fullname = ?, gender = ?, contact_number = ?, position = ? WHERE id = ?");
        $success = $updateStmt->execute([$fullname, $gender, $contact_number, $position, $admin_id]);

        if ($success) {
            $_SESSION['success'] = "Profile updated successfully";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error = "Failed to update profile. Please try again.";
        }
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}

// Fetch admin profile info from DB
$stmt = $conn->prepare("SELECT fullname, gender, contact_number, position, status FROM tbl_admin WHERE id = ?");
$stmt->execute([$admin_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // If no user found, log out or show error
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>My Profile</title>
    <!-- Bootstrap CSS (Bootstrap 5 CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />

</head>

<body>

    <div class="container mt-5" style="max-width: 600px; border: 2px solid blue; padding: 20px;">
        <h2 class="mb-4 text-primary">My Profile</h2>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Full Name:</strong>
            <div><?= htmlspecialchars($user['fullname']) ?></div>
        </div>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Gender:</strong>
            <div><?= htmlspecialchars($user['gender']) ?></div>
        </div>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Contact Number:</strong>
            <div><?= htmlspecialchars($user['contact_number']) ?></div>
        </div>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Position:</strong>
            <div style="text-transform: capitalize;"><?= htmlspecialchars($user['position']) ?></div>
        </div>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Status:</strong>
            <div style="text-transform: capitalize; color: green"><?= htmlspecialchars($user['status']) ?></div>
        </div>

        <button type="button" class="btn btn-secondary" onclick="window.location.href = 'index.php'">
            Go back
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            Edit Profile
        </button>

    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="fullname" class="form-label">Full Name *</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" value="<?= htmlspecialchars($user['fullname']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender *</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="Male" <?= $user['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                            <option value="Female" <?= $user['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="contact_number" class="form-label">Contact Number *</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="position" class="form-label">Position *</label>
                        <input readonly style="background-color: gray; color: white;" type="text" class="form-control" id="position" name="position" value="<?= htmlspecialchars($user['position']) ?>" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS (Popper and Bootstrap JS) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
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



</body>

</html>