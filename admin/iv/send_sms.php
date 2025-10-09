<?php
session_start();
require '../../vendor/autoload.php'; // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include '../../database/connection.php';

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION[$session_key];

// Fetch barangay info
$admin_stmt = $conn->prepare("SELECT barangay_id FROM tbl_admin WHERE id = ?");
$admin_stmt->execute([$admin_id]);
$admin_barangay_id = $admin_stmt->fetchColumn();
if (!$admin_barangay_id) {
    die('Barangay not found for this admin.');
}

$barangay_name_stmt = $conn->prepare("SELECT barangay_name FROM tbl_barangay WHERE id = ?");
$barangay_name_stmt->execute([$admin_barangay_id]);
$barangay_name = $barangay_name_stmt->fetchColumn();
if (!$barangay_name) {
    die('Barangay name not found.');
}

// Check announcement ID
$announcement_id = $_GET['id'] ?? null;
if ($announcement_id) {
    $stmt = $conn->prepare("SELECT * FROM tbl_announcement WHERE id = ? AND barangay = ?");
    $stmt->execute([$announcement_id, $admin_barangay_id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($announcement) {
        // Fetch family members
        $stmt = $conn->prepare("
            SELECT rfm.phone_number, r.email, r.first_name, r.last_name, rfm.resident_id
            FROM tbl_residents_family_members rfm
            JOIN tbl_residents r ON r.id = rfm.resident_id
            WHERE r.barangay_address = ?
        ");

        $stmt->execute([$admin_barangay_id]);
        $family_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Announcement details
        $announcement_title = $announcement['announcement_title'];
        $announcement_content = $announcement['announcement_content'];
        $announcement_venue = $announcement['announcement_venue'];

        $message = "Hi, I'm the Admin of Barangay $barangay_name. This is an important announcement:\n\nTitle: $announcement_title\nContent: $announcement_content\nVenue: $announcement_venue\n\nThank you!";


        $sent_numbers = [];

        foreach ($family_members as $member) {
            $phone_number = $member['phone_number'];
            $email = $member['email'];
            $fullname = $member['first_name'] . ' ' . $member['last_name'];

            if (!in_array($phone_number, $sent_numbers)) {
                $sent_numbers[] = $phone_number;

                // Send SMS via Semaphore
                $ch = curl_init();
                $parameters = [
                    'apikey' => 'b2a42d09e5cd42585fcc90bf1eeff24e',
                    'number' => $phone_number,
                    'message' => strip_tags(str_replace("<br>", "\n", $message)),
                    'sendername' => 'BPTOCEANUS'
                ];
                curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);

                // Send Email via PHPMailer
                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'gmanagementtt111@gmail.com';
                    $mail->Password = 'skbtosbmkiffrajr'; // App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('gsu-erequest@gmail.com', 'iBayan');
                    $mail->addAddress($email, $fullname);

                    $mail->isHTML(true);
                    $mail->Subject = 'Barangay Announcement';
                    $mail->Body = $message;

                    $mail->send();
                } catch (Exception $e) {
                    // Log or handle email failure if necessary
                }
            }
        }

        $_SESSION['success'] = "Send announcement to residents successfully";
        header("Location: announcements.php");
        exit();
    } else {
        $_SESSION['error'] = "Announcement not found.";
        header("Location: announcements.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid announcement ID.";
    header("Location: announcements.php");
    exit();
}
