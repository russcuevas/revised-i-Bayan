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

// Check if type is set to delete a valid user
if ($id && $type) {
    try {
        $conn->beginTransaction();

        if ($type == 'admin') {
            $query = "DELETE FROM tbl_admin WHERE id = :id";
        } elseif ($type == 'superadmin') {
            $query = "DELETE FROM tbl_superadmin WHERE id = :id";
        } elseif ($type == 'resident') {
            $query = "DELETE FROM tbl_residents_family_members WHERE resident_id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $query = "DELETE FROM tbl_residents WHERE id = :id";
        } else {
            throw new Exception("Invalid user type.");
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        if ($stmt->execute()) {
            $conn->commit();
            $_SESSION['success'] = "User deleted successfully!";
        } else {
            throw new Exception("Failed to delete user.");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }
    header("Location: system_permissions.php");
    exit();
} else {
    $_SESSION['error'] = "Invalid request.";
    header("Location: system_permissions.php");
    exit();
}
