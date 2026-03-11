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
    $chapterIDforVIDEO = (int)$_GET["chapter"]; 

    // Fetch chapter info
    $chap_q = mysqli_query($conn, "SELECT * FROM chapter_master WHERE chapter_id = $chapterIDforVIDEO");
    $chap_data = mysqli_fetch_assoc($chap_q);

    // Fetch course info
    $course_q = mysqli_query($conn, "SELECT course_title FROM course_master WHERE course_id = $courseIDforCHAPTER");
    $course_data = mysqli_fetch_assoc($course_q);

    // Fetch videos
    $selectQuery = "SELECT * FROM videos_master WHERE video_of_chapter = '$chapterIDforVIDEO'";
    $result = mysqli_query($conn, $selectQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Lessons — EduCat Admin</title>
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
                    <li class="breadcrumb-item"><a href="course-list.php" class="text-decoration-none">Courses</a></li>
                    <li class="breadcrumb-item"><a href="course-chapter-list.php?course=<?php echo $courseIDforCHAPTER; ?>" class="text-decoration-none"><?php echo htmlspecialchars($course_data['course_title'] ?? 'Course'); ?></a></li>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($chap_data['chapter_name'] ?? 'Chapter'); ?></li>
                </ol>
            </nav>

            <div class="ins-page-header">
                <div>
                    <h2 class="ins-page-title">Video Lessons</h2>
                    <p class="ins-page-subtitle">
                        Chapter: <strong><?php echo htmlspecialchars($chap_data['chapter_name'] ?? 'N/A'); ?></strong>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="course-chapter-list.php?course=<?php echo $courseIDforCHAPTER; ?>" class="ins-btn ins-btn-outline">
                        <i class="fas fa-arrow-left me-2"></i> Back to Chapters
                    </a>
                    <a href="course-add-video.php?course=<?php echo $courseIDforCHAPTER; ?>&chapter=<?php echo $chapterIDforVIDEO; ?>" class="ins-btn ins-btn-primary">
                        <i class="fas fa-plus me-2"></i> Add Video Lesson
                    </a>
                </div>
            </div>

            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success border-0 rounded-4 mb-24">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php
                        $msgs = ['deleted'=>'Video lesson deleted successfully.'];
                        echo $msgs[$_GET['msg']] ?? 'Operation completed.';
                    ?>
                </div>
            <?php endif; ?>

            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">#</th>
                                    <th>Lesson Title</th>
                                    <th>Duration / Plays</th>
                                    <th>Preview</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($result) > 0):
                                    $index = 1;
                                    while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="text-muted fw-bold"><?php echo $index++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="ins-stat-icon ins-stat--primary" style="width: 42px; height: 42px; border-radius: 12px; flex-shrink:0;">
                                                <i class="fas fa-play"></i>
                                            </div>
                                            <span class="fw-600"><?php echo htmlspecialchars($row['video_name']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ins-badge ins-badge-muted"><?php echo $row['video_plays']; ?> plays</span>
                                    </td>
                                    <td>
                                        <?php if(!empty($row['video_path'])): ?>
                                            <video width="120" height="70" controls style="border-radius: 8px; object-fit: cover;">
                                                <source src="../<?php echo $row['video_path']; ?>" type="video/mp4">
                                            </video>
                                        <?php else: ?>
                                            <span class="text-muted smallest">No preview</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="course-edit-video.php?course=<?php echo $_GET["course"] . "&chapter=" . $_GET["chapter"] . "&video=" . $row["video_id"]; ?>" class="ins-btn-icon" title="Edit Lesson">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="confirmation.php?course=<?php echo $_GET["course"] . "&chapter=" . $_GET["chapter"] . "&video=" . $row["video_id"]; ?>" class="ins-btn-icon text-danger" title="Delete Lesson" onclick="return confirm('Delete this video lesson?')">
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
                                            <i class="fas fa-film fs-2 mb-3 d-block opacity-25"></i>
                                            No video lessons added yet. Click <strong>Add Video Lesson</strong> to get started.
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