<?php
    require '../includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
        if($user_role != 1 && $user_role != 2){
            header("Location: ../404.php"); exit();
        }
    }else{
        header("Location: ../sign-in.php"); exit();
    }

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET["course"])) {
        $courseChapterName = mysqli_real_escape_string($conn, $_POST['course_chapter_name']);
        $course_chapter_description = mysqli_real_escape_string($conn, $_POST['course_chapter_description']);
        $course = (int)$_GET['course'];

        $checkChapterQuery = "SELECT * FROM course_chapter_list WHERE course_id = $course AND course_chapter_name = '$courseChapterName'";
        $checkChapterResult = mysqli_query($conn, $checkChapterQuery);

        if(mysqli_num_rows($checkChapterResult) > 0) {
            $_SESSION["educat_error_message"] = "A chapter with the same name already exists for this course.";
            header("Location: course-chapter-list.php?course=" . $course);
        } else {
            $insertChapterQuery = "INSERT INTO course_chapter_list (course_chapter_name, course_id, course_chapter_description) VALUES ('$courseChapterName', $course, '$course_chapter_description')";
            $insertChapterResult = mysqli_query($conn, $insertChapterQuery);
            if($insertChapterResult) {
                mysqli_query($conn, "UPDATE course_master SET course_chapters = course_chapters + 1 WHERE course_id = $course");
                $_SESSION["educat_success_message"] = "Chapter added successfully!";
            } else {
                $_SESSION["educat_error_message"] = "Error adding chapter. " . mysqli_error($conn);
            }
            header("Location: course-chapter-list.php?course=" . $course);
        }
        exit();
    }

    $course_id = (int)$_GET['course'];
    $course_q = mysqli_query($conn, "SELECT course_title FROM course_master WHERE course_id = $course_id");
    $course_data = mysqli_fetch_assoc($course_q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Chapter — EduCat Admin</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("sidebar.php"); ?>
    <div class="ins-main">
        <?php include("header.php"); ?>
        <div class="ins-content">

            <div class="ins-page-header">
                <div>
                    <h2 class="ins-page-title">Add New Chapter</h2>
                    <p class="ins-page-subtitle">Adding a chapter to <strong><?php echo htmlspecialchars($course_data['course_title'] ?? 'Course'); ?></strong></p>
                </div>
                <a href="course-chapter-list.php?course=<?php echo $course_id; ?>" class="ins-btn ins-btn-outline">
                    <i class="fas fa-arrow-left me-2"></i> Back to Chapters
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="ins-card">
                        <div class="ins-card__header">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-layer-group me-2 text-primary"></i> Chapter Details</h6>
                        </div>
                        <div class="ins-card__body">
                            <form method="post" action="">
                                <div class="mb-4">
                                    <label class="ins-form-label">Chapter Name <span class="text-danger">*</span></label>
                                    <input type="text" name="course_chapter_name" class="ins-form-control" placeholder="e.g. Introduction to HTML" required>
                                </div>
                                <div class="mb-4">
                                    <label class="ins-form-label">Chapter Description <span class="text-danger">*</span></label>
                                    <textarea name="course_chapter_description" class="ins-form-control" rows="5" placeholder="Briefly describe what students will learn in this chapter..." required></textarea>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="submit" name="submit" class="ins-btn ins-btn-primary px-5 py-3">
                                        <i class="fas fa-plus me-2"></i> Add Chapter
                                    </button>
                                    <a href="course-chapter-list.php?course=<?php echo $_GET['course']; ?>" class="ins-btn ins-btn-outline py-3 px-4">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('mobile_btn').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('active');
    });
</script>
</body>
</html>