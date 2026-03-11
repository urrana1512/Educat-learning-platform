<?php
require 'includes/scripts/connection.php';  
   session_start();
   if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
       $user_id = $_SESSION['educat_logedin_user_id'];
       $query = "SELECT * FROM user_master WHERE user_id = $user_id";
       $result = mysqli_query($conn, $query);
       $userdata = mysqli_fetch_assoc($result);
       $user_role = $userdata["role"];
   }else{
        header("Location:sign-in.php");
   }

   if($_SERVER["REQUEST_METHOD"] == "POST"){
    // $user_id = $_SESSION['educat_logedin_user_id'];
    $user_name = $_POST['firstname'];
    $user_contact = $_POST['phone'];
    $email = $_POST['email'];

    // for contact updation
    if(!empty($user_contact)){
        $sql = "UPDATE `user_master` SET `user_contact_number` = '$user_contact' WHERE `user_id` = $user_id";
        mysqli_query($conn,$sql);
        $_SESSION["profile_success_message"] = "Account details updated.";
        header("location: myaccount.php");
    }

    // for email updation
    if(!empty($email)){
        $sql = "UPDATE `user_master` SET `user_email` = '$email', `isVerified` = 0 WHERE `user_id` = $user_id";
        mysqli_query($conn,$sql);
        $_SESSION["profile_success_message"] = "Account details updated.";
        header("location: myaccount.php");
    }

    // for first name updation
    if(!empty($user_name)){
        $sql = "UPDATE `user_master` SET `user_name` = '$user_name' WHERE `user_id` = $user_id";
        mysqli_query($conn,$sql);
        $_SESSION["profile_success_message"] = "Account details updated.";
        header("location: myaccount.php");
    }
    
    // // for image updation
    if (!empty($_FILES["user_profile"]["name"])) {
        $target_dir = "client/profile_photo/";
        $file_name = uniqid() . "_" . basename($_FILES["user_profile"]["name"]);
        $target_path = $target_dir . $file_name;
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = array("jpg", "jpeg", "png");
        $max_file_size = 2 * 1024 * 1024; // 2 MB
    
        // Fetch old profile path
        $selectQuery = "SELECT user_profile_photo FROM user_master WHERE user_id = '$user_id'";
        $result = mysqli_query($conn, $selectQuery);
        $row = mysqli_fetch_assoc($result);
        $old_profile_path = $row['user_profile_photo'];
    
        if (in_array($file_extension, $allowed_extensions) && $_FILES["user_profile"]["size"] <= $max_file_size) {
            if (move_uploaded_file($_FILES["user_profile"]["tmp_name"], $target_path)) {
                // Update new profile photo path in the database
                $updateQuery = "UPDATE user_master SET user_profile_photo = '$target_path' WHERE user_id = '$user_id'";
                mysqli_query($conn, $updateQuery);
    
                // Delete old profile photo file
                if (!empty($old_profile_path) && file_exists($old_profile_path)) {
                    unlink($old_profile_path);
                }
    
                $_SESSION["profile_success_message"] = "Account details updated.";
                header("location: myaccount.php");
            } else {
                $_SESSION["profile_error_message"] = "Failed to upload the file.";
                header("location: myaccount.php");
            }
        } else {
            $_SESSION["profile_error_message"] = "Invalid file type or size. Please upload a JPG, JPEG, or PNG file (up to 2 MB).";
            header("location: myaccount.php");
        }
    } else {
        $_SESSION["profile_error_message"] = "Error in updating Profile Photo.";
        header("location: myaccount.php");
    }
    

   }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/videop.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Profile</title>
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

    .videocon {
        display: flex;
        justify-content: space-between;
    }

    .leftprofile {
        width: 20%;
        height: 95%;
        display: flex;
        justify-content: center;
    }

    .rightprofile {
        width: 79%;
        height: 95%;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
    }

    .leftprofile img {
        height: 36% !important;
        width: 59% !important;
        border-radius: 50%;
        background-color: rgb(218, 218, 218);
        overflow: hidden;
        object-fit: fill;
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
        width: 100%;
    }
    
    .c1,
    .c2{
        gap: 10px;
        display: flex;
        width: 100%;
    }

    .c3 .oldp{
        flex-direction: row;
        align-items: center;
        gap: 10px;
    }

    .c3 .oldp input{
        width: 94%;
    }

    .c4 a{
        text-decoration: none;
        color: black;
        font-size: 1.5rem;
        display: flex;
        margin-bottom: 15px;
        align-items: center;
        width: fit-content;
    }

    .c5 button{
        padding: 10px 25px;
        border-radius: 10px;
        border: 2px solid var(--btn-color);
        background-color: var(--btn-color);
        color: white;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s all ease;
    }

    .c5 button:hover{
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
   <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
    <div class="container">
        <div class="videocon">
            <div class="leftprofile" id="leftprofile">
                <img id="userImage" src="<?php echo $userdata['user_profile_photo']?>" alt="" style="cursor: pointer;">
                <input name="user_profile" type="file" id="imageInput" style="display: none;" accept="image/*">
            </div>
            <div class="rightprofile">
                <?php
                    if(isset($_SESSION["profile_error_message"])){
                        echo $_SESSION["profile_error_message"];
                        unset($_SESSION["profile_error_message"]);
                    }
                    if(isset($_SESSION["profile_success_message"])){
                        echo $_SESSION["profile_success_message"];
                        unset($_SESSION["profile_success_message"]);
                    }
                ?>
                <div class="c1">
                    <div class="oldp">
                        <label for="fn">First Name</label>
                        <input placeholder="Your Full Name" type="text" id="fn" name="firstname" value="<?php echo $userdata["user_name"]?>">
                    </div>
                    <!-- <div class="newp">
                        <label for="ln">Last Name</label>
                        <input type="text" id="ln" value="">
                    </div> -->
                </div>
                <div class="c2">
                    <div class="oldp">
                        <label for="em">Email</label>
                        <input placeholder="Your Email" type="text" id="em" name="email" value="<?php echo $userdata["user_email"]?>">
                        <p style="font-size: 12px; color: orange;">Once Email is updated you have to reverify it.</p>
                    </div>
                    <div class="newp">
                        <label for="pn">Phone number</label>
                        <input name="phone" placeholder="<?php echo ($userdata['user_contact_number'] == 0)? 'Set your contact number': 'Your Contact Number';?>" type="text" id="pn" value="<?php echo ($userdata['user_contact_number'] == 0)? '':$userdata['user_contact_number'];?>">
                    </div>
                </div>
                <!-- <div class="c3">
                    <div class="oldp">
                        <img src="./assets/img/linkedin.png" alt="" height="35px" width="35px">
                        <input type="text" value="linkedin.com/in/Priyanshu-Pithadiya">
                    </div>
                </div> -->
                <div class="c4" style="display:flex; gap:20px">
                    <a href="contact.php"><img src="./assets/img/help.png" alt="" height="25px" width="25px">&nbsp;Help</a>
                    <a href="myaccount-security.php">◉&nbsp;Security</a>
                    <a href="logout.php"><img src="./assets/img/logout.png" alt="" height="25px" width="25px">&nbsp;Logout</a>
                </div>
                <div class="c5">
                    <button type="submit" value="Submit" name="submit">Save Changes</button>
                </div>
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
<script>
    const imageContainer = document.getElementById("leftprofile");
    const imageInput = document.getElementById("imageInput");
    const userImage = document.getElementById("userImage");

    // Trigger the file input when the user clicks on the image container
    imageContainer.addEventListener("click", () => {
        imageInput.click();
    });

    // Listen for changes in the file input
    imageInput.addEventListener("change", (event) => {
        const selectedImage = event.target.files[0];

        if (selectedImage) {
            // Set the selected image as the source of the userImage
            const imageURL = URL.createObjectURL(selectedImage);
            userImage.src = imageURL;
        }
    });

    
</script>

</html>