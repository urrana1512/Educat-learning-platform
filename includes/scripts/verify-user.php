<?php
// Verify the user based on the token in the URL
if (isset($_GET['token'])) {
    $verificationToken = $_GET['token'];

    // Perform a database query to check if the token is valid
    require 'connection.php';

    // Select the user email and verification token from the verification table
    $selectQuery = "SELECT user_email FROM verification WHERE token = '$verificationToken' AND is_verified = 0";
    $result = $conn->query($selectQuery);

    if ($result->num_rows > 0) {
        // Token is valid, update the user's verification status in the database
        $row = $result->fetch_assoc();
        $userEmail = $row['user_email'];

        // Update the verification status to indicate that the user is now verified
        $updateQuery = "UPDATE verification SET is_verified = 1 WHERE user_email = '$userEmail'";
        $conn->query($updateQuery);
        $updateQuery = "UPDATE user_master SET isVerified = 1 WHERE user_email = '$userEmail'";
        $conn->query($updateQuery);

        session_start();
        $_SESSION['educat_success_message'] = "Email verified successfully, Login with your correct credentials.";
        header("Location: ../../sign-in.php");
        exit();
    } else {
        // Invalid verification token, send another email for verification
        session_start();
        $_SESSION['educat_error_message'] =  "Invalid verification token. Another email is sent for verification.";
        header("Location: ../../verify-account.php");
        exit();
    }
} else {
    // No token provided, display an error message or handle it accordingly
    echo "Invalid verification request!";
}
?>
