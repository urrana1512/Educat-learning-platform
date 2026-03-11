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


    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $title = $_POST['course_title'];
        $language = $_POST['course_langauge'];
        $price = $_POST['course_price'];
        $discount = $_POST['course_discount'];
        $category = $_POST['course_category'];
        $desc = $_POST['course_description'];
        $currentDate = date("d-m-Y");

        if(empty($title) || empty($language) || empty($price) || empty($category) || empty($discount) || empty($desc) || empty($_FILES["upload_course_photo"]["name"]) || empty($_FILES["upload_course_demo_video"]["name"])){
            $_SESSION["photo_success_message"] = "All fields are required.";
        }        
        if (!empty($_FILES["upload_course_photo"]["name"])) {
            $target_dir = "course/uploads/course_featured_image/";
            $file_name = uniqid() . "_" . basename($_FILES["upload_course_photo"]["name"]);
            $target_path = $target_dir . $file_name;
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = array("jpg", "jpeg", "png");
            $max_file_size = 2 * 1024 * 1024; // 2 MB
    
            if (in_array($file_extension, $allowed_extensions) && $_FILES["upload_course_photo"]["size"] <= $max_file_size) {
                // First, insert course information into the database
                $insertQuery = "INSERT INTO `course_master`(`course_name`, `course_instructor`, `course_description`, `course_language`,`course_price`, `course_discount`, `course_category`, `course_lanuched_on`, `course_last_updated_on`) VALUES ('$title','$user_id','$desc','$language','$price',$discount,'$category','$currentDate','$currentDate')";
                $result = mysqli_query($conn, $insertQuery);
                if ($result) {
                    // If course information added successfully, update the image path
                    if (move_uploaded_file($_FILES["upload_course_photo"]["tmp_name"], "../" . $target_path)) {
                        // Update the course image path in the database
                        $course_id = mysqli_insert_id($conn); // Get the last inserted course ID
                        $updateQuery = "UPDATE `course_master` SET `course_image`='$target_path' WHERE `course_id`='$course_id'";
                        if(mysqli_query($conn, $updateQuery)){
                            if (!empty($_FILES["upload_course_demo_video"]["name"])) {
                                $target_dir = "course/uploads/course_featured_video/";
                                $file_name = uniqid() . "_" . basename($_FILES["upload_course_demo_video"]["name"]);
                                $target_path = $target_dir . $file_name;
                                $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                                $allowed_extensions = array("mp4", "webm", "m4v", "ogg");
                                $max_file_size = 5 * 1024 * 1024 * 1024; // 5 GB
                        
                                if (in_array($file_extension, $allowed_extensions) && $_FILES["upload_course_demo_video"]["size"] <= $max_file_size) {
                                    // If course information added successfully, update the image path
                                    if (move_uploaded_file($_FILES["upload_course_demo_video"]["tmp_name"], "../" . $target_path)) {
                                        // Update the course image path in the database 
                                        $updateQuery = "UPDATE `course_master` SET `course_demo_lecture`='$target_path' WHERE `course_id`='$course_id'";
                                        if(mysqli_query($conn, $updateQuery)){
                                            $_SESSION["educat_success_message"] = "Success! Course is created.";
                                            header("location: course-list.php");
                                        }
                    
                                    } else {
                                        // If image upload fails, delete the previously added course information
                                        $deleteQuery = "DELETE FROM `course_master` WHERE `course_id`='$course_id'";
                                        if( mysqli_query($conn, $deleteQuery)){
                                            $_SESSION["educat_error_message"] = "Failed to upload the video file.";
                                            header("location: course-list.php");
                                        }
                                    }
                                } else {
                                    $_SESSION["educat_error_message"] = "Invalid file type or size. Please upload a JPG, JPEG, or PNG file (up to 5 GB).";
                                    header("location: course-list.php");
                                }
                            } else {
                                $_SESSION["educat_error_message"] = "Error in uploading Video.";
                                header("location: course-list.php");
                            }
                        }

                    } else {
                        // If image upload fails, delete the previously added course information
                        $deleteQuery = "DELETE FROM `course_master` WHERE `course_id`='$course_id'";
                        if( mysqli_query($conn, $deleteQuery)){
                            $_SESSION["educat_error_message"] = "Failed to upload the file.";
                            header("location: course-list.php");
                        }
                    }
                } else {
                    $_SESSION["educat_error_message"] = "Error inserting course information into the database.";
                    header("location: course-list.php");
                }
            } else {
                $_SESSION["educat_error_message"] = "Invalid file type or size. Please upload a JPG, JPEG, or PNG file (up to 2 MB).";
                header("location: course-list.php");
            }
        } else {
            $_SESSION["educat_error_message"] = "Error in uploading Photo.";
            header("location: course-list.php");
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
    <title>Add Course</title>

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
                        <h4>Add course</h4>
                        <?php
                            if(isset($_SESSION["educat_error_message"])){
                                ?>
                                <a style="color: red;"><?php echo $_SESSION["educat_error_message"]?></a>
                                <?php
                                unset($_SESSION["educat_error_message"]);
                            }
                            if(isset($_SESSION["educat_success_message"])){
                                ?>
                                <a style="color: green;"><?php echo $_SESSION["educat_success_message"]?></a>
                                <?php
                                unset($_SESSION["educat_success_message"]);
                            }
                        ?>
                    </div>
                </div>
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Title</label>
                                        <input type="text" name="course_title" required>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Course Language</label>
                                        <select class="select" name="course_langauge" required>
                                            <option value="0" disabled selected>--SELECT--</option>
                                            <?php
                                                $selectQuery = "SELECT * FROM `languages_master`";
                                                $result = mysqli_query($conn, $selectQuery);
                                                // Check if there are results
                                                if ($result) {
                                                    // Iterate through the results and print options
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        ?>
                                                        <option value='<?php echo $row["language_id"];?>'><?php echo $row["language_name"];?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    echo "Error fetching values from the database: " . mysqli_error($conn);
                                                }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Price</label>
                                        <input name="course_price" min="0" max="10000000" type="number" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Discount</label>
                                        <input type="number" name="course_discount" min="0" max="100" class="form-control"  pattern="[0-9]+" title="Please enter a number between 0 and 100" required>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-sm-6 col-12">
                                    <div class="form-group">
                                        <label>Course Category</label>
                                        <select class="select" name="course_category" required>
                                        <option value="0" disabled selected>--SELECT--</option>
                                        <optgroup label="Your Categories:">
                                            <?php
                                                $selectQuery = "SELECT * FROM `course_category_master` WHERE `course_category_owner` = $user_id";
                                                $result = mysqli_query($conn, $selectQuery);
                                                // Check if there are results
                                                if ($result) {
                                                    // Iterate through the results and print options
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        ?>
                                                        <option value='<?php echo $row["course_category_id"];?>'><?php echo $row["course_category_name"];?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    echo "Error fetching values from the database: " . mysqli_error($conn);
                                                }
                                            ?>
                                            </optgroup>
                                        <optgroup label="Other Categories:">
                                            <?php
                                                $selectQuery = "SELECT * FROM `course_category_master` WHERE `course_category_owner` != $user_id";
                                                $result = mysqli_query($conn, $selectQuery);
                                                // Check if there are results
                                                if ($result) {
                                                    // Iterate through the results and print options
                                                    while ($row = mysqli_fetch_assoc($result)) {
                                                        ?>
                                                        <option value='<?php echo $row["course_category_id"];?>'><?php echo $row["course_category_name"];?></option>
                                                        <?php
                                                    }
                                                } else {
                                                    echo "Error fetching values from the database: " . mysqli_error($conn);
                                                }
                                            ?>
                                            </optgroup>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <textarea rows="4" cols="50" name="course_description" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Featured Image</label>
                                        <input name="upload_course_photo" type="file" class="form-control" accept="image/*" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label>Demo Lecture</label>
                                        <input name="upload_course_demo_video" type="file" class="form-control" accept="video/*" required>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <input value="Submit" name="submit" type="submit" class="btn btn-submit me-2">
                                    <!-- <input value="Create Course" name="submit" type="submit" class="btn btn-submit me-2"> -->
                                    <a href="index.php" class="btn btn-cancel">Cancel</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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

    <script src="assets/js/moment.min.js"></script>
    <script src="assets/js/bootstrap-datetimepicker.min.js"></script>

    <script src="assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <script src="assets/plugins/sweetalert/sweetalerts.min.js"></script>

    <script src="assets/js/script.js"></script>
</body>

</html>