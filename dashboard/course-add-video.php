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

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"]) && isset($_GET["course"]) && isset($_GET["chapter"])) {
        if(!empty($_FILES["course_chapter_video_path"]["name"])) {
            $chapterVideoTitle = mysqli_real_escape_string($conn, $_POST['course_chapter_video_title']);
            $course  = (int)$_GET['course'];
            $chapter = (int)$_GET['chapter'];
        
            $targetDirectory = "course/uploads/videos/";
            $newFileName = $userdata["user_name"] . "_" . $chapterVideoTitle . "_" . uniqid() . "_" . basename($_FILES["course_chapter_video_path"]["name"]);
            $targetFilePath = $targetDirectory . $newFileName;
            $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
            if($_FILES["course_chapter_video_path"]["error"] > 0) {
                $_SESSION['educat_error_message'] = "Upload Error: " . $_FILES["course_chapter_video_path"]["error"];
            } else {
                $allowedVideoTypes = ["mp4", "webm", "mkv", "mov", "avi"];
                if(in_array(strtolower($fileType), $allowedVideoTypes)) {
                    if(move_uploaded_file($_FILES["course_chapter_video_path"]["tmp_name"], "../" . $targetFilePath)) {
                        $insertQuery = "INSERT INTO videos_master (video_name, video_path, video_of_chapter) VALUES ('$chapterVideoTitle', '$targetFilePath', $chapter)";
                        if(mysqli_query($conn, $insertQuery)) {
                            mysqli_query($conn, "UPDATE course_chapter_list SET chapter_total_videos = chapter_total_videos + 1 WHERE course_chapter_id = $chapter");
                            mysqli_query($conn, "UPDATE course_master SET course_total_lectures = course_total_lectures + 1 WHERE course_id = $course");
                            $_SESSION['educat_success_message'] = "Video lesson uploaded successfully!";
                        } else {
                            $_SESSION['educat_error_message'] = "Error saving to database: " . mysqli_error($conn);
                        }
                    } else {
                        $_SESSION['educat_error_message'] = "Error: Could not upload the video file. Check server permissions.";
                    }
                } else {
                    $_SESSION['educat_error_message'] = "Error: Only video files (MP4, WebM, MKV, MOV, AVI) are allowed.";
                }
            }
        } else {
            $_SESSION['educat_error_message'] = "Error: No file selected. Please choose a video file.";
        }

        header("Location: course-chapter-videos-list.php?course=" . $_GET['course'] . "&chapter=" . $_GET['chapter']);
        exit();
    }

    $course_id  = (int)$_GET['course'];
    $chapter_id = (int)$_GET['chapter'];

    $chap_q = mysqli_query($conn, "SELECT chapter_name, course_chapter_name FROM chapter_master WHERE chapter_id = $chapter_id UNION SELECT course_chapter_name, course_chapter_name FROM course_chapter_list WHERE course_chapter_id = $chapter_id LIMIT 1");
    $chap_data = mysqli_fetch_assoc($chap_q);

    $course_q = mysqli_query($conn, "SELECT course_title FROM course_master WHERE course_id = $course_id");
    $course_data = mysqli_fetch_assoc($course_q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Video Lesson — EduCat Admin</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
    <style>
        .video-drop { border: 2px dashed #c7d2fe; border-radius: 16px; padding: 3rem; text-align: center; background: #f5f7ff; cursor: pointer; transition: all 0.3s; }
        .video-drop:hover { border-color: #6366f1; background: #eef2ff; }
        .video-drop input { display: none; }
        #videoPreview { width: 100%; border-radius: 12px; display: none; margin-top: 1rem; }
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
                    <li class="breadcrumb-item"><a href="course-chapter-list.php?course=<?php echo $course_id; ?>" class="text-decoration-none"><?php echo htmlspecialchars($course_data['course_title'] ?? 'Course'); ?></a></li>
                    <li class="breadcrumb-item"><a href="course-chapter-videos-list.php?course=<?php echo $course_id; ?>&chapter=<?php echo $chapter_id; ?>" class="text-decoration-none">Videos</a></li>
                    <li class="breadcrumb-item active">Upload New</li>
                </ol>
            </nav>

            <div class="ins-page-header">
                <div>
                    <h2 class="ins-page-title">Upload Video Lesson</h2>
                    <p class="ins-page-subtitle">Add a new video to <strong><?php echo htmlspecialchars($course_data['course_title'] ?? 'Course'); ?></strong></p>
                </div>
                <a href="course-chapter-videos-list.php?course=<?php echo $course_id; ?>&chapter=<?php echo $chapter_id; ?>" class="ins-btn ins-btn-outline">
                    <i class="fas fa-arrow-left me-2"></i> Back to Lessons
                </a>
            </div>

            <?php if(isset($_SESSION['educat_error_message'])): ?>
                <div class="alert alert-danger border-0 rounded-4 mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['educat_error_message']; unset($_SESSION['educat_error_message']); ?>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <div class="ins-card">
                        <div class="ins-card__header">
                            <h6 class="mb-0 fw-bold"><i class="fas fa-video me-2 text-primary"></i> Video Upload</h6>
                        </div>
                        <div class="ins-card__body">
                            <form method="post" action="" enctype="multipart/form-data" id="uploadForm">
                                <div class="mb-4">
                                    <label class="ins-form-label">Lesson Title <span class="text-danger">*</span></label>
                                    <input type="text" name="course_chapter_video_title" class="ins-form-control" placeholder="e.g. Introduction to CSS Flexbox" required>
                                </div>
                                <div class="mb-4">
                                    <label class="ins-form-label">Video File <span class="text-danger">*</span></label>
                                    <div class="video-drop" id="videoDropZone" onclick="document.getElementById('videoFileInput').click()">
                                        <input type="file" id="videoFileInput" name="course_chapter_video_path" accept="video/*" required>
                                        <i class="fas fa-cloud-upload-alt fs-2 text-primary mb-3 d-block"></i>
                                        <p class="fw-bold mb-1">Click to upload or drag and drop</p>
                                        <p class="smallest text-muted mb-0">Supports MP4, WebM, MKV, MOV, AVI</p>
                                        <p id="fileName" class="smallest text-primary mt-2 fw-bold"></p>
                                    </div>
                                    <video id="videoPreview" controls></video>
                                </div>
                                
                                <!-- Upload progress -->
                                <div id="progressWrap" class="d-none mb-4">
                                    <label class="ins-form-label">Upload Progress</label>
                                    <div class="progress" style="height: 8px; border-radius: 50px;">
                                        <div id="progressBar" class="progress-bar bg-primary" style="width: 0%; border-radius: 50px; transition: width 0.3s;"></div>
                                    </div>
                                    <p id="progressText" class="smallest text-muted mt-2">0%</p>
                                </div>

                                <div class="d-flex gap-3">
                                    <button type="submit" name="submit" class="ins-btn ins-btn-primary px-5 py-3">
                                        <i class="fas fa-upload me-2"></i> Upload Lesson
                                    </button>
                                    <a href="course-chapter-videos-list.php?course=<?php echo $_GET['course']; ?>&chapter=<?php echo $_GET['chapter']; ?>" class="ins-btn ins-btn-outline py-3 px-4">Cancel</a>
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

    // File preview
    document.getElementById('videoFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if(file) {
            document.getElementById('fileName').textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
            const url = URL.createObjectURL(file);
            const vid = document.getElementById('videoPreview');
            vid.src = url;
            vid.style.display = 'block';
        }
    });

    // Simulate progress on form submit
    document.getElementById('uploadForm').addEventListener('submit', function() {
        document.getElementById('progressWrap').classList.remove('d-none');
        let prog = 0;
        const interval = setInterval(() => {
            prog = Math.min(prog + Math.random() * 15, 95);
            document.getElementById('progressBar').style.width = prog + '%';
            document.getElementById('progressText').textContent = Math.round(prog) + '% — Uploading...';
        }, 400);
    });
</script>
</body>
</html>