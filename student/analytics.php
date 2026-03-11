<?php
include 'header.php';
include 'sidebar.php';

// Fetch detailed analytics
// 1. Courses progress breakdown
$prog_query = "SELECT c.course_name, pc.course_progress 
               FROM purchased_course_master pc 
               JOIN course_master c ON pc.course_id = c.course_id 
               WHERE pc.user_id = $user_id 
               ORDER BY pc.course_progress DESC";
$prog_res = mysqli_query($conn, $prog_query);

// 2. Cumulative Stats
$total_lessons_comp_query = "SELECT COUNT(*) as total FROM lesson_progress WHERE user_id = $user_id";
$total_lessons_comp = mysqli_fetch_assoc(mysqli_query($conn, $total_lessons_comp_query))['total'];

$avg_score_query = "SELECT AVG(certificate_score) as avg FROM certificate_master WHERE certificate_student = $user_id";
$avg_score = round(mysqli_fetch_assoc(mysqli_query($conn, $avg_score_query))['avg']);

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

    .analytics-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .chart-box {
        height: 300px;
        display: flex;
        align-items: flex-end;
        gap: 15px;
        padding-top: 20px;
    }

    .chart-bar-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
        height: 100%;
        justify-content: flex-end;
    }

    .chart-bar {
        width: 40px;
        background: var(--primary-color);
        border-radius: 8px 8px 0 0;
        transition: height 1s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }

    .chart-bar:hover {
        background: var(--primary-hover);
    }

    .chart-bar .tooltip {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--text-main);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        opacity: 0;
        transition: opacity 0.2s;
        pointer-events: none;
    }

    .chart-bar:hover .tooltip {
        opacity: 1;
    }

    .chart-label {
        font-size: 0.7rem;
        color: var(--text-muted);
        text-align: center;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .stat-pill {
        background: #f8fafc;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        height: 100%;
        border: 1px solid var(--border-color);
    }

    .stat-pill-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="page-header mb-5">
            <h2 class="fw-bold">Learning Analytics</h2>
            <p class="text-muted">Track your progress and celebrate your learning milestones.</p>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-pill">
                    <div class="stat-pill-icon" style="background: #eef2ff; color: #6366f1;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0"><?php echo $total_lessons_comp; ?></h4>
                        <p class="text-muted small mb-0">Lessons Completed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-pill">
                    <div class="stat-pill-icon" style="background: #ecfdf5; color: #059669;">
                        <i class="fa-solid fa-fire"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0">7 Days</h4>
                        <p class="text-muted small mb-0">Learning Streak</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-pill">
                    <div class="stat-pill-icon" style="background: #fff7ed; color: #ea580c;">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0"><?php echo $avg_score ? $avg_score : 0; ?>%</h4>
                        <p class="text-muted small mb-0">Avg. Quiz Score</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="analytics-card">
                    <h5 class="fw-bold mb-4">Course Progress Overview</h5>
                    <div class="chart-box">
                        <?php if (mysqli_num_rows($prog_res) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($prog_res)): ?>
                                <div class="chart-bar-container">
                                    <div class="chart-bar" style="height: <?php echo $row['course_progress']; ?>%;">
                                        <div class="tooltip"><?php echo $row['course_progress']; ?>%</div>
                                    </div>
                                    <span class="chart-label"><?php echo $row['course_name']; ?></span>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted w-100 text-center pb-5">No course progress data available.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
