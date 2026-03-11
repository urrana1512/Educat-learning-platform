<?php
    require 'includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
        if ($user_role != 3) {
            header("Location: 404.php");   
        }
    }else{
        header("Location: sign-in.php");
    }

    if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(isset($_POST['instructor_answer1'])){
            $r1 = $_POST['instructor_answer1'];
        }
        if(isset($_POST['instructor_answer2'])){
            $r2 = $_POST['instructor_answer2'];
        }
        if(isset($_POST['instructor_answer3'])){
            $r3 = $_POST['instructor_answer3'];
        }

        if(!empty($r1) && !empty($r2) && !empty($r3)){
            $sql = "INSERT INTO `instructor_create_answers`(`instructor_id`, `answer_one`, `answer_two`, `answer_three`) VALUES ('$user_id', '$r1','$r2','$r3')";
            $result1 = mysqli_query($conn,$sql);
            $updatedquery = "UPDATE `user_master` SET `role` = '2' WHERE `user_id` = $user_id;";
            $result = mysqli_query($conn,$updatedquery);
            header("location: dashboard/index.php");
        }else{
            $_SESSION['error_option'] = "please select this option";
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
    <title>Become an Instructor?</title>
</head>
<style>
    .container {
        display: flex;
        justify-content: center;
    }

    .btnint {
        margin-top: 15px;
    }

    .btnint button {
        padding: 5px 30px;
        border: 2px solid rgb(4, 4, 70);
        background-color: rgb(4, 4, 70);
        color: white;
        font-weight: bold;
        transition: 0.3s all ease;
        cursor: pointer;
        border-radius: 20px;
        font-size: 1rem;
    }

    .btnint button:hover {
        border: 2px solid rgb(4, 4, 70);
        color: rgb(4, 4, 70);
        background-color: transparent;
    }
</style>

<body>
    <header>
        <nav class="navigation">
            <div class="logo">
                <a href="index.php"><img src="assets/img/EduCat (3).png" alt="Logo"></a>
            </div>
            <button class="menu-btn">☰</button>
            <div class="sidebar">
                <div class="menu-content">
                    <a href="index.php" class="buttons"><- Go back</a>
                </div>
            </div>
        </nav>
    </header>
    <Form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
    <div class="container">
        <div class="boxins">
            <div class="inq">
                <h4>What kind of teaching have you done before?</h4>
                <div class="options">
                    <label>
                        <input value="1" type="radio" name="instructor_answer1">
                        In person, informally
                    </label> <br>
                    <label>
                        <input value="2" type="radio" name="instructor_answer1">
                        In person, professionally
                    </label> <br>
                    <label>
                        <input value="3" type="radio" name="instructor_answer1">
                        Online
                    </label> <br>
                    <label>
                        <input value="4" type="radio" name="instructor_answer1">
                        Other
                    </label> <br>
                </div>
            </div>
            <div class="inq">
            
                <h4>How much of a video "pro" are you?</h4>
                <div class="options">
                    <label>
                        <input value="1" type="radio" name="instructor_answer2">
                        I'm beginner
                    </label> <br>
                    <label>
                        <input value="2" type="radio" name="instructor_answer2">
                        I have some knowledge
                    </label> <br>
                    <label>
                        <input value="3" type="radio" name="instructor_answer2">
                        I'm experiencede
                    </label> <br>
                    <label>
                        <input value="4" type="radio" name="instructor_answer2">
                        I have videos ready to upload
                    </label> <br>
                </div>
            </div>
            <div class="inq">
           
                <h4>Do you have your audience to share course with?</h4>
                <div class="options">
                    <label>
                        <input value="1" type="radio" name="instructor_answer3">
                        Not at the moment
                    </label> <br>
                    <label>
                        <input value="2" type="radio" name="instructor_answer3">
                        I have a small following
                    </label> <br>
                    <label>
                        <input value="3" type="radio" name="instructor_answer3">
                        I have a sizable following
                    </label> <br>
                </div>
                
            </div>
            <div class="btnint">
                <button type="submit" name="submit" value="submit">Submit</button>
            </div>
        </div>
    </div>
                </Form>
</body>

</html>