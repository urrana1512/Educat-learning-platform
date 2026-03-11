<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    $course_id  = isset($_GET["course"])  ? intval($_GET["course"])  : 0;
    $chapter_id = isset($_GET["chapter"]) ? intval($_GET["chapter"]) : 0;
    if($course_id == 0 || $chapter_id == 0){ header("Location: course-curriculum.php?course=$course_id"); exit; }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $video_title = mysqli_real_escape_string($conn, $_POST['video_title']);
        $video_type  = $_POST['content_type'];

        if(empty($video_title) || empty($_FILES["upload_video"]["name"])){
            $_SESSION["upload_error"] = "All fields are required.";
        } else {
            $target_dir = "course/uploads/course_videos/";
            if(!file_exists("../".$target_dir)) mkdir("../".$target_dir, 0777, true);

            $file_name  = uniqid()."_".basename($_FILES["upload_video"]["name"]);
            $target_path = $target_dir.$file_name;
            $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_ext  = $video_type == 'video' ? ["mp4","webm","m4v"] : ["pdf","zip","txt"];
            $max_size     = $video_type == 'video' ? 5*1024*1024*1024 : 100*1024*1024;

            if(in_array($file_ext, $allowed_ext) && $_FILES["upload_video"]["size"] <= $max_size){
                if(move_uploaded_file($_FILES["upload_video"]["tmp_name"], "../".$target_path)){
                    $insertQuery = "INSERT INTO `videos_master`(`video_name`,`video_path`,`video_plays`,`video_of_chapter`) VALUES ('$video_title','$target_path',0,'$chapter_id')";
                    if(mysqli_query($conn, $insertQuery)){
                        mysqli_query($conn, "UPDATE course_chapter_list SET chapter_total_videos = chapter_total_videos + 1 WHERE course_chapter_id = '$chapter_id'");
                        mysqli_query($conn, "UPDATE course_master SET course_videos = course_videos + 1 WHERE course_id = '$course_id'");
                        
                        // Notify students
                        $c_name_query = mysqli_query($conn, "SELECT course_name FROM course_master WHERE course_id = $course_id");
                        $course_name_fetch = mysqli_fetch_assoc($c_name_query)['course_name'] ?? 'Course';
                        $ins_name = $userdata['user_name'];
                        $notif_title = "New Lesson Uploaded!";
                        $notif_msg = "$ins_name uploaded a new lesson '$video_title' to '$course_name_fetch'.";
                        
                        $enrolled = mysqli_query($conn, "SELECT user_id FROM purchased_course_master WHERE course_id = $course_id");
                        while($es = mysqli_fetch_assoc($enrolled)){
                            $sid = $es['user_id'];
                            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($sid, '$notif_title', '$notif_msg', 'course')");
                        }
                        
                        $_SESSION["curriculum_success"] = "Lesson added successfully.";
                        header("location: course-curriculum.php?course=$course_id"); exit;
                    } else { $_SESSION["upload_error"] = "Database error."; }
                } else { $_SESSION["upload_error"] = "Failed to upload the file."; }
            } else { $_SESSION["upload_error"] = "Invalid file type or size. Allowed: ".implode(", ", $allowed_ext); }
        }
    }

    // Get chapter name
    $chap_row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT course_chapter_name FROM course_chapter_list WHERE course_chapter_id = $chapter_id"));
    $chapter_name = $chap_row['course_chapter_name'] ?? 'Section';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Lesson — EduCat Instructor</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="../dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("includes/sidebar.php"); ?>
    <div class="ins-main">
        <?php include("includes/header.php"); ?>
        <div class="ins-content">

            <div class="ins-page-header">
                <div>
                    <h1 class="ins-page-title">Add Lesson</h1>
                    <p class="ins-page-subtitle"><?php echo htmlspecialchars($chapter_name); ?></p>
                </div>
                <a href="course-curriculum.php?course=<?php echo $course_id; ?>" class="ins-btn ins-btn-outline"><i class="fas fa-arrow-left"></i> Back to Curriculum</a>
            </div>

            <?php if(isset($_SESSION["upload_error"])): ?>
            <div style="background:var(--danger-light);color:var(--danger);padding:13px 18px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION["upload_error"]; unset($_SESSION["upload_error"]); ?>
            </div>
            <?php endif; ?>

            <div style="max-width:700px;">
                <div class="ins-card">
                    <div class="ins-card__header"><h4 class="ins-card__title">Lesson Details</h4></div>
                    <div class="ins-card__body">
                        <form method="post" action="" enctype="multipart/form-data" id="uploadForm">
                            <div class="ins-form-group">
                                <label class="ins-form-label">Lesson Title <span class="required">*</span></label>
                                <input type="text" name="video_title" class="ins-form-control" placeholder="e.g. Setting up the Environment" required>
                            </div>

                            <div class="ins-form-group">
                                <label class="ins-form-label">Content Type</label>
                                <select name="content_type" class="ins-form-control" onchange="updateAcceptType(this.value)">
                                    <option value="video">Video Lecture (MP4, WebM)</option>
                                    <option value="resource">Document / Resource (PDF, ZIP, TXT)</option>
                                </select>
                            </div>

                            <div class="ins-form-group">
                                <label class="ins-form-label">Upload File <span class="required">*</span></label>
                                <div class="ins-upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                                    <div class="ins-upload-area__icon" id="uploadIcon"><i class="fas fa-cloud-upload-alt"></i></div>
                                    <div class="ins-upload-area__title">Drag & drop or click to browse</div>
                                    <div class="ins-upload-area__hint" id="fileDesc">MP4 or WebM &bull; Max 5GB</div>
                                </div>
                                <input type="file" name="upload_video" id="fileInput" accept="video/mp4,video/webm" style="display:none;" required onchange="showFileName(this)">
                                <p id="fileNameDisplay" style="margin-top:10px;font-size:.875rem;font-weight:600;color:var(--secondary);display:none;"></p>

                                <!-- Progress bar -->
                                <div id="progressWrap" style="display:none;margin-top:16px;">
                                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;font-size:.8rem;color:var(--text-muted);">
                                        <span>Uploading...</span><span id="pct">0%</span>
                                    </div>
                                    <div class="ins-progress"><div class="ins-progress-bar" id="progressBar" style="width:0%;transition:width .3s;"></div></div>
                                </div>
                            </div>

                            <div style="display:flex;gap:12px;margin-top:8px;">
                                <a href="course-curriculum.php?course=<?php echo $course_id; ?>" class="ins-btn ins-btn-outline">Cancel</a>
                                <button type="button" class="ins-btn ins-btn-primary" onclick="startUpload()"><i class="fas fa-cloud-upload-alt"></i> Upload Lesson</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="ins-card mt-20">
                    <div class="ins-card__header"><h4 class="ins-card__title"><i class="fas fa-lightbulb" style="color:#F59E0B;"></i> Upload Tips</h4></div>
                    <div class="ins-card__body">
                        <ul style="margin:0;padding-left:20px;color:var(--text-muted);font-size:.875rem;line-height:2;">
                            <li>Videos should be in <strong>MP4</strong> or <strong>WebM</strong> format for best compatibility.</li>
                            <li>Keep videos under <strong>30 minutes</strong> for better student completion rates.</li>
                            <li>Add a descriptive lesson title so students know what they'll learn.</li>
                            <li>Use resources (PDFs) for supplementary material and code snippets.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function updateAcceptType(type){
    var ip = document.getElementById('fileInput');
    var d  = document.getElementById('fileDesc');
    if(type==='video'){ ip.accept='video/mp4,video/webm'; d.textContent='MP4 or WebM • Max 5GB'; }
    else              { ip.accept='.pdf,.zip,.txt';       d.textContent='PDF, ZIP, TXT • Max 100MB'; }
}

function showFileName(input){
    if(input.files && input.files[0]){
        var el = document.getElementById('fileNameDisplay');
        el.style.display='block';
        el.innerHTML='<i class="fas fa-check-circle" style="color:var(--secondary);"></i> '+input.files[0].name;
    }
}

function startUpload(){
    var title = document.querySelector('input[name="video_title"]').value;
    var fi    = document.getElementById('fileInput');
    if(!title || !fi.files.length){ alert('Please fill all required fields.'); return; }
    var pw = document.getElementById('progressWrap');
    var pb = document.getElementById('progressBar');
    var pt = document.getElementById('pct');
    pw.style.display = 'block';
    var p = 0;
    var iv = setInterval(function(){
        p += Math.random()*18+6;
        if(p>=100){ p=100; clearInterval(iv); document.getElementById('uploadForm').submit(); }
        pb.style.width = p+'%';
        pt.textContent = Math.floor(p)+'%';
    }, 400);
}

// Drag-drop
var ua = document.getElementById('uploadArea');
ua.addEventListener('dragover', function(e){ e.preventDefault(); ua.classList.add('dragover'); });
ua.addEventListener('dragleave', function(){ ua.classList.remove('dragover'); });
ua.addEventListener('drop', function(e){
    e.preventDefault(); ua.classList.remove('dragover');
    var file = e.dataTransfer.files[0];
    if(!file) return;
    showFileName({files:[file]});
});
</script>
<?php include("includes/footer.php"); ?>
</body>
</html>
