<?php
    require 'includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
    }
    $id = $_GET["id"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <title>EduCat</title>
</head>
<style>
    .quizcon{
        width: 97%;
        margin: auto;
        margin-top: 20px;
    }

    .quiz{
        background-color: white;
        padding: 17px;
        border-radius: 15px;
        box-shadow: 0px 0px 8px 1px gray;
        border: 3px solid transparent;
        transition: 0.3s all ease;
        margin-bottom: 20px;
    }
    
    .quiz:hover{
        border: 3px solid rgba(3, 3, 58, 0.562);
    }

    .quiz form h2{
        margin: 0;
    }

    .quiz form .quizleft{
        width: 3%;
    }

    .quiz form .quizright{
        width: 97%;
    }

    .quiz form{
        display: flex;
    }

    .optionquiz input,
    .optionquiz label{
        cursor: pointer;
    }

    .clear{
        width: fit-content;
        padding: 5px;
        border-radius: 9px;
        outline: none;
        border: 2px solid rgba(1, 1, 68, 0.562);
        cursor: pointer;
        box-shadow: 0px 0px 5px 1px gray;
        font-size: 1rem;
        transition: 0.3s all ease;
    }
    
    .clear:hover{
        box-shadow: 0px 0px 10px 1px gray;
    }

    .optionquiz label{
        font-size: 1.2rem;
    }

    .optionquiz{
        display: flex;
        gap: 7px;
        align-items: center;
        justify-content: center;
    }

    .optionquiz input{
        margin: 0;
    }

    .quizsubmit{
        text-decoration: none;  
        width: 100%;
        text-align: center;
        color: black;
        font-size: 1.2rem;
        font-weight: bold;
    }
</style>
<body>
    <?php
    include("includes/components/header.php");
    ?>

    <div class="quizcon">
        <div class="quizlist">
            <div class="quiz">
                <form action="#">
                    <div class="quizleft">
                        <h2>1.</h2>
                    </div>
                    <div class="quizright">
                        <div class="quizque" style="display: flex; justify-content: space-between;"><h2> Who is the Designer of Educat ?</h2><input type="reset" value="Clear my choice" class="clear"></div>
                        <div class="quizans" style="display: flex; gap: 30px; margin-top: 10px;">
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q1o1">
                                <label for="q1o1">Priyanshu</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q1o2">
                                <label for="q1o2">Vansh</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q1o3">
                                <label for="q1o3">Samarth</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q1o4">
                                <label for="q1o4">Jay</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="quiz">
                <form action="#">
                    <div class="quizleft">
                        <h2>2.</h2>
                    </div>
                    <div class="quizright">
                        <div class="quizque" style="display: flex; justify-content: space-between;"><h2> Who is the Backend Programmer of Educat ?</h2><input type="reset" value="Clear my choice" class="clear"></div>
                        <div class="quizans" style="display: flex; gap: 30px; margin-top: 10px;">
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q2o1">
                                <label for="q2o1">Priyanshu</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q2o2">
                                <label for="q2o2">Vansh</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q2o3">
                                <label for="q2o3">Rudra</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q2o4">
                                <label for="q2o4">Jay</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="quiz">
                <form action="#">
                    <div class="quizleft">
                        <h2>3.</h2>
                    </div>
                    <div class="quizright">
                        <div class="quizque" style="display: flex; justify-content: space-between;"><h2> Who is the Documentor of Educat ?</h2><input type="reset" value="Clear my choice" class="clear"></div>
                        <div class="quizans" style="display: flex; gap: 30px; margin-top: 10px;">
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q3o1">
                                <label for="q3o1">Rudra</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q3o2">
                                <label for="q3o2">Vansh</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q3o3">
                                <label for="q3o3">Aryan</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q3o4">
                                <label for="q3o4">Jay</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="quiz">
                <form action="#">
                    <div class="quizleft">
                        <h2>4.</h2>
                    </div>
                    <div class="quizright">
                        <div class="quizque" style="display: flex; justify-content: space-between;"><h2> Who is the Backend Programmer of Educat ?</h2><input type="reset" value="Clear my choice" class="clear"></div>
                        <div class="quizans" style="display: flex; gap: 30px; margin-top: 10px;">
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q4o1">
                                <label for="q4o1">Priyanshu</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q4o2">
                                <label for="q4o2">Aryan</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q4o3">
                                <label for="q4o3">Samarth</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q4o4">
                                <label for="q4o4">Jay</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="quiz">
                <form action="#">
                    <div class="quizleft">
                        <h2>5.</h2>
                    </div>
                    <div class="quizright">
                        <div class="quizque" style="display: flex; justify-content: space-between;"><h2> Who is the Documentor of Educat ?</h2><input type="reset" value="Clear my choice" class="clear"></div>
                        <div class="quizans" style="display: flex; gap: 30px; margin-top: 10px;">
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q5o1">
                                <label for="q5o1">Priyanshu</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q5o2">
                                <label for="q5o2">Vansh</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q5o3">
                                <label for="q5o3">Samarth</label>
                            </div>
                            <div class="optionquiz">
                                <input type="radio" name="question" id="q5o4">
                                <label for="q5o4">Jay</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="quiz" style="display: flex; justify-content: center; align-items: center;">
                <a href="#" class="quizsubmit">Submit</a>
            </div>
        </div>
    </div>
    <?php 
        include("includes/components/footer.php");
    ?>
</body>
</html>