<?php
// Start session and include DB connection
session_start();
include '../../database/connection.php'; // Adjust path as needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input using prepared statements
    $sql = "INSERT INTO tbl_residents_family_members (
                resident_id,
                barangay_address,
                first_name,
                middle_name,
                last_name,
                suffix,
                purok,
                relationship,
                gender,
                date_of_birth,
                birthplace,
                age,
                civil_status,
                is_working,
                is_approved,
                is_barangay_voted,
                years_in_barangay,
                phone_number,
                philhealth_number,
                school,
                occupation
            ) VALUES (
                :resident_id,
                :barangay_address,
                :first_name,
                :middle_name,
                :last_name,
                :suffix,
                :purok,
                :relationship,
                :gender,
                :date_of_birth,
                :birthplace,
                :age,
                :civil_status,
                :is_working,
                :is_approved,
                :is_barangay_voted,
                :years_in_barangay,
                :phone_number,
                :philhealth_number,
                :school,
                :occupation
            )";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':resident_id'        => $_POST['resident_id'],
            ':barangay_address'   => $_POST['barangay_address'],
            ':first_name'         => $_POST['first_name'],
            ':middle_name'        => $_POST['middle_name'],
            ':last_name'          => $_POST['last_name'],
            ':suffix'             => $_POST['suffix'],
            ':purok'              => $_POST['purok'],
            ':relationship'       => $_POST['relationship'],
            ':gender'             => $_POST['gender'],
            ':date_of_birth'      => $_POST['birthday'],
            ':birthplace'         => $_POST['birthplace'],
            ':age'                => $_POST['age'],
            ':civil_status'       => $_POST['civil_status'],
            ':is_working'         => $_POST['is_working'],
            ':is_approved'        => $_POST['is_approved'],
            ':is_barangay_voted'  => $_POST['is_barangay_voted'],
            ':years_in_barangay'  => $_POST['years_in_barangay'],
            ':phone_number'       => $_POST['phone_number'],
            ':philhealth_number'  => $_POST['philhealth_number'],
            ':school'             => $_POST['school'],
            ':occupation'         => $_POST['occupation']
        ]);

        // Redirect or show success message
        $_SESSION['success'] = "Family member saved successfully.";
        header("Location: manage_residents.php");
        exit();
    } catch (PDOException $e) {
        // Log error and show friendly message
        $_SESSION['error'] = "Error saving data: " . $e->getMessage();
        header("Location: manage_residents.php");
        exit();
    }
} else {
    // Invalid access
    header("Location: manage_residents.php");
    exit();
}
