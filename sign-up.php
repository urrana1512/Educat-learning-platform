<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/login.css">
    <title>Sign up</title>
</head>

<body>
    <div class="container">
        <form action="includes/scripts/signmeup.php" method="post" class="login" id="signupform">
            <div class="main">
                <div class="heading">
                    <h1>Sign up</h1>
                </div>
                <h4><?php
                    session_start();
                        if (isset($_SESSION['educat_error_message'])){
                            echo "<a>" . $_SESSION['educat_error_message'] . "</a>";
                            unset($_SESSION['educat_error_message']);
                        }
                    ?>
                </h4>
                <div class="inputs">
                    <input type="text" name="educat_user_fullname" class="input" placeholder="Full Name" pattern="^[^0-9]+$" title="Please enter a valid name without numbers" autofocus required>
                    <input type="email" name="educat_user_email" class="input" placeholder="Email" id="emailID" required>
                    <span id="emailError" class="error"></span>
                    <input type="password" name="educat_user_password" class="input" placeholder="Password" pattern=".{8,}" title="Password must be at least 8 characters long" id="password" required>
                    <input type="password" name="educat_user_confirm_password" class="input" placeholder="Confirm password" pattern=".{8,}" title="Password must be at least 8 characters long" oninput="checkPasswordMatch()" id="confirm-password" required>
                </div>
                <div class="button">
                    <input type="submit" class="btn" value="Sign Up">
                </div>
                <div class="signup">
                    Have an account? <a href="sign-in.php">&nbsp;Sign In</a>
                </div>
            </div>
        </form>
        <div class="leftimg">
            <img src="./assets/img/study.png" alt="">
        </div>
    </div>
    <script>
        function checkPasswordMatch() {
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("confirm-password");

            if (password.value !== confirmPassword.value) {
            confirmPassword.setCustomValidity("Passwords do not match");
            } else {
            confirmPassword.setCustomValidity("");
            }
        }
    </script>

<script>
        // document.getElementById('signupform').addEventListener('submit', function (event) {
        //     // Prevent the form from submitting
        //     event.preventDefault();

        //     // Get the email input value
        //     var emailInput = document.getElementById('emailID');
        //     var email = emailInput.value.trim();

        //     // Regular expression for basic email validation
        //     var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        //     // Check if the email is valid
        //     if (emailRegex.test(email)) {
        //         // Email is valid, you can submit the form or perform additional actions
        //         document.getElementById('emailError').textContent = '';
        //         // Here you can submit the form or perform other actions
        //         alert('Email is valid.');
        //     } else {
        //         // Email is not valid, display an error message
        //         document.getElementById('emailError').textContent = 'Invalid email format';
        //     }
        // });
    </script>

</body>

</html>