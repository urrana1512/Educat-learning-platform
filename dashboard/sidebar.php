<?php
$currentURL  = $_SERVER['PHP_SELF'];
$currentPage = basename($currentURL);

// Determine active groups
$coursePages    = ['course-list.php','course-edit.php','course-add.php','course-chapter-list.php','course-chapter-videos-list.php','course-add-chapter.php','course-edit-chapter.php','course-add-video.php','course-edit-video.php'];
$coursesActive  = in_array($currentPage, $coursePages);
$userPages      = ['users.php','instructors.php','students-list.php'];
$usersActive    = in_array($currentPage, $userPages);
?>
<!-- Sidebar Overlay (mobile) -->
<div id="ins-overlay" class="ins-sidebar-overlay"></div>

<aside class="ins-sidebar" id="ins-sidebar">

    <!-- Logo -->
    <div class="ins-sidebar__logo">
        <a href="index.php">
            <img src="../assets/img/EduCat (3).png" alt="EduCat" style="height:36px;">
        </a>
        <button id="ins-toggle" style="background:none;border:none;cursor:pointer;margin-left:auto;color:var(--text-muted);font-size:1.1rem;padding:4px;border-radius:6px;transition:var(--transition);" title="Collapse sidebar">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav class="ins-sidebar__nav">
        <ul style="list-style:none;padding:0;margin:0;">

            <!-- Dashboard -->
            <li class="ins-nav-item">
                <a href="index.php" class="ins-nav-link <?php echo ($currentPage=='index.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-th-large"></i></span>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Analytics -->
            <li class="ins-nav-item">
                <a href="analytics.php" class="ins-nav-link <?php echo ($currentPage=='analytics.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-chart-line"></i></span>
                    <span>Analytics</span>
                </a>
            </li>

            <!-- Users (submenu) -->
            <li class="ins-nav-item">
                <a href="javascript:void(0);" class="ins-nav-link ins-submenu-toggle <?php echo $usersActive ? 'open' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-users"></i></span>
                    <span>Users</span>
                    <span class="ins-arrow" style="margin-left:auto;font-size:.7rem;transition:transform .25s;<?php echo $usersActive ? 'transform:rotate(90deg);' : ''; ?>"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="ins-submenu <?php echo $usersActive ? 'open' : ''; ?>" style="<?php echo $usersActive ? '' : 'display:none;'; ?>">
                    <li><a href="users.php" class="ins-nav-link <?php echo ($currentPage=='users.php') ? 'active' : ''; ?>">All Users</a></li>
                    <li><a href="instructors.php" class="ins-nav-link <?php echo ($currentPage=='instructors.php') ? 'active' : ''; ?>">Instructors</a></li>
                    <li><a href="students-list.php" class="ins-nav-link <?php echo ($currentPage=='students-list.php') ? 'active' : ''; ?>">Students</a></li>
                </ul>
            </li>

            <!-- Courses (submenu) -->
            <li class="ins-nav-item">
                <a href="javascript:void(0);" class="ins-nav-link ins-submenu-toggle <?php echo $coursesActive ? 'open' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-book-open"></i></span>
                    <span>Courses</span>
                    <span class="ins-arrow" style="margin-left:auto;font-size:.7rem;transition:transform .25s;<?php echo $coursesActive ? 'transform:rotate(90deg);' : ''; ?>"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="ins-submenu <?php echo $coursesActive ? 'open' : ''; ?>" style="<?php echo $coursesActive ? '' : 'display:none;'; ?>">
                    <li><a href="course-list.php" class="ins-nav-link <?php echo ($currentPage=='course-list.php') ? 'active' : ''; ?>">All Courses</a></li>
                </ul>
            </li>

            <!-- Categories -->
            <li class="ins-nav-item">
                <a href="category-list.php" class="ins-nav-link <?php echo ($currentPage=='category-list.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-th-large"></i></span>
                    <span>Categories</span>
                </a>
            </li>

            <!-- Enrollments -->
            <li class="ins-nav-item">
                <a href="enrollments.php" class="ins-nav-link <?php echo ($currentPage=='enrollments.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span>Enrollments</span>
                </a>
            </li>

            <!-- Reviews -->
            <li class="ins-nav-item">
                <a href="reviews.php" class="ins-nav-link <?php echo ($currentPage=='reviews.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-star"></i></span>
                    <span>Reviews</span>
                </a>
            </li>

            <!-- Certificates -->
            <li class="ins-nav-item">
                <a href="certificates.php" class="ins-nav-link <?php echo ($currentPage=='certificates.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-certificate"></i></span>
                    <span>Certificates</span>
                </a>
            </li>

            <!-- Messages -->
            <li class="ins-nav-item">
                <a href="messages.php" class="ins-nav-link <?php echo ($currentPage=='messages.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-comment-alt"></i></span>
                    <span>Messages</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="ins-nav-item">
                <a href="settings.php" class="ins-nav-link <?php echo ($currentPage=='settings.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-cog"></i></span>
                    <span>Settings</span>
                </a>
            </li>

            <!-- Logout -->
            <li class="ins-nav-item">
                <a href="../logout.php" class="ins-nav-link" style="color: var(--danger);">
                    <span class="ins-nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                    <span>Logout</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- User info at bottom -->
    <?php
    if(!isset($userdata)) {
        if(!isset($conn)) require '../includes/scripts/connection.php';
        if(isset($_SESSION['educat_logedin_user_id'])){
            $userdata = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
        } else {
            $userdata = ['user_name'=>'Admin','user_email'=>'admin@educat.com','user_profile_photo'=>'assets/img/EduCat (4).png'];
        }
    }
    ?>
    <div class="ins-sidebar__user">
        <img src="../<?php echo $userdata['user_profile_photo']; ?>" alt="" class="ins-avatar ins-avatar-sm" onerror="this.src='../assets/img/EduCat (4).png'">
        <div class="ins-sidebar__user-info">
            <div class="ins-sidebar__user-name"><?php echo htmlspecialchars($userdata['user_name']); ?></div>
            <div class="ins-sidebar__user-role">Platform Admin</div>
        </div>
    </div>
</aside>

<script>
// Submenu toggle
document.querySelectorAll('.ins-submenu-toggle').forEach(function(btn) {
    btn.addEventListener('click', function() {
        this.classList.toggle('open');
        var arrow = this.querySelector('.ins-arrow');
        if(arrow) arrow.style.transform = this.classList.contains('open') ? 'rotate(90deg)' : '';
        var submenu = this.nextElementSibling;
        if(submenu && submenu.classList.contains('ins-submenu')) {
            submenu.style.display = submenu.style.display === 'none' ? 'block' : 'none';
        }
    });
});

// Mobile sidebar toggle
var overlay = document.getElementById('ins-overlay');
var sidebar  = document.getElementById('ins-sidebar');
var toggleBtn = document.getElementById('ins-toggle');
var mobileBtn = document.getElementById('ins-mobile-toggle');

if(toggleBtn) toggleBtn.addEventListener('click', function() {
    sidebar.classList.toggle('collapsed');
});
if(mobileBtn) mobileBtn.addEventListener('click', function() {
    sidebar.classList.add('open');
    if(overlay) overlay.classList.add('show');
});
if(overlay) overlay.addEventListener('click', function() {
    sidebar.classList.remove('open');
    overlay.classList.remove('show');
});
</script>