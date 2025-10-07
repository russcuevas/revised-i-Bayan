<?php
session_start();
include '../database/connection.php';

header('Content-Type: application/json');

$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

try {
    $sql = "SELECT b.barangay_name, COUNT(r.id) AS total_residents
            FROM tbl_residents_family_members r
            JOIN tbl_barangay b ON r.barangay_address = b.id
            WHERE YEAR(r.created_at) = :year
            GROUP BY b.barangay_name
            ORDER BY b.barangay_name ASC";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $residentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $residentsData
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
