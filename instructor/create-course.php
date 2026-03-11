<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $title    = $_POST['course_title'];
        $language = $_POST['course_langauge'];
        $price    = $_POST['course_price'] ?? 0;
        $discount = $_POST['course_discount'] ?? 0;
        $category = $_POST['course_category'];
        $desc     = $_POST['course_description'];
        $currentDate = date("d-m-Y");
        $status = isset($_POST['publish_course']) ? 1 : 0;

        if(empty($title)||empty($language)||empty($category)||empty($desc)||empty($_FILES["upload_course_photo"]["name"])||empty($_FILES["upload_course_demo_video"]["name"])){
            $_SESSION["educat_error_message"] = "All fields are required.";
        } else {
            $target_dir = "course/uploads/course_featured_image/";
            $file_name  = uniqid()."_".basename($_FILES["upload_course_photo"]["name"]);
            $target_path = $target_dir.$file_name;
            $file_ext    = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if(in_array($file_ext, ["jpg","jpeg","png"]) && $_FILES["upload_course_photo"]["size"] <= 2*1024*1024){
                $insertQuery = "INSERT INTO `course_master`(`course_name`,`course_instructor`,`course_description`,`course_language`,`course_price`,`course_discount`,`course_category`,`course_lanuched_on`,`course_last_updated_on`,`course_approved_by_admin`) VALUES ('$title','$user_id','$desc','$language','$price',$discount,'$category','$currentDate','$currentDate',$status)";
                $result = mysqli_query($conn, $insertQuery);
                if($result){
                    $course_id = mysqli_insert_id($conn);
                    if(move_uploaded_file($_FILES["upload_course_photo"]["tmp_name"], "../dashboard/".$target_path)){
                        mysqli_query($conn, "UPDATE `course_master` SET `course_image`='$target_path' WHERE `course_id`='$course_id'");
                        $target_dir_v = "course/uploads/course_featured_video/";
                        $file_name_v  = uniqid()."_".basename($_FILES["upload_course_demo_video"]["name"]);
                        $target_path_v = $target_dir_v.$file_name_v;
                        $file_ext_v    = strtolower(pathinfo($file_name_v, PATHINFO_EXTENSION));
                        if(in_array($file_ext_v, ["mp4","webm","m4v","ogg"]) && $_FILES["upload_course_demo_video"]["size"] <= 5*1024*1024*1024){
                            if(move_uploaded_file($_FILES["upload_course_demo_video"]["tmp_name"], "../dashboard/".$target_path_v)){
                                mysqli_query($conn, "UPDATE `course_master` SET `course_demo_lecture`='$target_path_v' WHERE `course_id`='$course_id'");
                                
                                // Notify students if published
                                if ($status == 1) {
                                    $ins_name = $userdata['user_name'];
                                    $notif_title = "New Course from $ins_name!";
                                    $notif_msg = "$ins_name just published a new course: '$title'. Check it out now!";
                                    
                                    // Get all active students on the platform
                                    $stu_query = "SELECT user_id FROM user_master WHERE role = 1";
                                    $stu_res = mysqli_query($conn, $stu_query);
                                    if ($stu_res) {
                                        while ($sr = mysqli_fetch_assoc($stu_res)) {
                                            $sid = $sr['user_id'];
                                            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($sid, '$notif_title', '$notif_msg', 'course')");
                                        }
                                    }
                                }
                                
                                $_SESSION["educat_success_message"] = "Course created successfully!";
                                header("location: my-courses.php"); exit;
                            }
                        } else { $_SESSION["educat_error_message"] = "Invalid video file type or size."; }
                    } else { $_SESSION["educat_error_message"] = "Failed to upload image."; }
                } else { $_SESSION["educat_error_message"] = "Error saving course to database."; }
            } else { $_SESSION["educat_error_message"] = "Invalid image type or file too large (max 2MB)."; }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Course — EduCat Instructor</title>
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
                    <h1 class="ins-page-title">Create New Course</h1>
                    <p class="ins-page-subtitle">Fill in the details to publish your next course</p>
                </div>
                <a href="my-courses.php" class="ins-btn ins-btn-outline"><i class="fas fa-arrow-left"></i> Back to Courses</a>
            </div>

            <?php if(isset($_SESSION["educat_error_message"])): ?>
            <div style="background:var(--danger-light);color:var(--danger);padding:14px 20px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION["educat_error_message"]; unset($_SESSION["educat_error_message"]); ?>
            </div>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div style="display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;">

                    <!-- Main form card -->
                    <div class="ins-card">
                        <div class="ins-card__header"><h4 class="ins-card__title">Course Details</h4></div>
                        <div class="ins-card__body">
                            <div class="ins-form-group">
                                <label class="ins-form-label">Course Title <span class="required">*</span></label>
                                <input type="text" name="course_title" class="ins-form-control" placeholder="e.g. React Development Masterclass" required>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                                <div class="ins-form-group">
                                    <label class="ins-form-label">Language <span class="required">*</span></label>
                                    <select class="ins-form-control" name="course_langauge" required>
                                        <option value="" disabled selected>Select Language</option>
                                        <?php
                                        $r = mysqli_query($conn,"SELECT * FROM `languages_master`");
                                        if($r) while($row=mysqli_fetch_assoc($r)) echo "<option value='{$row['language_id']}'>{$row['language_name']}</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="ins-form-group">
                                    <label class="ins-form-label">Category <span class="required">*</span></label>
                                    <select class="ins-form-control" name="course_category" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <?php
                                        $r = mysqli_query($conn,"SELECT * FROM `course_category_master`");
                                        if($r) while($row=mysqli_fetch_assoc($r)) echo "<option value='{$row['course_category_id']}'>{$row['course_category_name']}</option>";
                                        ?>
                                    </select>
                                </div>
                                <div class="ins-form-group">
                                    <label class="ins-form-label">Difficulty Level</label>
                                    <select class="ins-form-control" name="course_difficulty">
                                        <option>Beginner</option>
                                        <option>Intermediate</option>
                                        <option>Advanced</option>
                                        <option>All Levels</option>
                                    </select>
                                </div>
                            </div>

                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                                <div class="ins-form-group">
                                    <label class="ins-form-label">Price ($) <span class="required">*</span></label>
                                    <input type="number" name="course_price" class="ins-form-control" placeholder="0 for Free" min="0" required>
                                </div>
                                <div class="ins-form-group">
                                    <label class="ins-form-label">Discount (%)</label>
                                    <input type="number" name="course_discount" class="ins-form-control" placeholder="e.g. 10" min="0" max="100">
                                </div>
                            </div>

                            <div class="ins-form-group">
                                <label class="ins-form-label">Description <span class="required">*</span></label>
                                <textarea name="course_description" class="ins-form-control" rows="5" placeholder="What will students learn in this course?" required></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Side panel -->
                    <div style="display:flex;flex-direction:column;gap:20px;">

                        <!-- Thumbnail upload -->
                        <div class="ins-card">
                            <div class="ins-card__header"><h4 class="ins-card__title">Course Thumbnail</h4></div>
                            <div class="ins-card__body">
                                <div class="ins-upload-area" id="thumbArea" onclick="document.getElementById('upload_course_photo').click()">
                                    <div class="ins-upload-area__icon"><i class="fas fa-image"></i></div>
                                    <div class="ins-upload-area__title">Click to upload</div>
                                    <div class="ins-upload-area__hint">JPG, PNG &bull; Max 2MB &bull; 1280×720px recommended</div>
                                    <img id="thumbPreview" style="max-width:100%;margin-top:12px;border-radius:8px;display:none;">
                                </div>
                                <input type="file" id="upload_course_photo" name="upload_course_photo" accept="image/*" style="display:none;" required onchange="previewThumb(this)">
                            </div>
                        </div>

                        <!-- Promo video upload -->
                        <div class="ins-card">
                            <div class="ins-card__header"><h4 class="ins-card__title">Promo Video</h4></div>
                            <div class="ins-card__body">
                                <div class="ins-upload-area" id="vidArea" onclick="document.getElementById('upload_course_demo_video').click()">
                                    <div class="ins-upload-area__icon"><i class="fas fa-video"></i></div>
                                    <div class="ins-upload-area__title">Click to upload video</div>
                                    <div class="ins-upload-area__hint">MP4, WebM &bull; Max 5GB</div>
                                </div>
                                <input type="file" id="upload_course_demo_video" name="upload_course_demo_video" accept="video/*" style="display:none;" required onchange="vidSelected(this)">
                                <p id="vidName" style="font-size:.8rem;color:var(--text-muted);margin-top:8px;text-align:center;"></p>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="ins-card">
                            <div class="ins-card__body" style="display:flex;flex-direction:column;gap:12px;">
                                <button type="submit" name="save_draft" class="ins-btn ins-btn-outline" style="justify-content:center;width:100%;"><i class="fas fa-save"></i> Save as Draft</button>
                                <button type="submit" name="publish_course" class="ins-btn ins-btn-primary" style="justify-content:center;width:100%;"><i class="fas fa-paper-plane"></i> Publish Course</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function previewThumb(input){
    if(input.files && input.files[0]){
        var r = new FileReader();
        r.onload = function(e){
            var img = document.getElementById('thumbPreview');
            img.src = e.target.result;
            img.style.display = 'block';
        };
        r.readAsDataURL(input.files[0]);
    }
}
function vidSelected(input){
    if(input.files && input.files[0]){
        document.getElementById('vidName').textContent = '✓ ' + input.files[0].name;
    }
}
// Drag-drop
['thumbArea','vidArea'].forEach(function(id){
    var el = document.getElementById(id);
    if(!el) return;
    el.addEventListener('dragover', function(e){ e.preventDefault(); el.classList.add('dragover'); });
    el.addEventListener('dragleave', function(){ el.classList.remove('dragover'); });
    el.addEventListener('drop', function(e){
        e.preventDefault(); el.classList.remove('dragover');
        var file = e.dataTransfer.files[0];
        if(!file) return;
        if(id==='thumbArea'){ var fi = document.getElementById('upload_course_photo'); previewThumb({files:[file]}); }
        else { document.getElementById('vidName').textContent = '✓ ' + file.name; }
    });
});
</script>
<?php include("includes/footer.php"); ?>
</body>
</html>
