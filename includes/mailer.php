<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function sendVerificationEmail($to, $verificationLink, $emailSubject) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ljeducat@gmail.com';
        $mail->Password = 'YOUR GMAIL GENERATED PASSWORD';
        $mail->SMTPSecure = 'tls'; // Use 'tls' or 'ssl'
        // For SSL use this port
        // $mail->Port = 465;
        // For TLS use this port
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('ljeducat@gmail.com', 'EduCat Team');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $emailSubject;
        $mail->Body = $verificationLink;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
