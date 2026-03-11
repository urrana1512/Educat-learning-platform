<?php
// Self-contained: fetch $userdata from session if not already set
if(!isset($userdata)) {
    if(!isset($conn)) require '../includes/scripts/connection.php';
    if(isset($_SESSION['educat_logedin_user_id'])){
        $userdata = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
    } else {
        $userdata = ['user_name'=>'Admin','user_email'=>'admin@educat.com','user_profile_photo'=>'assets/img/EduCat (4).png'];
    }
}
$pending_courses = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM course_master WHERE course_approved_by_admin=0"))['c'] ?? 0;
?>
<header class="ins-header">
    <div class="ins-header__left">
        <button class="ins-header__mobile-toggle" id="ins-mobile-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="ins-header__breadcrumb">
            <span>Admin Panel</span>
        </div>
    </div>

    <div class="ins-header__right">
        <!-- Notifications -->
        <a href="course-list.php?status=0" class="ins-header__action-btn" title="Pending Course Approvals">
            <i class="fas fa-bell"></i>
            <?php if($pending_courses > 0): ?>
            <span class="ins-header__badge"></span>
            <?php endif; ?>
        </a>

        <!-- Messages -->
        <a href="messages.php" class="ins-header__action-btn" title="Messages">
            <i class="fas fa-comment-alt"></i>
        </a>

        <!-- Profile Dropdown -->
        <div class="ins-dropdown" id="ins-profile-dropdown">
            <button class="ins-profile-btn" id="ins-profile-btn">
                <img src="../<?php echo $userdata['user_profile_photo']; ?>" alt="" onerror="this.src='../assets/img/EduCat (4).png'">
                <div class="ins-profile-btn__info">
                    <div class="ins-profile-btn__name"><?php echo htmlspecialchars(explode(' ', $userdata['user_name'])[0]); ?></div>
                    <div class="ins-profile-btn__role">Admin</div>
                </div>
                <i class="fas fa-chevron-down" style="font-size:.65rem;color:var(--text-muted);margin-left:4px;"></i>
            </button>
            <div class="ins-dropdown-menu" id="ins-profile-menu">
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <div class="divider"></div>
                <a href="../logout.php" style="color:var(--danger);"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</header>

<script>
// Profile dropdown toggle
var profileBtn  = document.getElementById('ins-profile-btn');
var profileMenu = document.getElementById('ins-profile-menu');
if(profileBtn && profileMenu) {
    profileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        profileMenu.classList.toggle('show');
    });
    document.addEventListener('click', function() {
        if(profileMenu) profileMenu.classList.remove('show');
    });
}
</script>