<?php
session_start();
require './includes/scripts/connection.php';
if(isset($_POST["submit"])){
    $name = $_POST["txt_name"];
    $email= $_POST["email"];
    $message = $_POST["message"];

    if(!$name || !$email || !$message){
        $_SESSION["message"] = "Please enter all fields";
    }else{
        $current_date = date("d-m-y");
        $sql = "INSERT INTO `contact_us`( `contact_user_name`, `contact_user_email`, `contact_user_message`, `contact_date`) VALUES ('$name','$email','$message', '$current_date')";
        $result=mysqli_query($conn,$sql);
        if($result){
            header("location: contact.php");
        } else{
            $_SESSION["message"] = "please added all fields";
            header("location: contact.php");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/videop.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>Contact Us</title>
</head>
<style>
    .container {
        display: flex;
        justify-content: center;
    }
    .contact_cont{
        width: 99%;
        border-radius: 25px;
        min-height: 87vh;
        background-color: rgb(223 223 223 / 56%);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .leftcon{
        width: 49%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .rightcon{
        display: flex;
        align-items: center;
        justify-content: center;
        width: 49%;
    }
    .contact_cont h1{
        color: black;
        font-size: 4rem;
        font-family: monospace;
        margin-bottom: 2rem;
    }
    .inputcon{
        padding: 10px 20px;
        width: 250px;
        text-align: center; 
        border-radius: 25px;
        margin: 5px;
        transition: 0.5s all ease;
        text-shadow: gray;
        border: 2px solid grey;
    }
    .inputcon:focus{
        border: 2px solid rgb(15, 162, 230);
        width: 300px;
        outline: none;
    }
    form{
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 10px;
    }
    .btncon{
        cursor: pointer;
        width: 150px;
        padding: 10px 20px;
        border-radius: 25px;
        outline: none;
        border: 2px solid rgb(80, 80, 80);
        transition: 0.2s all ease;
        background-color: rgb(216, 216, 216);
        margin-top: 10px;
    }
    .btncon:hover{
        width: 180px;
        background-color: rgb(80, 80, 80);
        color: white;
        font-weight: bold;
    }
    .btncon:active{
        width: 160px;
        background-color: rgb(39, 39, 39);
        border: 2px solid  rgb(39, 39, 39);
        color: white;
        font-weight: bold;
    }
</style>

<body>
    <!-- Header START -->
    <header>
        <nav class="navigation" style="justify-content: center;">
            <div class="logo">
                <a href="index.php"><img src="assets/img/EduCat (3).png" alt="Logo"></a>
            </div>
        </nav>
    </header>
    <!-- Header END -->
    <div class="container">
        <div class="contact_cont">
            <div class="leftcon">
                <img src="./assets/img/contact_us.png" alt="">
            </div>
            <div class="rightcon">
                <div class="c_form">
                <?php
                    if(isset($_SESSION["message"])){
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error. </strong>'.$_SESSION["message"].'
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                        unset($_SESSION["message"]);
                    }
                ?>
                    <form action="" method="post">
                        <input type="text" placeholder="Name" name="txt_name" class="inputcon">
                        <input type="email" placeholder="Email" name="email" class="inputcon">
                        <textarea name="message" id="" cols="30" rows="10" placeholder="Message" class="inputcon"></textarea>
                        <input type="submit" name="submit" value="Submit" class="btncon">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer START -->
    <?php 
        include("includes/components/footer.php");
    ?>
    <!-- Footer END -->
    
</body>

</html>