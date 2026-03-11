<?php
require 'includes/mailer.php';
require 'includes/scripts/connection.php';
session_start();
$userEmail = $_SESSION['educat_temp_data_transfer_variable_between_pages'];  // Actual user's email

// FOR BASE URL PER USER FILE PATH
    // Get the current URL
    $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    // Remove the file name from the URL
    $baseUrl = rtrim($currentUrl, basename($_SERVER['SCRIPT_NAME']));

// Generate a unique verification token
$verificationToken = bin2hex(random_bytes(16));

// Insert the token into the database
$insertQuery = "INSERT INTO verification (user_email, token) VALUES ('$userEmail', '$verificationToken')";

if ($conn->query($insertQuery) === TRUE) {
    // Token inserted successfully, proceed with sending the verification email

    // Construct the verification link
    $verificationLink = $baseUrl . "includes/scripts/verify-user.php?token=$verificationToken";

    // Send the verification email
    if (sendVerificationEmail($userEmail, $verificationLink, "Email Verification")) {
        // Email sent successfully, redirect to a verification page
        $_SESSION['educat_success_message'] = "Verification email sent, Please verify your Email then login with your correct credentials.";
        header("Location: sign-in.php");
        exit();
    } else {
        // Failed to send email, handle the error
        $_SESSION['educat_error_message'] = "Failed to send email.";
        header("Location: sign-in.php");
        exit();
    }
} else {
    // Failed to insert token into the database, handle the error
    $_SESSION['educat_error_message'] = "Failed to store token.";
    header("Location: sign-in.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your Account</title>
</head>
<body>
    <h4>
        <?php
        session_start();
        if (isset($_SESSION['educat_error_message'])){
            echo "<a>" . $_SESSION['educat_error_message'] . "</a>";
            unset($_SESSION['educat_error_message']);
        }
        if (isset($_SESSION['educat_success_message'])){
            echo "<a>" . $_SESSION['educat_success_message'] . "</a>";
            unset($_SESSION['educat_success_message']);
        }
        ?>
    </h4>
</body>
</html>