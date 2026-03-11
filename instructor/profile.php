<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $name  = mysqli_real_escape_string($conn, $_POST['user_name']);
        $email = mysqli_real_escape_string($conn, $_POST['user_email']);
        $pass_update = "";
        if(!empty($_POST['user_password'])){
            $pass = mysqli_real_escape_string($conn, $_POST['user_password']);
            $pass_update = ", `user_password`='$pass'";
        }
        $updateQuery = "UPDATE `user_master` SET `user_name`='$name', `user_email`='$email' $pass_update WHERE `user_id`=$user_id";
        if(mysqli_query($conn, $updateQuery)){
            $_SESSION["profile_success"] = "Profile updated successfully!";
            if(!empty($_FILES["profile_photo"]["name"])){
                $target_dir  = "dashboard/assets/img/user/";
                $file_name   = uniqid() . "_" . basename($_FILES["profile_photo"]["name"]);
                $target_path = $target_dir . $file_name;
                if(move_uploaded_file($_FILES["profile_photo"]["tmp_name"], "../" . $target_path)){
                    mysqli_query($conn, "UPDATE `user_master` SET `user_profile_photo`='$target_path' WHERE `user_id`=$user_id");
                }
            }
            header("Location: profile.php"); exit;
        } else {
            $_SESSION["profile_error"] = "Error updating profile.";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings — EduCat Instructor</title>
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
                    <h1 class="ins-page-title">Profile Settings</h1>
                    <p class="ins-page-subtitle">Update your personal information and account details</p>
                </div>
            </div>

            <?php if(isset($_SESSION["profile_success"])): ?>
            <div style="background:#D1FAE5;color:#065F46;padding:14px 20px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION["profile_success"]; unset($_SESSION["profile_success"]); ?>
            </div>
            <?php endif; ?>
            <?php if(isset($_SESSION["profile_error"])): ?>
            <div style="background:var(--danger-light);color:var(--danger);padding:14px 20px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION["profile_error"]; unset($_SESSION["profile_error"]); ?>
            </div>
            <?php endif; ?>

            <form method="post" action="" enctype="multipart/form-data">
                <div style="display:grid;grid-template-columns:280px 1fr;gap:24px;align-items:start;">

                    <!-- Profile Banner Card -->
                    <div class="ins-card" style="text-align:center;">
                        <div class="ins-card__body">
                            <div style="position:relative;display:inline-block;margin-bottom:16px;">
                                <img src="../<?php echo $userdata['user_profile_photo']; ?>" alt="" id="avatarPreview" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:4px solid var(--primary-light);" onerror="this.src='../assets/img/EduCat (4).png'">
                                <label for="photoUpload" style="position:absolute;bottom:2px;right:2px;width:32px;height:32px;border-radius:50%;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid white;font-size:.8rem;" title="Change Photo">
                                    <i class="fas fa-camera"></i>
                                </label>
                                <input type="file" id="photoUpload" name="profile_photo" accept="image/*" style="display:none;" onchange="previewImage(this)">
                            </div>
                            <h4 style="font-weight:700;margin:0 0 4px;font-family:'Poppins',sans-serif;"><?php echo htmlspecialchars($userdata['user_name']); ?></h4>
                            <p style="color:var(--text-muted);margin:0 0 16px;font-size:.875rem;">Instructor at EduCat</p>
                            <span class="ins-badge ins-badge-primary"><i class="fas fa-chalkboard-teacher"></i> Instructor</span>

                            <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border-light);">
                                <a href="../myaccount.php" class="ins-btn ins-btn-outline w-100" style="justify-content:center;">
                                    <i class="fas fa-graduation-cap"></i> Student View
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Info Form Card -->
                    <div class="ins-card">
                        <div class="ins-card__header">
                            <h4 class="ins-card__title">Personal Information</h4>
                        </div>
                        <div class="ins-card__body">
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                                <div class="ins-form-group">
                                    <label class="ins-form-label">Full Name <span class="required">*</span></label>
                                    <input type="text" name="user_name" class="ins-form-control" value="<?php echo htmlspecialchars($userdata['user_name']); ?>" required>
                                </div>
                                <div class="ins-form-group">
                                    <label class="ins-form-label">Email Address <span class="required">*</span></label>
                                    <input type="email" name="user_email" class="ins-form-control" value="<?php echo htmlspecialchars($userdata['user_email']); ?>" required>
                                </div>
                            </div>

                            <div class="ins-form-group">
                                <label class="ins-form-label">New Password</label>
                                <input type="password" name="user_password" class="ins-form-control" placeholder="Leave blank to keep current password">
                                <p class="ins-form-hint">Minimum 8 characters recommended.</p>
                            </div>

                            <div class="ins-form-group">
                                <label class="ins-form-label">Instructor Bio / Expertise</label>
                                <textarea class="ins-form-control" rows="4" placeholder="Tell students about your experience and what you teach..."></textarea>
                            </div>

                            <div style="display:flex;gap:12px;padding-top:8px;">
                                <button type="submit" class="ins-btn ins-btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                <a href="index.php" class="ins-btn ins-btn-outline">Cancel</a>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<script>
function previewImage(input){
    if(input.files && input.files[0]){
        var r = new FileReader();
        r.onload = function(e){ document.getElementById('avatarPreview').src = e.target.result; }
        r.readAsDataURL(input.files[0]);
    }
}
</script>
<?php include("includes/footer.php"); ?>
</body>
</html>
