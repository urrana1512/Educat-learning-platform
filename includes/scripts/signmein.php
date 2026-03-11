<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginEmail = $_POST["educat_login_email"];
    $loginPassword = $_POST["educat_login_password"];

    // Validate input (you might want to add more validation checks)
    if (empty($loginEmail) || empty($loginPassword)) {
        $_SESSION['educat_error_message'] = "Email and password are required.";
        header("Location: sign-in.php");
    }

    // Database connection
    require 'connection.php';

    // Retrieve hashed password from the database based on the provided email
    $selectQuery = "SELECT * FROM user_master WHERE user_email = '$loginEmail'";
    $result = $conn->query($selectQuery);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPasswordFromDB = $row["user_password"];
        $isVerified = $row["isVerified"];
        $userId = $row["user_id"];

        // Verify the provided password against the stored hashed password
        if (password_verify($loginPassword, $hashedPasswordFromDB)) {
            // Password is correct, set session variables or perform other actions as needed
            if ($isVerified == 1) {
                // Password is correct and the user is verified
                $_SESSION['educat_logedin_user_id'] = $row["user_id"];
                                
                header("Location: ../../index.php");
                
            } else {
                // User is not verified
                // $_SESSION['educat_error_message'] = "Your account is not verified yet, Please check your email for verification link.";
                $_SESSION['educat_temp_data_transfer_variable_between_pages'] = $loginEmail;
                header("Location: ../../verify-account.php");
            }
        } else {
            // Password is incorrect
            $_SESSION['educat_error_message'] = "Incorrect password.";
            header("Location: ../../sign-in.php");
        }
    } else {
        // Email not found in the database
        $_SESSION['educat_error_message'] = "Email not found.";
        header("Location: ../../sign-in.php");
    }

    $conn->close();
}
?>
