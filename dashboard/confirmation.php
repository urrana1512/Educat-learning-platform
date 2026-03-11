<?php
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
    
    if(!isset($_GET['course']) || (!isset($_GET['course']) && !isset($_GET['chapter']))){
        header("Location: ../404.php");
    }else{
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'Unknown';
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="description" content="POS - Bootstrap Admin Template">
    <meta name="keywords"
        content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern,  html5, responsive">
    <meta name="author" content="Dreamguys - Bootstrap Admin Template">
    <meta name="robots" content="noindex, nofollow">
    <title>Dashboard</title>

    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/EduCat (4)_rm.png">

    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" href="assets/css/animate.css">

    <link rel="stylesheet" href="assets/css/dataTables.bootstrap4.min.css">

    <link rel="stylesheet" href="assets/plugins/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">

    <link rel="stylesheet" href="./assets/css/style.css">

</head>
<style>
    .tac{
        text-align: center;
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
                <a href="aindex.php" class="logo-small">
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
           

            <!-- <div class="dropdown mobile-user-menu">
                <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="aprofile.php">My Profile</a>
                    <a class="dropdown-item" href="generalsettings.php">Settings</a>
                    <a class="dropdown-item" href="#">Logout</a>
                </div>
            </div> -->

        </div>

        <!-- Sidebar START -->
        <?php
            include("sidebar.php");
        ?>
        <!-- Sidebar END -->
        
        <div class="page-wrapper">
    <div class="content">
        <?php
            if(isset($_GET["course"]) && !isset($_GET['chapter']) && !isset($_GET['video'])){
                ?>
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-lg-5" style="height: auto; padding: 15px; background-color: white; box-shadow: 0px 0px 12px 2px gainsboro; border-radius: 15px;">
                            <div class="content d-flex justify-content-center align-items-center" style="margin: 0; padding: 0;">
                                <h2 style="font-weight: bold;">Are you sure to delete?</h2>
                            </div>
                            <div class="content d-flex justify-content-center align-items-center">
                                <p style="text-align: justify;">After deleting a course, all your chapters and their assoicated videos will be deleted too. These changes can not be undone once deleted.</p>
                            </div>
                            <div class="conbtns d-flex justify-content-center align-items-center" style="gap: 10px;">
                                <a href="course-delete.php?course=<?php echo $_GET["course"];?>" class="btn btn-submit">Yes Delete</a>
                                <a href="course-list.php" class="btn btn-cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                <?php
            }
            if(isset($_GET['course']) && isset($_GET['chapter']) && !isset($_GET['video'])){
                ?>
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-lg-5" style="height: auto; padding: 15px; background-color: white; box-shadow: 0px 0px 12px 2px gainsboro; border-radius: 15px;">
                            <div class="content d-flex justify-content-center align-items-center" style="margin: 0; padding: 0;">
                                <h2 style="font-weight: bold;">Are you sure to delete?</h2>
                            </div>
                            <div class="content d-flex justify-content-center align-items-center">
                                <p style="text-align: justify;">After deleting a chapter, all assoicated videos with this chapter will be deleted too. These changes can not be undone once deleted.</p>
                            </div>
                            <div class="conbtns d-flex justify-content-center align-items-center" style="gap: 10px;">
                                <a href="course-delete.php?course=<?php echo $_GET["course"] . "&chapter=" . $_GET['chapter'];?>" class="btn btn-submit">Yes Delete</a>
                                <a href="course-list.php" class="btn btn-cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                <?php
            }
            if(isset($_GET['course']) && isset($_GET['chapter']) && isset($_GET['video'])){
                ?>
                    <div class="row d-flex justify-content-center align-items-center">
                        <div class="col-lg-5" style="height: auto; padding: 15px; background-color: white; box-shadow: 0px 0px 12px 2px gainsboro; border-radius: 15px;">
                            <div class="content d-flex justify-content-center align-items-center" style="margin: 0; padding: 0;">
                                <h2 style="font-weight: bold;">Are you sure to delete?</h2>
                            </div>
                            <div class="content d-flex justify-content-center align-items-center">
                                <p style="text-align: justify;">After deleting a video, These changes can not be undone once deleted.</p>
                            </div>
                            <div class="conbtns d-flex justify-content-center align-items-center" style="gap: 10px;">
                                <a href="course-delete.php?course=<?php echo $_GET["course"] . "&chapter=" . $_GET['chapter'] . "&video=" . $_GET['video'];?>" class="btn btn-submit">Yes Delete</a>
                                <a href="course-list.php" class="btn btn-cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                <?php
            }
        ?>
    </div>
</div>
    </div>


    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <script src="assets/js/feather.min.js"></script>

    <script src="assets/js/jquery.slimscroll.min.js"></script>

    <script src="assets/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/dataTables.bootstrap4.min.js"></script>

    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <script src="assets/plugins/apexchart/apexcharts.min.js"></script>
    <script src="assets/plugins/apexchart/chart-data.js"></script>

    <script src="assets/js/script.js"></script>
</body>

</html>