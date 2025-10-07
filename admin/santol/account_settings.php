<?php
session_start();

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

include '../../database/connection.php';

$admin_id = $_SESSION[$session_key];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $email) {
        $query = "UPDATE tbl_admin SET username = ?, email = ?";
        $params = [$username, $email];

        if (!empty($password)) {
            $hashed_password = sha1($password);  // Using SHA1 (not recommended)
            $query .= ", password = ?";
            $params[] = $hashed_password;
        }


        $query .= " WHERE id = ?";
        $params[] = $admin_id;

        $updateStmt = $conn->prepare($query);
        $success = $updateStmt->execute($params);

        if ($success) {
            $_SESSION['success'] = "Profile updated successfully.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $error = "Failed to update profile.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

// Fetch admin profile info
$stmt = $conn->prepare("SELECT username, email FROM tbl_admin WHERE id = ?");
$stmt->execute([$admin_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="../plugins/sweetalert/sweetalert.css" rel="stylesheet" />
</head>

<body>

    <div class="container mt-5" style="max-width: 600px; border: 2px solid blue; padding: 20px;">
        <h2 class="mb-4 text-primary">Acccount Settings</h2>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Username:</strong>
            <div><?= htmlspecialchars($user['username']) ?></div>
        </div>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Email:</strong>
            <div><?= htmlspecialchars($user['email']) ?></div>
        </div>

        <div class="mb-3 d-flex">
            <strong style="width: 150px;">Password:</strong>
            <div>••••••••</div>
        </div>

        <button type="button" class="btn btn-secondary" onclick="window.location.href = 'index.php'">
            Go back
        </button>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            Edit Profile
        </button>
    </div>

    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form class="modal-content" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username *</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password (Leave blank to keep current)</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>

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

    <script>
        <?php if (!empty($error)) : ?>
            var editModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
            editModal.show();
        <?php endif; ?>
    </script>

</body>

</html>