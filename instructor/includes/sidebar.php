<?php
$currentURL  = $_SERVER['PHP_SELF'];
$currentPage = basename($currentURL);

// Check if any Courses subpage is active
$coursesActive = in_array($currentPage, [
    'my-courses.php','create-course.php','course-curriculum.php',
    'content-upload.php','quiz-management.php','quiz-add-information.php','edit-course.php'
]);
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

            <!-- Courses (submenu) -->
            <li class="ins-nav-item">
                <a href="javascript:void(0);" class="ins-nav-link ins-submenu-toggle <?php echo $coursesActive ? 'open' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-book-open"></i></span>
                    <span>Courses</span>
                    <span class="ins-arrow" style="margin-left:auto;font-size:.7rem;transition:transform .25s;<?php echo $coursesActive ? 'transform:rotate(90deg);' : ''; ?>"><i class="fas fa-chevron-right"></i></span>
                </a>
                <ul class="ins-submenu <?php echo $coursesActive ? 'open' : ''; ?>" style="<?php echo $coursesActive ? '' : 'display:none;'; ?>">
                    <li>
                        <a href="my-courses.php" class="ins-nav-link <?php echo in_array($currentPage,['my-courses.php','edit-course.php']) ? 'active' : ''; ?>">
                            My Courses
                        </a>
                    </li>
                    <li>
                        <a href="create-course.php" class="ins-nav-link <?php echo ($currentPage=='create-course.php') ? 'active' : ''; ?>">
                            Create Course
                        </a>
                    </li>
                    <li>
                        <a href="course-curriculum.php" class="ins-nav-link <?php echo in_array($currentPage,['course-curriculum.php','content-upload.php']) ? 'active' : ''; ?>">
                            Course Content
                        </a>
                    </li>
                    <li>
                        <a href="quiz-management.php" class="ins-nav-link <?php echo in_array($currentPage,['quiz-management.php','quiz-add-information.php']) ? 'active' : ''; ?>">
                            Quizzes
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Students -->
            <li class="ins-nav-item">
                <a href="students.php" class="ins-nav-link <?php echo ($currentPage=='students.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-users"></i></span>
                    <span>Students</span>
                </a>
            </li>

            <!-- Reviews -->
            <li class="ins-nav-item">
                <a href="reviews.php" class="ins-nav-link <?php echo ($currentPage=='reviews.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-star"></i></span>
                    <span>Reviews</span>
                </a>
            </li>

            <!-- Analytics -->
            <li class="ins-nav-item">
                <a href="analytics.php" class="ins-nav-link <?php echo ($currentPage=='analytics.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-chart-line"></i></span>
                    <span>Analytics</span>
                </a>
            </li>

            <!-- Messages -->
            <li class="ins-nav-item">
                <a href="messages.php" class="ins-nav-link <?php echo ($currentPage=='messages.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-comment-alt"></i></span>
                    <span>Messages</span>
                </a>
            </li>

            <!-- Notifications -->
            <li class="ins-nav-item">
                <a href="notifications.php" class="ins-nav-link <?php echo ($currentPage=='notifications.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-bell"></i></span>
                    <span>Notifications</span>
                </a>
            </li>

            <!-- Profile -->
            <li class="ins-nav-item">
                <a href="profile.php" class="ins-nav-link <?php echo ($currentPage=='profile.php') ? 'active' : ''; ?>">
                    <span class="ins-nav-icon"><i class="fas fa-user-circle"></i></span>
                    <span>Profile</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- User info at bottom -->
    <div class="ins-sidebar__user">
        <img src="../<?php echo $userdata['user_profile_photo']; ?>" alt="" class="ins-avatar ins-avatar-sm" onerror="this.src='../assets/img/EduCat (4).png'">
        <div class="ins-sidebar__user-info">
            <div class="ins-sidebar__user-name"><?php echo htmlspecialchars($userdata['user_name']); ?></div>
            <div class="ins-sidebar__user-role">Instructor</div>
        </div>
    </div>
</aside>
