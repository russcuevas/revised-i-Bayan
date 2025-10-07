<?php
session_start();
include '../database/connection.php';
header('Content-Type: application/json');

$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

try {
    $sql = "
        SELECT b.barangay_name,
            COALESCE(SUM(c1.total_amount), 0) +
            COALESCE(SUM(c2.total_amount_paid), 0) +
            COALESCE(SUM(c3.total_amount), 0) +
            COALESCE(SUM(c4.total_amount), 0) AS total_fees
        FROM tbl_barangay b
        LEFT JOIN tbl_cedula_claimed c1 ON c1.for_barangay = b.id AND YEAR(c1.created_at) = :year
        LEFT JOIN tbl_certificates_claimed c2 ON c2.for_barangay = b.id AND YEAR(c2.created_at) = :year
        LEFT JOIN tbl_operate_claimed c3 ON c3.for_barangay = b.id AND YEAR(c3.created_at) = :year
        LEFT JOIN tbl_closure_claimed c4 ON c4.for_barangay = b.id AND YEAR(c4.created_at) = :year
        GROUP BY b.barangay_name
        ORDER BY b.barangay_name ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
