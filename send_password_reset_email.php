<?php
include("includes/database/connection.php");
include("includes/functions/mailSettings.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $email = $_POST['email'];

    // Generate a unique token (can use a library for secure token generation)
    $reset_token = bin2hex(random_bytes(32));
        $reset_link .= $reset_token;
    // Compose the email
    $subject = 'Password Reset Request';
    $message = "Click the link below to reset your password:\n\n$reset_link";

    // Send the email using PHPMailer
    require 'phpmailer/src/Exception.php';
    require 'phpmailer/src/PHPMailer.php';
    require 'phpmailer/src/SMTP.php';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    include("includes/functions/mailHosts.php");
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls'; // Change to 'ssl' if required by Hostinger
    $mail->Port = 587;
    $mail->addAddress($email);

    // Set the email subject and body
    $mail->Subject = $subject;
    $mail->Body = $message;

    // Store token in database
    $sql = "INSERT INTO password_reset_tokens (email, token) VALUES('$email', '$reset_token')";
    $result = mysqli_query($conn, $sql);

    // Send the email
    if ($mail->send()) {
        echo 'Email sent successfully. Please check your email to reset your password.';
    } else {
        echo 'Failed to send email. Error: ' . $mail->ErrorInfo;
    }
} else {
    echo 'Invalid email address.';
}
?>
