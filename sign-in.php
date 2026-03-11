<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="./assets/css/login.css">
  <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <title>Sign In</title>
</head>

<body>
  <div class="container">
    <div class="leftimg">
      <img src="./assets/img/study.png" alt="">
    </div>
    <form action="includes/scripts/signmein.php" method="post" class="login">
      <div class="main">
        <div class="heading">
          <h1>Sign In</h1>
        </div>
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
        <div class="inputs">
          <input type="email" name="educat_login_email" class="input" placeholder="Email" autofocus required>
          <input type="password" name="educat_login_password" class="input" placeholder="Password" pattern=".{8,}" title="Password must be at least 8 characters long" required>
        </div>
        <div class="link">
          <a href="forgot-password.php">Forgot password?</a>
        </div>
        <div class="button">
          <input type="submit" class="btn" value="Sign In">
        </div>
        <div class="signup">
          Don't have an account? <a href="sign-up.php">&nbsp;Sign Up</a>
        </div>
      </div>
    </form>
  </div>
</body>

</html>