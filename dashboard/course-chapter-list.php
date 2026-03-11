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

    $courseIDforCHAPTER = (int)$_GET["course"]; 

    // Fetch course info
    $course_q = mysqli_query($conn, "SELECT * FROM course_master WHERE course_id = $courseIDforCHAPTER");
    $course_data = mysqli_fetch_assoc($course_q);

    // Fetch chapters with video counts
    $selectQuery = "SELECT ccl.*, 
        (SELECT COUNT(*) FROM videos_master WHERE video_of_chapter = ccl.course_chapter_id) as video_count
        FROM course_chapter_list ccl 
        WHERE ccl.course_id = '$courseIDforCHAPTER'";
    $result = mysqli_query($conn, $selectQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Curriculum — EduCat Admin</title>
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

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb small text-muted">
                    <li class="breadcrumb-item"><a href="course-list.php" class="text-decoration-none">Course Catalog</a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($course_data['course_title'] ?? 'Course'); ?></li>
                </ol>
            </nav>

            <div class="ins-page-header">
                <div>
                    <h2 class="ins-page-title">Course Curriculum</h2>
                    <p class="ins-page-subtitle">
                        Managing chapters for <strong><?php echo htmlspecialchars($course_data['course_title'] ?? 'N/A'); ?></strong>
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="quiz-add-information.php?course=<?php echo $courseIDforCHAPTER; ?>" class="ins-btn ins-btn-outline">
                        <i class="fas fa-question-circle me-2"></i> Manage Quiz
                    </a>
                    <a href="course-add-chapter.php?course=<?php echo $courseIDforCHAPTER; ?>" class="ins-btn ins-btn-primary">
                        <i class="fas fa-plus me-2"></i> Add Chapter
                    </a>
                    <a href="course-list.php" class="ins-btn ins-btn-outline">
                        <i class="fas fa-arrow-left me-2"></i> Back to Courses
                    </a>
                </div>
            </div>

            <?php if(isset($_SESSION['educat_success_message'])): ?>
                <div class="alert alert-success border-0 rounded-4 mb-24">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['educat_success_message']; unset($_SESSION['educat_success_message']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['educat_error_message'])): ?>
                <div class="alert alert-danger border-0 rounded-4 mb-24">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['educat_error_message']; unset($_SESSION['educat_error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Chapter Name</th>
                                    <th>Description</th>
                                    <th>Video Lessons</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0):
                                    $indexNumber = 1;
                                    while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="text-muted fw-bold"><?php echo $indexNumber++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="ins-stat-icon ins-stat--blue" style="width: 42px; height: 42px; border-radius: 12px; flex-shrink:0;">
                                                <i class="fas fa-layer-group"></i>
                                            </div>
                                            <span class="fw-600"><?php echo htmlspecialchars($row['course_chapter_name']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted smallest"><?php echo htmlspecialchars(substr($row['course_chapter_description'], 0, 80)); ?>...</span>
                                    </td>
                                    <td>
                                        <span class="ins-badge ins-badge-primary"><?php echo $row['video_count']; ?> videos</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="course-chapter-videos-list.php?course=<?php echo $courseIDforCHAPTER; ?>&chapter=<?php echo $row['course_chapter_id']; ?>" class="ins-btn-icon text-primary" title="Manage Videos">
                                                <i class="fas fa-video"></i>
                                            </a>
                                            <a href="course-edit-chapter.php?course=<?php echo $courseIDforCHAPTER; ?>&chapter=<?php echo $row['course_chapter_id']; ?>" class="ins-btn-icon" title="Edit Chapter">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="confirmation.php?course=<?php echo $row['course_id'] . "&chapter=" . $row['course_chapter_id']; ?>" class="ins-btn-icon text-danger" title="Delete Chapter" onclick="return confirm('This will delete the chapter and all its videos. Continue?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile;
                                else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-layer-group fs-2 mb-3 d-block opacity-25"></i>
                                            No chapters yet. <a href="course-add-chapter.php?course=<?php echo $courseIDforCHAPTER; ?>">Add the first chapter</a> to build the curriculum.
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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