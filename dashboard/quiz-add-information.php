<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

    require '../includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
        if($user_role != 1 && $user_role != 2){
            header("Location: ../404.php");
        }
    }else{
        header("Location: ../sign-in.php");
    }

    
    $quiz_for_course = $_GET["course"];
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST["course_quiz_name"]) && isset($_POST["course_quiz_total_question"]) && isset($_POST["course_quiz_total_marks"]))) {
        $quiz_name = $_POST["course_quiz_name"];
        $quiz_total_questions = $_POST["course_quiz_total_question"];
        $quiz_total_marks = $_POST["course_quiz_total_marks"];
        
        $sqlToInsert = "INSERT INTO `quiz_master` (quiz_for_course, quiz_name, quiz_total_marks, quiz_total_questions, quiz_added_by) VALUES($quiz_for_course, '$quiz_name', $quiz_total_marks, $quiz_total_questions, $user_id)";
        $resToInsert = mysqli_query($conn, $sqlToInsert);
        if($resToInsert){
            $selectAgain = "SELECT quiz_id FROM quiz_master WHERE quiz_for_course = $quiz_for_course AND quiz_added_by = $user_id";
            $resAgain = mysqli_query($conn, $selectAgain);
            $rowAgain = mysqli_fetch_assoc($resAgain);
            $quizID = $rowAgain["quiz_id"];
            $_SESSION["educat_success_message"] = "Quiz added.";
            header("Location: quiz-add.php?course=" . $quiz_for_course . "&quiz=" . $quizID);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, invoice, html5, responsive, Projects">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Add/Manage Quiz</title>

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/animate.css">

    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<style>
    .dataTables_filter,
    .dataTables_length,
    .dataTables_paginate,
    .dataTables_info{
        display: none !important;
    }
</style>
<body>
    <!-- <div id="global-loader">
        <div class="whirly-loader"> </div>
    </div> -->

    <div class="main-wrapper">

    <div class="header">

<div class="header-left active">
    <a href="index.php" class="logo">
        <img src="../assets/img/EduCat (3).png" alt="">
    </a>
    <a href="index.php" class="logo-small">
        <img src="../assets/img/EduCat (4).png" alt="">
    </a>
    <a id="toggle_btn" href="javascript:void(0);">
    </a>
</div>

<a id="mobile_btn" class="mobile_btn" href="#sidebar">
    <span class="bar-icon">
        <span></span>
        <span></span>
        <span></span>
    </span>
</a>


            <!-- Header START -->
            <?php
                include("header.php");
            ?>
            <!-- Header END -->


<div class="dropdown mobile-user-menu">
    <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
        aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
    <div class="dropdown-menu dropdown-menu-right">
        <a class="dropdown-item" href="aprofile.php">My Profile</a>
        <!-- <a class="dropdown-item" href="generalsettings.php">Settings</a> -->
        <a class="dropdown-item" href="#">Logout</a>
    </div>
</div>

</div>


<?php
    include("sidebar.php");
?>

        <div class="page-wrapper">
            <div class="content">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Add/Manage Quiz</h4>
                        <h6 style="color:red">Important: Once quiz is created you can not modify and delete it. <b style="color: orange" title="Modification undermines assessment integrity and diminishes the reliability of learning outcomes.">Understand Why?</b></h6>
                    </div>
                </div>


                <?php
                
                $sqlSelectQuiz = "SELECT * FROM quiz_master WHERE quiz_for_course = $quiz_for_course AND quiz_added_by = $user_id";
                $resultSelectQuiz = mysqli_query($conn, $sqlSelectQuiz);
                if(($resultSelectQuiz) && (mysqli_num_rows($resultSelectQuiz) > 0)){
                    $rowSelectQuiz = mysqli_fetch_assoc($resultSelectQuiz);
                }
                ?>

                <div class="card">
                    <div class="card-body">
                        <form action="" method="post" style="margin: 20px; <?php echo (mysqli_num_rows($resultSelectQuiz) > 0)? "display:none;":"";?>">
                            <div class="row">
                                <div class="col-lg-12 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Quiz Name</label>
                                        <input type="text" name="course_quiz_name" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>How many questions do you want to add?</label>
                                        <input placeholder="Allowed min=10 and max=50." step="5" type="number" name="course_quiz_total_question" min="10" max="50" class="form-control"  pattern="[0-9]+" title="Please enter a number between 0 and 100" required>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Marks per question</label>
                                        <input placeholder="Allowed min=2 and max=10."  step="2" type="number" name="course_quiz_total_marks" min="2" max="10" class="form-control"  pattern="[0-9]+" title="Please enter a number between 0 and 100" required>
    
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <input value="Submit" name="submit" type="submit" class="btn btn-submit me-2">
                                    <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'javascript:history.go(-1)'; ?>" class="btn btn-cancel">Cancel</a>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive" style="<?php echo (mysqli_num_rows($resultSelectQuiz) == 0)? "display:none;":"";?>">
                            <table class="table datanew">
                                <thead>
                                    <tr>
                                        <th>Quiz Name</th>
                                        <th>Question(s)</th>
                                        <th>Mark(s)</th>
                                        <?php
                                            if($rowSelectQuiz["quiz_questions_marked"] == 0){
                                                ?>
                                                <th>Action</th>
                                                <?php
                                            }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        
                                        <td><?php echo $rowSelectQuiz["quiz_name"];?></td>
                                        <td><?php echo $rowSelectQuiz["quiz_total_questions"];?></td>
                                        <td><?php echo $rowSelectQuiz["quiz_total_marks"];?></td>
                                        <?php
                                            if($rowSelectQuiz["quiz_questions_marked"] == 0){
                                                ?>
                                                    <td>
                                                        <a class="me-3" <?php echo "href='quiz-add.php?course=" . $quiz_for_course . "&quiz=" . $rowSelectQuiz["quiz_id"] . "'"?>>
                                                            <img src="assets/img/icons/edit.svg" alt="img">
                                                        </a>
                                                    </td>
                                                <?php
                                            }
                                        ?>
                                    </tr>                            
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <script src="assets/js/feather.min.js"></script>

    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>

    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/plugins/select2/js/select2.min.js"></script>

    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>

    <script src="assets/js/script.js"></script>
</body>

</html>