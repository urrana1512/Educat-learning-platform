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
    <title>Course Reviews</title>

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/EduCat (4)_rm.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/bootstrap-datetimepicker.min.css">

    <link rel="stylesheet" href="assets/css/animate.css">

    <link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">

    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<style>
    .star{
        font-size: 2rem;
        color: orange;
        font-weight: bold;
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


        <!-- Sidebar START -->
        <?php
            include("sidebar.php");
        ?>
        <!-- Sidebar END -->


        <div class="page-wrapper">
            <div class="content">
                <div class="page-header">
                    <div class="page-title">
                        <h4>Course Reviews</h4>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <?php
                                $sqlQuery = ($user_role === "1")? "SELECT cm.course_id, cm.course_name, cm.course_image, cm.course_description, cm.course_purchases, cm.course_completion_number, cm.course_instructor, cm.course_rating, um.user_name
                                FROM course_master cm
                                JOIN user_master um ON cm.course_instructor = um.user_id" : "SELECT cm.course_id, cm.course_name, cm.course_image, cm.course_description, cm.course_purchases, cm.course_completion_number, cm.course_instructor, cm.course_rating, um.user_name
             FROM course_master cm
             JOIN user_master um ON cm.course_instructor = um.user_id
             WHERE um.user_id = $user_id";

                                $result = mysqli_query($conn, $sqlQuery);
                                if ($result) {
                                    if(mysqli_num_rows($result) == 0){
                                        echo "No course found.";
                                        die();
                                    }
                                    // Iterate through the results and print options
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        ?>
                                            <div class="col-12 col-md-6 col-lg-4 d-flex">
                                                <a href="#" class="card flex-fill bg-white br">
                                                    <img alt="Card Image" src="../<?php echo $row["course_image"];?>" class="card-img-top">
                                                    <!-- <div class="card-header">
                                                        <h5 class="card-title mb-0">Card with image and list</h5>
                                                    </div> -->
                                                    <ul class="list-group list-group-flush">
                                                        <li class="list-group-item" title="Title of course addressed by course instructor."><b>Name: </b><?php echo $row["course_name"];?></li>
                                                        <li class="list-group-item" title="Numbe of students purchased this course."><b>Purchases: </b><?php echo $row["course_purchases"];?></li>
                                                        <li class="list-group-item" title="Number of Students Completed this course."><b>Graduation Count: </b><?php echo $row["course_completion_number"];?></li>
                                                        <li class="list-group-item"><b>Instructor: </b><?php echo $row["user_name"];?></li>
                                                        <li class="list-group-item star"><?php
                                                        if($row["course_rating"] > 0){
                                                            for ($i=0; $i <  $row["course_rating"]; $i++) { 
                                                                echo "&#9733";
                                                            }
                                                        }else{
                                                            echo "&#9733";
                                                        }
                                                        ?></li>
                                                    </ul>
                                                </a>
                                            </div>
                            <?php
                                    }
                                } else {
                                    echo "Error fetching values from the database: " . mysqli_error($conn);
                                }
                            ?>
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

    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <script src="assets/plugins/select2/js/select2.min.js"></script>

    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>

    <script src="assets/js/script.js"></script>
</body>

</html>