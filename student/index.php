<?php
include 'header.php';
include 'sidebar.php';

// Fetch Statistics
// Total Enrolled Courses
$enrolled_query = "SELECT COUNT(*) as total FROM purchased_course_master WHERE user_id = $user_id";
$enrolled_res = mysqli_query($conn, $enrolled_query);
$total_enrolled = mysqli_fetch_assoc($enrolled_res)['total'];

// Courses in Progress (Progress > 0 and < 100)
$progress_query = "SELECT COUNT(*) as total FROM purchased_course_master WHERE user_id = $user_id AND course_progress > 0 AND course_progress < 100";
$progress_res = mysqli_query($conn, $progress_query);
$total_in_progress = mysqli_fetch_assoc($progress_res)['total'];

// Completed Courses
$completed_query = "SELECT COUNT(*) as total FROM purchased_course_master WHERE user_id = $user_id AND course_completed = 1";
$completed_res = mysqli_query($conn, $completed_query);
$total_completed = mysqli_fetch_assoc($completed_res)['total'];

// Certificates Earned
$cert_query = "SELECT COUNT(*) as total FROM certificate_master WHERE certificate_student = $user_id";
$cert_res = mysqli_query($conn, $cert_query);
$total_certs = mysqli_fetch_assoc($cert_res)['total'];

// Fetch Recently Accessed Courses
$recent_query = "SELECT pc.*, c.course_name, c.course_image, u.user_name as instructor_name 
                 FROM purchased_course_master pc 
                 JOIN course_master c ON pc.course_id = c.course_id 
                 JOIN user_master u ON c.course_instructor = u.user_id 
                 WHERE pc.user_id = $user_id 
                 ORDER BY pc.purchase_date DESC LIMIT 3";
$recent_res = mysqli_query($conn, $recent_query);

// Fetch Available Courses (Discover)
$discover_query = "SELECT c.*, u.user_name as instructor_name, cat.course_category_name
                   FROM course_master c 
                   JOIN user_master u ON c.course_instructor = u.user_id 
                   LEFT JOIN course_category_master cat ON c.course_category = cat.course_category_id
                   WHERE c.course_approved_by_admin = 1 AND c.course_id NOT IN (SELECT course_id FROM purchased_course_master WHERE user_id = $user_id)
                   ORDER BY c.course_id DESC LIMIT 3";
$discover_res = mysqli_query($conn, $discover_query);

?>


<style>
    .main-content {
        margin-left: 260px;
        padding: 100px 30px 30px;
        transition: all 0.3s ease;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
        }
    }

    .welcome-card {
        background: linear-gradient(to right, #ffffff, #f1f5f9);
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
    }

    .welcome-text h1 {
        font-weight: 700;
        font-size: 1.8rem;
        margin-bottom: 10px;
    }

    .stat-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 24px;
        height: 100%;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 15px;
    }

    .stat-info h3 {
        font-weight: 700;
        margin: 0;
    }

    .stat-info p {
        color: var(--text-muted);
        margin: 0;
        font-size: 0.9rem;
    }

    .course-mini-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 20px;
        display: flex;
        padding: 12px;
        gap: 15px;
        transition: all 0.2s;
    }

    .course-mini-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .course-img-wrapper {
        width: 100px;
        height: 70px;
        border-radius: 10px;
        overflow: hidden;
    }

    .course-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .course-info-mini {
        flex: 1;
    }

    .course-info-mini h6 {
        margin: 0 0 5px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .course-info-mini p {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin: 0 0 8px;
    }

    /* Discover Course Card styles */
    .course-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        border-color: var(--primary-color);
    }

    .course-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        border-radius: 8px;
        font-weight: 600;
        color: var(--primary-color);
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .course-title-discover {
        font-weight: 700;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .section-title {
        font-weight: 700;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .progress-custom {
        height: 6px;
        border-radius: 10px;
        background: #f1f5f9;
        margin-top: 5px;
    }

    .progress-bar-custom {
        background: var(--primary-color);
        border-radius: 10px;
    }

    .announcement-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 20px;
    }

    .notif-item {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .notif-item:last-child {
        border-bottom: none;
    }

    .notif-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <!-- Welcome Section -->
        <div class="welcome-card">
            <div class="welcome-text">
                <h1>Hello, <?php echo explode(' ', $userdata['user_name'])[0]; ?>! 👋</h1>
                <p class="text-muted">It's a great day to learn something new. You have completed <b><?php echo $total_completed; ?></b> courses so far.</p>
                <a href="my-courses.php" class="btn btn-primary mt-2 px-4" style="border-radius: 10px;">Continue Learning</a>
            </div>
            <div class="welcome-img d-none d-md-block">
                <img src="../assets/img/EduCat (4)_rm.png" alt="Welcome" style="height: 120px;">
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="row g-4 mb-5">
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e0e7ff; color: #4338ca;">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_enrolled; ?></h3>
                        <p>Enrolled Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_in_progress; ?></h3>
                        <p>In Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dcfce7; color: #15803d;">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_completed; ?></h3>
                        <p>Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fae8ff; color: #a21caf;">
                        <i class="fa-solid fa-award"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $total_certs; ?></h3>
                        <p>Certificates</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Recent Courses -->
            <div class="col-lg-8">
                <div class="section-title">
                    <i class="fa-solid fa-play text-primary"></i>
                    <span>Recently Accessed Courses</span>
                </div>
                
                <?php if (mysqli_num_rows($recent_res) > 0): ?>
                    <?php while ($course = mysqli_fetch_assoc($recent_res)): ?>
                        <div class="course-mini-card">
                            <div class="course-img-wrapper">
                                <img src="../<?php echo $course['course_image']; ?>" alt="Course">
                            </div>
                            <div class="course-info-mini">
                                <div class="d-flex justify-content-between">
                                    <h6><?php echo $course['course_name']; ?></h6>
                                    <span class="badge bg-light text-dark" style="font-size: 0.7rem;"><?php echo $course['course_progress']; ?>% Done</span>
                                </div>
                                <p>by <?php echo $course['instructor_name']; ?></p>
                                <div class="progress progress-custom">
                                    <div class="progress-bar progress-bar-custom" style="width: <?php echo $course['course_progress']; ?>%"></div>
                                </div>
                            </div>
                            <div class="course-action d-flex align-items-center">
                                <a href="learn.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">Resume</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center p-5 bg-white rounded-4 border">
                        <img src="../assets/img/notfound.png" alt="No Courses" style="height: 100px; opacity: 0.5;">
                        <p class="mt-3 text-muted">You haven't enrolled in any courses yet.</p>
                        <a href="../courses.php" class="btn btn-primary btn-sm">Browse Courses</a>
                    </div>
                <?php endif; ?>
                
                <!-- Discover New Courses -->
                <div class="section-title mt-5 d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fa-solid fa-compass text-primary"></i>
                        <span>Discover New Courses</span>
                    </div>
                    <a href="../courses.php" class="btn btn-link btn-sm text-decoration-none p-0">Browse Catalog</a>
                </div>
                
                <div class="row">
                <?php if (mysqli_num_rows($discover_res) > 0): ?>
                    <?php while ($course = mysqli_fetch_assoc($discover_res)): ?>
                        <div class="col-md-4 mb-3">
                            <div class="course-card" style="margin-bottom:0; height:100%;">
                                <div class="course-img-wrapper" style="height:120px; width:100%; border-radius:0; position:relative;">
                                    <span class="course-badge" style="font-size:0.6rem; padding:3px 8px;"><?php echo $course['course_category_name'] ?: 'General'; ?></span>
                                    <img src="../<?php echo $course['course_image']; ?>" alt="Course Thumbnail">
                                </div>
                                <div class="course-info-mini" style="padding:15px; display:flex; flex-direction:column; flex:1;">
                                    <h6 class="course-title-discover" style="font-size:0.95rem; height:2.8rem; margin-bottom:8px;"><?php echo $course['course_name']; ?></h6>
                                    <div class="instructor-info" style="margin-bottom:10px; font-size:0.75rem; color:var(--text-muted); display:flex; align-items:center; gap:6px;">
                                        <i class="fa-regular fa-user-circle"></i>
                                        <span><?php echo $course['instructor_name']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                        <span class="fw-bold text-primary"><?php echo $course['course_price'] > 0 ? '$'.$course['course_price'] : 'Free'; ?></span>
                                        <a href="../course-overview.php?id=<?php echo $course['course_id']; ?>" class="btn btn-sm btn-outline-primary" style="font-size:0.75rem; padding:4px 10px; border-radius:8px;">Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center p-4 bg-white rounded-4 border">
                        <p class="mb-0 text-muted">No new courses available to discover at this moment.</p>
                    </div>
                <?php endif; ?>
                </div>
            </div>

            <!-- Notifications / Announcements -->
            <div class="col-lg-4">
                <div class="section-title">
                    <i class="fa-regular fa-bell text-primary"></i>
                    <span>Latest Activity</span>
                </div>
                <div class="announcement-card shadow-sm">
                    <?php
                    $notif_res = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5");
                    if (mysqli_num_rows($notif_res) > 0):
                        while ($notif = mysqli_fetch_assoc($notif_res)):
                            $bg = '#e0e7ff'; $color = '#4338ca'; $icon = 'fa-bell';
                            if ($notif['type'] == 'quiz') { $bg = '#fef3c7'; $color = '#d97706'; $icon = 'fa-list-check'; }
                            if ($notif['type'] == 'msg') { $bg = '#fae8ff'; $color = '#a21caf'; $icon = 'fa-comment'; }
                    ?>
                    <div class="notif-item">
                        <div class="notif-icon" style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>;">
                            <i class="fa-solid <?php echo $icon; ?>"></i>
                        </div>
                        <div class="notif-info">
                            <h6 class="mb-1" style="font-size: 0.85rem; font-weight: 600;"><?php echo $notif['title']; ?></h6>
                            <p class="mb-0 text-muted" style="font-size: 0.75rem;"><?php echo $notif['message']; ?></p>
                        </div>
                    </div>
                    <?php endwhile; else: ?>
                        <p class="text-muted text-center py-4" style="font-size: 0.85rem;">No recent notifications.</p>
                    <?php endif; ?>
                    <a href="notifications.php" class="btn btn-link w-100 text-decoration-none mt-2" style="font-size: 0.85rem; font-weight: 600;">View All</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
