<?php
// header.php — Top navbar for instructor panel
// $userdata must be set in the parent page before including this.
?>
<header class="ins-header">
    <div class="ins-header__left">
        <button class="ins-header__mobile-toggle" id="ins-mobile-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="ins-header__breadcrumb">
            <span>Instructor Panel</span>
        </div>
    </div>
    <div class="ins-header__right">
        <!-- Notifications -->
        <a href="notifications.php" class="ins-header__action-btn" title="Notifications">
            <i class="fas fa-bell"></i>
            <span class="ins-header__badge"></span>
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
                    <div class="ins-profile-btn__role">Instructor</div>
                </div>
                <i class="fas fa-chevron-down" style="font-size:.65rem;color:var(--text-muted);margin-left:4px;"></i>
            </button>
            <div class="ins-dropdown-menu" id="ins-profile-menu">
                <a href="profile.php"><i class="fas fa-user-circle"></i> Profile Settings</a>
                <a href="../myaccount.php"><i class="fas fa-graduation-cap"></i> Student Dashboard</a>
                <div class="divider"></div>
                <a href="../logout.php" style="color:var(--danger);"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</header>
