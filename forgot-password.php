<?php
require 'includes/mailer.php';
require 'includes/scripts/connection.php';

// Function to generate a random 4-digit OTP
function generateOTP() {
    return sprintf('%04d', rand(0, 9999));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    session_start();
    $userEmail = $_POST['educat_password_reset_email'];  // Actual user's email

    // Generate a unique verification token (OTP)
    $verificationToken = generateOTP();
    // Insert the token into the database along with the user email
    $insertQuery = "INSERT INTO verification (user_email, token) VALUES ('$userEmail', '$verificationToken')";

    if ($conn->query($insertQuery) === TRUE) {
        // Token inserted successfully, proceed with sending the verification email

        // Construct the verification link
        $verificationLink = "Your OTP for resetting password is " . $verificationToken;

        // Send the verification email
        if (sendVerificationEmail($userEmail, $verificationLink, "EduCat OTP to Reset Password Request")) {
            // Email sent successfully, redirect to a verification page
            $_SESSION['educat_success_message'] = "OTP is sent to your email, Verify with the correct OTP to proceed.";
            header("Location: verify-otp.php");
            exit();
        } else {
            // Failed to send email, handle the error
            $_SESSION['educat_error_message'] = "Failed to send email. Error: " . $mail->ErrorInfo;
            header("Location: forgot-password.php");
            exit();
        }
    } else {
        // Failed to insert token into the database, handle the error
        $_SESSION['educat_error_message'] = "Failed to store token in the database. Error: " . $conn->error;
        header("Location: forgot-password.php");
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
  <link rel="stylesheet" href="./assets/css/login.css">
  <title>Forgot Password</title>
</head>

<body>
  <div class="container">
    <div class="leftimg">
      <img src="./assets/img/forgot.png" alt="">
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="login">
      <div class="main">
        <div class="heading">
          <h1>Reset Password</h1>
        </div>
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
        <div class="inputs">
          <input type="email" name="educat_password_reset_email" class="input" placeholder="Enter your Email" required>
        </div>
        <div class="button">
          <input value="Submit" name="Submit" type="submit" class="btn">
        </div>
        <div class="signup">
          <a href="sign-in.php">Back to Login</a>
        </div>
      </div>
    </form>
  </div>
</body>

</html>