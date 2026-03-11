<?php
include 'header.php';
include 'sidebar.php';

// Fetch all enrolled courses
$enrolled_query = "SELECT pc.*, c.course_name, c.course_image, c.course_category, c.course_rating as avg_rating, 
                          u.user_name as instructor_name, cat.course_category_name,
                          (SELECT COUNT(*) FROM feedback_master WHERE feedback_on = c.course_id) as reviews_count,
                          (SELECT COUNT(*) FROM videos_master v 
                           JOIN course_chapter_list ch ON v.video_of_chapter = ch.course_chapter_id 
                           WHERE ch.course_id = c.course_id) as total_lessons
                   FROM purchased_course_master pc 
                   JOIN course_master c ON pc.course_id = c.course_id 
                   JOIN user_master u ON c.course_instructor = u.user_id 
                   LEFT JOIN course_category_master cat ON c.course_category = cat.course_category_id
                   WHERE pc.user_id = $user_id 
                   ORDER BY pc.purchase_date DESC";
$enrolled_res = mysqli_query($conn, $enrolled_query);
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

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h2 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .course-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 30px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: var(--primary-color);
    }

    .course-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(5px);
        padding: 5px 12px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--primary-color);
        z-index: 10;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .course-img-box {
        position: relative;
        height: 180px;
        overflow: hidden;
    }

    .course-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .course-card:hover .course-img-box img {
        transform: scale(1.1);
    }

    .course-body {
        padding: 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .course-category {
        font-size: 0.75rem;
        color: var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .course-title {
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 12px;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        height: 3rem;
    }

    .instructor-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .instructor-info span {
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .rating-box {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 15px;
        font-size: 0.85rem;
    }

    .rating-box .stars {
        color: #fbbf24;
    }

    .progress-section {
        margin-top: auto;
    }

    .progress-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .progress-bar-container {
        height: 8px;
        background: #f1f5f9;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--primary-color);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .btn-continue {
        border-radius: 12px;
        font-weight: 600;
        padding: 10px;
        transition: all 0.2s;
    }

    .btn-continue:hover {
        background: var(--primary-hover);
        color: white;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h2>My Courses</h2>
                <p class="text-muted">You have enrolled in <?php echo mysqli_num_rows($enrolled_res); ?> courses.</p>
            </div>
            <div class="filter-box d-none d-md-block">
                <select class="form-select border-0 shadow-sm" style="border-radius: 10px;">
                    <option>All Courses</option>
                    <option>In Progress</option>
                    <option>Completed</option>
                </select>
            </div>
        </div>

        <div class="row">
            <?php if (mysqli_num_rows($enrolled_res) > 0): ?>
                <?php while ($course = mysqli_fetch_assoc($enrolled_res)): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="course-card">
                            <div class="course-img-box">
                                <span class="course-badge"><?php echo $course['course_category_name'] ?: 'General'; ?></span>
                                <img src="../<?php echo $course['course_image']; ?>" alt="Course Thumbnail">
                            </div>
                            <div class="course-body">
                                <span class="course-category"><?php echo $course['course_category_name']; ?></span>
                                <h5 class="course-title"><?php echo $course['course_name']; ?></h5>
                                
                                <div class="instructor-info">
                                    <i class="fa-regular fa-user-circle text-muted"></i>
                                    <span><?php echo $course['instructor_name']; ?></span>
                                </div>

                                <div class="rating-box">
                                    <div class="stars">
                                        <?php 
                                        $rating = $course['avg_rating'];
                                        for($i=1; $i<=5; $i++) {
                                            echo $i <= $rating ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                        }
                                        ?>
                                    </div>
                                    <span class="text-muted">(<?php echo $course['reviews_count']; ?> Reviews)</span>
                                </div>

                                <div class="progress-section">
                                    <div class="progress-info">
                                        <span>Progress</span>
                                        <span><?php echo $course['course_progress']; ?>%</span>
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" style="width: <?php echo $course['course_progress']; ?>%"></div>
                                    </div>
                                    <p class="text-muted mb-3" style="font-size: 0.75rem;">
                                        <i class="fa-regular fa-circle-play me-1"></i> <?php echo $course['total_lessons']; ?> Lessons total
                                    </p>
                                    <a href="learn.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary w-100 btn-continue">
                                        <?php echo $course['course_progress'] > 0 ? 'Continue Learning' : 'Start Learning'; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <img src="../assets/img/notfound.png" alt="No Results" style="max-width: 200px; opacity: 0.5;">
                    <h4 class="mt-4">No enrolled courses yet</h4>
                    <p class="text-muted">Start your learning journey today by exploring our courses.</p>
                    <a href="../courses.php" class="btn btn-primary mt-3 px-5 py-2" style="border-radius: 12px;">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
