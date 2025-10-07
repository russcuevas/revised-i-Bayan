<?php
session_start();

include '../../database/connection.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$barangay = basename(__DIR__);
$session_key = "admin_id_$barangay";

if (!isset($_SESSION[$session_key])) {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION[$session_key];

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

$announcement_id = $_GET['id'] ?? null;

if ($announcement_id) {
    $stmt = $conn->prepare("SELECT * FROM tbl_announcement WHERE id = ? AND barangay = ?");
    $stmt->execute([$announcement_id, $admin_barangay_id]);
    $announcement = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($announcement) {
        $stmt = $conn->prepare("
            SELECT rfm.phone_number, r.first_name, r.last_name, rfm.resident_id
            FROM tbl_residents_family_members rfm
            JOIN tbl_residents r ON r.id = rfm.resident_id
            WHERE r.barangay_address = ?
        ");
        $stmt->execute([$admin_barangay_id]);
        $family_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $apikey = 'b2a42d09e5cd42585fcc90bf1eeff24e';
        $sendername = 'BPTOCEANUS';
        $announcement_title = $announcement['announcement_title'];
        $announcement_content = $announcement['announcement_content'];
        $announcement_venue = $announcement['announcement_venue'];

        $message = "Hi, I'm the Admin of Barangay $barangay_name. This is an important announcement:\n\nTitle: $announcement_title\nContent: $announcement_content\nVenue: $announcement_venue\n\nThank you!";

        $sent_numbers = [];

        foreach ($family_members as $member) {
            $phone_number = $member['phone_number'];

            if (in_array($phone_number, $sent_numbers)) {
                continue;
            }

            $sent_numbers[] = $phone_number;

            // ---- SEND EMAIL FIRST ----
            $resident_stmt = $conn->prepare("SELECT email FROM tbl_residents WHERE id = ?");
            $resident_stmt->execute([$member['resident_id']]);
            $resident_data = $resident_stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($resident_data['email'])) {
                $email = $resident_data['email'];
                $fullname = ucfirst(strtolower($member['first_name'])) . ' ' . ucfirst(strtolower($member['last_name']));

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'gmanagementtt111@gmail.com';
                    $mail->Password = 'skbtosbmkiffrajr';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('gsu-erequest@gmail.com', 'Barangay ' . $barangay_name);
                    $mail->addAddress($email, $fullname);

                    $mail->isHTML(true);
                    $mail->Subject = 'Barangay ' . $barangay_name . ' Announcement: ' . $announcement_title;

                    $mail_body = "<p>Dear {$fullname},</p>
                          <p>This is an important announcement from Barangay {$barangay_name}:</p>
                          <p><strong>Title:</strong> {$announcement_title}</p>
                          <p><strong>Content:</strong> {$announcement_content}</p>
                          <p><strong>Venue:</strong> {$announcement_venue}</p>
                          <p>Thank you,<br>Barangay {$barangay_name} Admin</p>";

                    $mail->Body = $mail_body;
                    $mail->send();
                } catch (Exception $e) {
                    error_log("Email failed to send to {$email}: {$mail->ErrorInfo}");
                }
            }

            // ---- THEN SEND SMS ----
            $ch = curl_init();
            $parameters = [
                'apikey' => $apikey,
                'number' => $phone_number,
                'message' => $message,
                'sendername' => $sendername
            ];

            curl_setopt($ch, CURLOPT_URL, 'https://semaphore.co/api/v4/messages');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $output = curl_exec($ch);
            curl_close($ch);
        }

        $_SESSION['success'] = "SMS sent to all unique family members successfully.";
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
