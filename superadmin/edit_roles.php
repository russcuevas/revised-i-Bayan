<?php
// session
session_start();
if (!isset($_SESSION['superadmin_id'])) {
    header("Location: login.php");
    exit();
}

// database connection
include '../database/connection.php';

// Get the user ID and type from the URL
$id = $_GET['id'];
$type = $_GET['type'];

// Determine the table to fetch user data from based on the type
if ($type == 'admin') {
    $query = "SELECT * FROM tbl_admin WHERE id = :id";
} elseif ($type == 'superadmin') {
    $query = "SELECT * FROM tbl_superadmin WHERE id = :id";
} else {
    $query = "SELECT * FROM tbl_residents WHERE id = :id";
}

// Fetch user data
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

// If the form is submitted, update the user data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $contact = $_POST['contact'];

    // Update the user data in the database
    if ($type == 'admin') {
        $update_query = "UPDATE tbl_admin SET fullname = :fullname, contact_number = :contact WHERE id = :id";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bindParam(':fullname', $fullname);
        $stmt_update->bindParam(':contact', $contact);
        $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
    } elseif ($type == 'superadmin') {
        $update_query = "UPDATE tbl_superadmin SET first_name = :fullname, phone_number = :contact WHERE id = :id";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bindParam(':fullname', $fullname);
        $stmt_update->bindParam(':contact', $contact);
        $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
    } else {
        // Split fullname
        $name_parts = explode(' ', $fullname);
        $first_name = $name_parts[0];
        $middle_name = $name_parts[1] ?? '';
        $last_name = $name_parts[2] ?? '';
        $suffix = $name_parts[3] ?? '';

        $update_query = "UPDATE tbl_residents SET first_name = :first_name, middle_name = :middle_name, last_name = :last_name, suffix = :suffix, phone_number = :contact WHERE id = :id";
        $stmt_update = $conn->prepare($update_query);
        $stmt_update->bindParam(':first_name', $first_name);
        $stmt_update->bindParam(':middle_name', $middle_name);
        $stmt_update->bindParam(':last_name', $last_name);
        $stmt_update->bindParam(':suffix', $suffix);
        $stmt_update->bindParam(':contact', $contact);
        $stmt_update->bindParam(':id', $id, PDO::PARAM_INT);
    }

    if ($stmt_update->execute()) {
        $_SESSION['success'] = "Updated users successfully!";
        header("Location: system_permissions.php");
        exit();
    } else {
        echo "Failed to update user.";
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
    <link rel="icon" href="img/logo.png" type="image/x-icon">
    <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="plugins/font-awesome/css/font-awesome.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2>Edit Users</h2>
        <?php if ($user_data) : ?>
            <form method="POST" action="edit_roles.php?id=<?= $id ?>&type=<?= $type ?>">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?=
                                                                                                    ($type == 'admin' ? $user_data['fullname'] : ($type == 'superadmin' ? $user_data['first_name'] . " " . $user_data['last_name'] :
                                                                                                        $user_data['first_name'] . " " . $user_data['middle_name'] . " " . $user_data['last_name'] . " " . $user_data['suffix']))
                                                                                                    ?>" required>
                </div>
                <div class="form-group">
                    <label for="contact">Contact Number</label>
                    <input type="text" class="form-control" id="contact" name="contact" value="<?=
                                                                                                ($type == 'admin' ? $user_data['contact_number'] : ($type == 'superadmin' ? $user_data['phone_number'] :
                                                                                                    $user_data['phone_number']))
                                                                                                ?>" required>
                </div>
                <button type="button" class="btn btn-secondary" onclick="window.location.href = 'system_permissions.php'">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>

            </form>
        <?php else: ?>
            <p>User not found.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.js"></script>
</body>

</html>