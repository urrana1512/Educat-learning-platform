<?php
require 'includes/scripts/connection.php';  
session_start();
   if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
        $password = $userdata["user_password"];
    }
    elseif (isset($_SESSION['allow_user_reset_password'])) {
            $user_email = $_SESSION['allow_user_reset_password'];
            $query = "SELECT * FROM user_master WHERE user_email = '$user_email'";
            $result = mysqli_query($conn, $query);
            $userdata = mysqli_fetch_assoc($result);
            $user_role = $userdata["role"];
            $user_id = $userdata["user_id"];
            $password = $userdata["user_password"];
    }
    else{
        header("Location:sign-in.php");
    }


    if($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION['allow_user_reset_password'])){
       $old_password = $_POST["old_password"];
       $new_password = $_POST["new_password"];
        
        $sql = "SELECT * FROM user_master WHERE user_id = $user_id";
        $res = mysqli_query($conn, $sql);
        if($res){
            $rowPass = mysqli_fetch_assoc($res);
            $oldDBPassword = $rowPass["user_password"];
            $num_rows = $result->num_rows;
            if ($num_rows > 0) {
                if (strlen($new_password) < 8) {
                    $_SESSION['educat_error_message'] = "Your new password length shoud be 8 or above";
                } else {
                    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                    if (password_verify($old_password, $oldDBPassword)) {
                        $sql = "UPDATE `user_master` SET `user_password` = '$hashedPassword' WHERE `user_id` = $user_id";
                        mysqli_query($conn, $sql);
                        $_SESSION['educat_success_message'] = "Your password was updated";
                    } else {
                        $_SESSION['educat_error_message'] = "Your password do not match with old password";
                    }
                }
            }else{
                $_SESSION['educat_error_message'] = "User not found.";
            }
        }else{
            $_SESSION["educat_error_message"] = "Error while fetching your data from database.";
        }
        // Check if the new password meets the minimum length requirement
    
    
   }

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['allow_user_reset_password'])){
       $new_password = $_POST["new_password"];
        // Check if the new password meets the minimum length requirement
    if (strlen($new_password) < 8) {
        $_SESSION['educat_error_message'] = "Your new password length shoud be 8 or above";
    } else {
        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
        if ($new_password != "") {
            $user_email = $_SESSION['allow_user_reset_password'];
            $sql = "UPDATE `user_master` SET `user_password` = '$hashedPassword' WHERE `user_email` = '$user_email'";
            mysqli_query($conn, $sql);
            $_SESSION['educat_success_message'] = "Your password is updated, Login with your correct credentials.";
            unset($_SESSION['allow_user_reset_password']);            
            header("location: sign-in.php");
        } else {
            $_SESSION['educat_error_message'] = "Your password is invalid.";
        }
    }
    
   }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="./assets/css/videop.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>All Videos</title>
</head>
<style>
    body{
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 100vh;
    }
    
    .container {
        display: flex;
        justify-content: center;
    }

    h1 {
        color: black;
        text-align: center;
        margin: 0;
    }

    .videocon {
        flex-direction: column;
        justify-content: space-evenly;
    }

    .oldp input,
    .newp input {
        padding: 7px 10px;
        border: 2px solid rgb(74 74 74 / 71%);
        margin-top: 5px;
        border-radius: 8px;
        transition: 0.3s all ease;
    }

    .oldp input:focus,
    .newp input:focus {
        outline: none;
    }

    label {
        font-weight: bold;
    }

    .oldp,
    .newp{
        display: flex;
        flex-direction: column;
    }

    .centerset{
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
    }
    
    .bottomset button{
        padding: 10px 25px;
        border-radius: 10px;           
        border: 2px solid var(--btn-color);
        background-color: var(--btn-color);
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s all ease;
    }

    .bottomset button:hover{
        border: 2px solid var(--btn-color);
        background-color: transparent;
        color: var(--btn-color);
    }
</style>

<body>

   <!-- Header START -->
        <?php
            include("includes/components/header-two.php");
        ?>
   <!-- Header END -->
   <form method="post" enctype="multipart/form-data">
    <div class="container">
        <div class="videocon">
            <div class="topset">
                <h1>Security</h1>
                <h3>Reset Password</h3>
                <font color="red">
                    <?php
                 if(isset($_SESSION['educat_error_message'])){
                     echo $_SESSION['educat_error_message'];
                     unset($_SESSION['educat_error_message']);
                 }
                    ?>
                </font>
                <font color="green">
                    <?php
                 if(isset($_SESSION['educat_success_message'])){
                     echo $_SESSION['educat_success_message'];
                     unset($_SESSION['educat_success_message']);
                 }
                    ?>
                </font>
            </div>
            <div class="centerset">
                <?php
                if(!isset($_SESSION['allow_user_reset_password'])){
                ?>
                <div class="oldp">
                    <label for="oldp">Old Password</label>
                    <input type="password" name="old_password" id="oldp" required>
                </div>
                <?php
                }
                ?>
                <div class="newp">
                    <label for="newp">New Password</label>
                    <input type="text" name="new_password"id="newp" required>
                </div>
            </div>
            <div class="bottomset">
                <button>Save Password</button>
            </div>
        </div>
    </div>
</form>
    
    <!-- Footer START -->
    <?php
            include("includes/components/footer.php");
        ?>
    <!-- Footer END -->
    
</body>

</html>