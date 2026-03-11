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

    $course  = (int)$_GET["course"];
    $chapter = (int)$_GET["chapter"];

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $courseChapterName = mysqli_real_escape_string($conn, $_POST['course_chapter_name']);
        $course_chapter_description = mysqli_real_escape_string($conn, $_POST['course_chapter_description']);

        $updateQuery = "UPDATE course_chapter_list SET course_chapter_name='$courseChapterName', course_chapter_description='$course_chapter_description' WHERE course_chapter_id=$chapter AND course_id=$course";
        if(mysqli_query($conn, $updateQuery)){
            $_SESSION["educat_success_message"] = "Chapter updated successfully!";
        }else{
            $_SESSION["educat_error_message"] = "Error updating chapter.";
        }
        header("Location: course-chapter-list.php?course=$course");
        exit();
    }

    // Fetch existing chapter data
    $selectQuery = "SELECT * FROM course_chapter_list WHERE course_id=$course AND course_chapter_id=$chapter";
    $result = mysqli_query($conn, $selectQuery);
    $row = mysqli_fetch_assoc($result);

    // Fetch course name
    $course_q = mysqli_query($conn, "SELECT course_title FROM course_master WHERE course_id=$course");
    $course_data = mysqli_fetch_assoc($course_q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Chapter — EduCat Admin</title>
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
                    <h2 class="ins-page-title">Edit Chapter</h2>
                    <p class="ins-page-subtitle">Updating chapter in <strong><?php echo htmlspecialchars($course_data['course_title'] ?? 'Course'); ?></strong></p>
                </div>
                <a href="course-chapter-list.php?course=<?php echo $course; ?>" class="ins-btn ins-btn-outline">
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
                                    <input type="text" name="course_chapter_name" class="ins-form-control" value="<?php echo htmlspecialchars($row['course_chapter_name']); ?>" required>
                                </div>
                                <div class="mb-4">
                                    <label class="ins-form-label">Chapter Description <span class="text-danger">*</span></label>
                                    <textarea name="course_chapter_description" class="ins-form-control" rows="5" required><?php echo htmlspecialchars($row['course_chapter_description']); ?></textarea>
                                </div>
                                <div class="d-flex gap-3">
                                    <button type="submit" name="submit" class="ins-btn ins-btn-primary px-5 py-3">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                    <a href="course-chapter-list.php?course=<?php echo $course; ?>" class="ins-btn ins-btn-outline py-3 px-4">Cancel</a>
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