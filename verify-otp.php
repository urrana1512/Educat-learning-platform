<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the OTP values from the form
    $otp1 = $_POST['otp_1'];
    $otp2 = $_POST['otp_2'];
    $otp3 = $_POST['otp_3'];
    $otp4 = $_POST['otp_4'];

    // Combine the OTP values into a single string
    $verificationToken = $otp1 . $otp2 . $otp3 . $otp4;

    // Perform a database query to check if the token is valid
    require 'includes/scripts/connection.php';

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
        
        // Update the user_master table as well (assuming you have this table)
        $updateQuery = "UPDATE user_master SET isVerified = 1 WHERE user_email = '$userEmail'";
        $conn->query($updateQuery);

        session_start();
        $_SESSION['educat_success_message'] = "Reset your Password.";
        $_SESSION['allow_user_reset_password'] = $userEmail;
        header("Location: myaccount-security.php");
        exit();
    } else {
        // Invalid verification token, send another email for verification
        session_start();
        $_SESSION['educat_error_message'] =  "Invalid OTP.";
        header("Location: verify-otp.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/login.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <title>OTP Verification</title>
</head>

<style>
    .input {
        width: 40px;
        border: none;
        border-bottom: 3px solid rgba(0, 0, 0, 0.5);
        margin: 0 10px;
        text-align: center;
        font-size: 36px;
    }

    .input:focus {
        border-bottom: 3px solid orange;
        outline: none;
    }

    .input:nth-child(1) {
        cursor: pointer;
        pointer-events: all;
    }

    .inputs {
        flex-direction: row;
    }

    .input:focus {
        width: 60px;
    }
</style>

<body>
    <div class="container">
        <div class="leftimg">
            <img src="./assets/img/otp.png" alt="">
        </div>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="login">
            <h1>OTP Verification</h1>
            <div class="main">
                <?php
                session_start();
                if (isset($_SESSION['educat_error_message'])) {
                    echo "<a>" . $_SESSION['educat_error_message'] . "</a>";
                    unset($_SESSION['educat_error_message']);
                }
                if (isset($_SESSION['educat_success_message'])) {
                    echo "<a>" . $_SESSION['educat_success_message'] . "</a>";
                    unset($_SESSION['educat_success_message']);
                }
                ?>
                <div class="container">
                    <div id="inputs" class="inputs">
                        <input class="input" type="text" name="otp_1" inputmode="numeric" maxlength="1" required />
                        <input class="input" type="text" name="otp_2" inputmode="numeric" maxlength="1" required />
                        <input class="input" type="text" name="otp_3" inputmode="numeric" maxlength="1" required />
                        <input class="input" type="text" name="otp_4" inputmode="numeric" maxlength="1" required />
                    </div>
                </div>
                <div class="button">
                    <input type="submit" class="btn" value="Verify">
                </div>
                <div class="signup">
                    <a href="forgot-password.php" title="If you made a mistake while typing email please correct it.">Wrong Email?</a>
                </div>
            </div>
        </form>
    </div>
</body>

<script>
    const inputs = document.getElementById("inputs");

    inputs.addEventListener("input", function (e) {
        const target = e.target;
        const val = target.value;

        if (isNaN(val)) {
            target.value = "";
            return;
        }

        if (val != "") {
            const next = target.nextElementSibling;
            if (next) {
                next.focus();
            }
        }
    });

    inputs.addEventListener("keyup", function (e) {
        const target = e.target;
        const key = e.key.toLowerCase();

        if (key == "backspace" || key == "delete") {
            target.value = "";
            const prev = target.previousElementSibling;
            if (prev) {
                prev.focus();
            }
            return;
        }
    });
</script>

</html>
