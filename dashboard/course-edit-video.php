<?php
require '../includes/scripts/connection.php';
session_start();

if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')) {
    $user_id = $_SESSION['educat_logedin_user_id'];
    $query = "SELECT * FROM user_master WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $userdata = mysqli_fetch_assoc($result);
    $user_role = $userdata["role"];
    if($user_role != 1 && $user_role != 2) {
        header("Location: ../404.php"); exit();
    }
}else{
    header("Location: ../sign-in.php"); exit();
}

$course  = (int)$_GET["course"];
$chapter = (int)$_GET["chapter"];
$video   = (int)$_GET["video"];

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $chapterVideoTitle = mysqli_real_escape_string($conn, $_POST['course_chapter_video_title']);

    if(!empty($_FILES["course_chapter_video_path"]["name"])) {
        // New video uploaded — replace old one
        $targetDirectory = "course/uploads/videos/";
        $newFileName = $userdata["user_name"] . "_" . $chapterVideoTitle . "_" . uniqid() . "_" . basename($_FILES["course_chapter_video_path"]["name"]);
        $targetFilePath = $targetDirectory . $newFileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        if($_FILES["course_chapter_video_path"]["error"] > 0) {
            $_SESSION['educat_error_message'] = "Upload Error: " . $_FILES["course_chapter_video_path"]["error"];
        }else{
            $allowedVideoTypes = ["mp4", "webm", "mkv", "mov", "avi"];
            if(in_array(strtolower($fileType), $allowedVideoTypes)) {
                if(move_uploaded_file($_FILES["course_chapter_video_path"]["tmp_name"], "../" . $targetFilePath)) {
                    // Delete old video file
                    $oldQ = mysqli_query($conn, "SELECT video_path FROM videos_master WHERE video_id=$video");
                    $oldD = mysqli_fetch_assoc($oldQ);
                    if($oldD && file_exists("../" . $oldD["video_path"])) { @unlink("../" . $oldD["video_path"]); }

                    $updateQuery = "UPDATE videos_master SET video_name='$chapterVideoTitle', video_path='$targetFilePath' WHERE video_id=$video";
                    if(mysqli_query($conn, $updateQuery)){
                        $_SESSION['educat_success_message'] = "Video lesson updated successfully!";
                    }else{
                        $_SESSION['educat_error_message'] = "Error updating database: " . mysqli_error($conn);
                    }
                }else{
                    $_SESSION['educat_error_message'] = "Error uploading video file. Check server permissions.";
                }
            }else{
                $_SESSION['educat_error_message'] = "Only MP4, WebM, MKV, MOV, AVI video files are allowed.";
            }
        }
    }else{
        // No new video — update title only
        $updateQuery = "UPDATE videos_master SET video_name='$chapterVideoTitle' WHERE video_id=$video";
        if(mysqli_query($conn, $updateQuery)){
            $_SESSION['educat_success_message'] = "Video title updated successfully!";
        }else{
            $_SESSION['educat_error_message'] = "Error: " . mysqli_error($conn);
        }
    }

    header("Location: course-chapter-videos-list.php?course=$course&chapter=$chapter");
    exit();
}

$sql = "SELECT * FROM videos_master WHERE video_id=$video";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

$course_q = mysqli_query($conn, "SELECT course_title FROM course_master WHERE course_id=$course");
$course_data = mysqli_fetch_assoc($course_q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Video Lesson — EduCat Admin</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
    <style>
        .video-drop { border: 2px dashed #c7d2fe; border-radius: 16px; padding: 2rem; text-align: center; background: #f5f7ff; cursor: pointer; transition: all 0.3s; }
        .video-drop:hover { border-color: #6366f1; background: #eef2ff; }
        .video-drop input { display: none; }
        #newVideoPreview { width: 100%; border-radius: 12px; display: none; margin-top: 1rem; }
    </style>
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
                    <li class="breadcrumb-item"><a href="course-chapter-list.php?course=<?php echo $course; ?>" class="text-decoration-none"><?php echo htmlspecialchars($course_data['course_title'] ?? 'Course'); ?></a></li>
                    <li class="breadcrumb-item"><a href="course-chapter-videos-list.php?course=<?php echo $course; ?>&chapter=<?php echo $chapter; ?>" class="text-decoration-none">Videos</a></li>
                    <li class="breadcrumb-item active">Edit Lesson</li>
                </ol>
            </nav>

            <div class="ins-page-header">
                <div>
                    <h2 class="ins-page-title">Edit Video Lesson</h2>
                    <p class="ins-page-subtitle">Updating: <strong><?php echo htmlspecialchars($row['video_name']); ?></strong></p>
                </div>
                <a href="course-chapter-videos-list.php?course=<?php echo $course; ?>&chapter=<?php echo $chapter; ?>" class="ins-btn ins-btn-outline">
                    <i class="fas fa-arrow-left me-2"></i> Back to Lessons
                </a>
            </div>

            <div class="row g-4 justify-content-center">
                <div class="col-lg-8">

                    <!-- Current Video Preview -->
                    <?php if(!empty($row['video_path'])): ?>
                    <div class="ins-card mb-4">
                        <div class="ins-card__header">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-film me-2 text-primary"></i> Current Video</h6>
                        </div>
                        <div class="ins-card__body">
                            <video controls class="w-100 rounded-4" style="max-height: 300px; object-fit: contain; background: #000;">
                                <source src="../<?php echo $row['video_path']; ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Edit Form -->
                    <div class="ins-card">
                        <div class="ins-card__header">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-edit me-2 text-primary"></i> Edit Video Details</h6>
                        </div>
                        <div class="ins-card__body">
                            <form method="post" action="" enctype="multipart/form-data" id="editForm">
                                <div class="mb-4">
                                    <label class="ins-form-label">Lesson Title <span class="text-danger">*</span></label>
                                    <input type="text" name="course_chapter_video_title" class="ins-form-control" value="<?php echo htmlspecialchars($row['video_name']); ?>" required>
                                </div>

                                <div class="mb-4">
                                    <label class="ins-form-label">Replace Video (Optional)</label>
                                    <div class="video-drop" id="videoDropZone" onclick="document.getElementById('videoFileInput').click()">
                                        <input type="file" id="videoFileInput" name="course_chapter_video_path" accept="video/*">
                                        <i class="fas fa-exchange-alt fs-4 text-muted mb-2 d-block"></i>
                                        <p class="fw-600 mb-1 text-muted">Click to upload a new video</p>
                                        <p class="smallest text-muted mb-0">Leave empty to keep current video</p>
                                        <p id="newFileName" class="smallest text-primary mt-2 fw-bold"></p>
                                    </div>
                                    <video id="newVideoPreview" controls></video>
                                </div>

                                <div class="d-flex gap-3">
                                    <button type="submit" name="submit" class="ins-btn ins-btn-primary px-5 py-3">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                    <a href="course-chapter-videos-list.php?course=<?php echo $course; ?>&chapter=<?php echo $chapter; ?>" class="ins-btn ins-btn-outline py-3 px-4">Cancel</a>
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

    document.getElementById('videoFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file) {
            document.getElementById('newFileName').textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
            const url = URL.createObjectURL(file);
            const vid = document.getElementById('newVideoPreview');
            vid.src = url;
            vid.style.display = 'block';
        }
    });
</script>
</body>
</html>