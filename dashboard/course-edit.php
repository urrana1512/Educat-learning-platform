<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$id = (int)$_GET['courseId'];

$sql = "SELECT c.*, cat.category_name, l.language_name 
        FROM course_master c 
        LEFT JOIN category_master cat ON c.category_id = cat.category_id
        LEFT JOIN languages_master l ON c.course_language = l.language_id
        WHERE c.course_id = $id";
$result = mysqli_query($conn, $sql);
$rowdata = mysqli_fetch_assoc($result);

$categories = mysqli_query($conn, "SELECT * FROM category_master ORDER BY category_name");
$languages  = mysqli_query($conn, "SELECT * FROM languages_master ORDER BY language_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course — EduCat Admin</title>
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
                    <h2 class="ins-page-title">Edit Course</h2>
                    <p class="ins-page-subtitle">Update course information and content for <strong><?php echo htmlspecialchars($rowdata['course_title']); ?></strong></p>
                </div>
                <a href="course-list.php" class="ins-btn ins-btn-outline">
                    <i class="fas fa-arrow-left me-2"></i> Back to Courses
                </a>
            </div>

            <?php if(isset($_SESSION['educat_error_message'])): ?>
                <div class="alert alert-danger border-0 rounded-4 mb-24">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $_SESSION['educat_error_message']; unset($_SESSION['educat_error_message']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['educat_success_message'])): ?>
                <div class="alert alert-success border-0 rounded-4 mb-24">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $_SESSION['educat_success_message']; unset($_SESSION['educat_success_message']); ?>
                </div>
            <?php endif; ?>

            <form action="course-edit-script.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="courseId" value="<?php echo $id; ?>">
                <div class="row g-4">

                    <!-- Left Column -->
                    <div class="col-lg-8">
                        <div class="ins-card">
                            <div class="ins-card__header">
                                <h6 class="mb-0 fw-bold">Course Information</h6>
                            </div>
                            <div class="ins-card__body">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="ins-form-label">Course Title <span class="text-danger">*</span></label>
                                        <input type="text" name="course_title" class="ins-form-control" value="<?php echo htmlspecialchars($rowdata['course_name'] ?? $rowdata['course_title'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ins-form-label">Course Language <span class="text-danger">*</span></label>
                                        <select name="course_langauge" class="ins-form-control" required>
                                            <option value="" disabled>-- Select Language --</option>
                                            <?php while($lang = mysqli_fetch_assoc($languages)): ?>
                                                <option value="<?php echo $lang['language_id']; ?>" <?php if($lang['language_id'] == $rowdata['course_language']) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($lang['language_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ins-form-label">Category <span class="text-danger">*</span></label>
                                        <select name="course_category" class="ins-form-control" required>
                                            <option value="" disabled>-- Select Category --</option>
                                            <?php while($cat = mysqli_fetch_assoc($categories)): ?>
                                                <option value="<?php echo $cat['category_id']; ?>" <?php if($cat['category_id'] == ($rowdata['category_id'] ?? $rowdata['course_category'])) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($cat['category_name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ins-form-label">Price (USD) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0 fw-bold text-muted">$</span>
                                            <input type="number" name="course_price" min="0" max="10000000" class="ins-form-control border-start-0" value="<?php echo $rowdata['course_price']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="ins-form-label">Discount (%)</label>
                                        <input type="number" name="course_discount" min="0" max="100" class="ins-form-control" value="<?php echo $rowdata['course_discount']; ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="ins-form-label">Description <span class="text-danger">*</span></label>
                                        <textarea rows="6" name="course_description" class="ins-form-control" required><?php echo htmlspecialchars($rowdata['course_description']); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-lg-4">
                        <div class="ins-card mb-4">
                            <div class="ins-card__header">
                                <h6 class="mb-0 fw-bold">Course Media</h6>
                            </div>
                            <div class="ins-card__body">
                                <div class="mb-4">
                                    <label class="ins-form-label">Featured Image</label>
                                    <div class="mb-3">
                                        <img src="../<?php echo $rowdata['course_banner_image']; ?>" class="w-100 rounded-4" style="height: 150px; object-fit: cover;" onerror="this.style.display='none'">
                                    </div>
                                    <input type="file" name="upload_course_photo" class="ins-form-control" accept="image/*">
                                    <p class="smallest text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Leave empty to keep current image.</p>
                                </div>

                                <div>
                                    <label class="ins-form-label">Demo Video</label>
                                    <?php if(!empty($rowdata['course_demo_video'])): ?>
                                        <video class="w-100 rounded-4 mb-2" style="height: 110px;" controls>
                                            <source src="../<?php echo $rowdata['course_demo_video']; ?>" type="video/mp4">
                                        </video>
                                    <?php endif; ?>
                                    <input type="file" name="upload_course_demo_video" class="ins-form-control" accept="video/*">
                                    <p class="smallest text-muted mt-2"><i class="fas fa-info-circle me-1"></i> Leave empty to keep current video.</p>
                                </div>
                            </div>
                        </div>

                        <div class="ins-card">
                            <div class="ins-card__body">
                                <div class="d-grid gap-2">
                                    <button type="submit" name="submit" class="ins-btn ins-btn-primary py-3">
                                        <i class="fas fa-save me-2"></i> Save Changes
                                    </button>
                                    <a href="course-list.php" class="ins-btn ins-btn-outline py-3 text-center">Discard Changes</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

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