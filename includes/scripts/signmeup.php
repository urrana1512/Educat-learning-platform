<?php
session_start();
// Database connection
require 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST["educat_user_fullname"];
    $email = $_POST["educat_user_email"];
    $password = $_POST["educat_user_password"];
    $hashedpassword = password_hash($_POST["educat_user_password"], PASSWORD_DEFAULT); // Hash the password
    $confirmPassword = $_POST["educat_user_confirm_password"];
    $registrationDate = date("d-m-Y");
    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT COUNT(*) FROM `user_master` WHERE `user_email` = '$email'";
    $res = mysqli_query($conn, $sql);
    
    if($res) {
        $row = mysqli_fetch_array($res);
        $count = $row[0]; // Number of rows with the specified email
        
        if($count > 0) {
            $_SESSION['educat_error_message'] = "Email address already exists.";
            header("Location: ../../sign-up.php");
            exit(); // Always exit after a header redirect
        }
    }

    if (empty($fullname) || empty($email) || empty($password) || empty($confirmPassword)) {
        $_SESSION['educat_error_message'] = "All fields are required.";
        header("Location: ../../sign-up.php");
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['educat_error_message'] = "Invalid email format.";
        header("Location: ../../sign-up.php");
        exit();
    }
    
    if ($password !== $confirmPassword) {
        $_SESSION['educat_error_message'] = "Passwords do not match.";
        header("Location: ../../sign-up.php");
        exit();
    }

        
    // Insert user data into the database
    $insertQuery = "INSERT INTO user_master (user_name, user_email, user_password, role, isVerified, isActive, registration_date) VALUES ('$fullname', '$email', '$hashedpassword', 3, 0, 0, '$registrationDate')";

    if ($conn->query($insertQuery) === TRUE) {
        // Registration successful
        $_SESSION['educat_success_message'] = "Account created, Please login with correct credentials.";
        header("Location: ../../sign-in.php");
        exit();
    } else {
        // Handle database error
        $_SESSION['educat_error_message'] = "Error: " . $insertQuery . "<br>" . $conn->error;
        header("Location: ../../sign-up.php");
        exit();
    }

    $conn->close();
}
?>
