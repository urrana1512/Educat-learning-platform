<?php
require '../includes/scripts/connection.php';
session_start();

if(!isset($_SESSION['educat_logedin_user_id']) || trim($_SESSION['educat_logedin_user_id'])===''){
    header("Location: ../sign-in.php"); exit();
}
$user_id = (int)$_SESSION['educat_logedin_user_id'];
$userdata = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=$user_id"));
if(!$userdata || $userdata['role']!=1){ header("Location: ../404.php"); exit(); }

// ─── Live Stats ───────────────────────────────────────────────────────────────
$total_students    = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM user_master WHERE role=3"))['c'];
$total_instructors = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM user_master WHERE role=2"))['c'];
$total_courses     = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM course_master"))['c'];
$total_enrollments = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM purchased_course_master"))['c'];
$total_revenue     = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(payment_amount),0) r FROM purchased_course_master"))['r'];
$total_categories  = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM course_category_master"))['c'];
$pending_courses   = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM course_master WHERE course_approved_by_admin=0"))['c'];

// Enrollments by course for bar chart
$chart_labels = []; $chart_data = [];
$cq = mysqli_query($conn,"SELECT cm.course_name, COUNT(pcm.pcm_id) as cnt FROM course_master cm LEFT JOIN purchased_course_master pcm ON cm.course_id=pcm.course_id GROUP BY cm.course_id ORDER BY cnt DESC LIMIT 6");
while($rc=mysqli_fetch_assoc($cq)){ $chart_labels[]=mb_substr($rc['course_name'],0,16); $chart_data[]=(int)$rc['cnt']; }

// Category distribution
$cat_labels=[]; $cat_data=[];
$catq=mysqli_query($conn,"SELECT ccm.course_category_name,COUNT(cm.course_id) cnt FROM course_category_master ccm LEFT JOIN course_master cm ON cm.course_category=CAST(ccm.course_category_id AS CHAR) GROUP BY ccm.course_category_id HAVING cnt>0 ORDER BY cnt DESC LIMIT 5");
while($rc=mysqli_fetch_assoc($catq)){ $cat_labels[]=$rc['course_category_name']; $cat_data[]=(int)$rc['cnt']; }
if(empty($cat_data)){ $cat_labels=['No data']; $cat_data=[1]; }

// Recent courses
$recent_courses_q = mysqli_query($conn,"SELECT cm.*, um.user_name instructor_name FROM course_master cm LEFT JOIN user_master um ON cm.course_instructor=um.user_id ORDER BY cm.course_id DESC LIMIT 5");

// Recent users
$recent_users_q = mysqli_query($conn,"SELECT * FROM user_master WHERE role!=1 ORDER BY user_id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Dashboard — EduCat</title>
<link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../instructor/assets/css/instructor.css">
<style>
/* ── Premium Stat Cards ─────────────────────────────────────── */
.adm-stat {
    border-radius: 16px;
    padding: 24px 22px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    position: relative;
    overflow: hidden;
    border: none;
    box-shadow: 0 4px 24px rgba(0,0,0,.10);
    transition: transform .2s ease, box-shadow .2s ease;
    color: #fff;
}
.adm-stat:hover { transform: translateY(-4px); box-shadow: 0 8px 32px rgba(0,0,0,.18); }
.adm-stat__top { display:flex; align-items:flex-start; justify-content:space-between; }
.adm-stat__icon {
    width: 48px; height: 48px; border-radius: 14px;
    background: rgba(255,255,255,.2);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.35rem;
}
.adm-stat__badge {
    background: rgba(255,255,255,.2);
    border-radius: 8px; padding: 4px 10px;
    font-size: 0.72rem; font-weight: 700;
    backdrop-filter: blur(4px);
}
.adm-stat__value {
    font-size: 2rem; font-weight: 800;
    letter-spacing: -.5px; line-height: 1;
    font-family: 'Poppins', sans-serif;
}
.adm-stat__label { font-size: 0.825rem; opacity: .85; font-weight: 500; }
.adm-stat__bar {
    height: 4px; border-radius: 99px;
    background: rgba(255,255,255,.25);
    margin-top: 4px;
}
.adm-stat__bar-fill {
    height: 100%; border-radius: 99px;
    background: rgba(255,255,255,.7);
    width: 70%;
}
/* Gradient variants */
.adm-grad-indigo  { background: linear-gradient(135deg,#4F46E5 0%,#7C3AED 100%); }
.adm-grad-cyan    { background: linear-gradient(135deg,#06B6D4 0%,#0284C7 100%); }
.adm-grad-emerald { background: linear-gradient(135deg,#10B981 0%,#059669 100%); }
.adm-grad-amber   { background: linear-gradient(135deg,#F59E0B 0%,#D97706 100%); }
.adm-grad-rose    { background: linear-gradient(135deg,#EF4444 0%,#DC2626 100%); }
.adm-grad-violet  { background: linear-gradient(135deg,#8B5CF6 0%,#7C3AED 100%); }

/* ── Chart card ───────────────────────────────────────────── */
.adm-chart-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: 0 2px 16px rgba(0,0,0,.06);
    overflow: hidden;
}
.adm-chart-card__header {
    padding: 18px 22px 0;
    display: flex; align-items: center; justify-content: space-between;
}
.adm-chart-card__title {
    font-size: .95rem; font-weight: 700;
    font-family: 'Poppins',sans-serif;
    color: var(--text);
    margin: 0;
}
.adm-chart-card__sub { font-size:.75rem; color: var(--text-muted); }
.adm-chart-card__body { padding: 16px 22px 22px; }

/* ── Activity Feed ────────────────────────────────────────── */
.adm-user-row {
    display: flex; align-items: center; gap: 12px;
    padding: 9px 0;
    border-bottom: 1px solid var(--border-light);
}
.adm-user-row:last-child { border-bottom: none; padding-bottom: 0; }
</style>
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("sidebar.php"); ?>
    <div class="ins-main">
        <?php include("header.php"); ?>
        <div class="ins-content">

            <!-- Page Header -->
            <div class="ins-page-header">
                <div>
                    <h1 class="ins-page-title">Platform Overview</h1>
                    <p class="ins-page-subtitle">Welcome back, <strong><?php echo htmlspecialchars(explode(' ',$userdata['user_name'])[0]); ?></strong> — here's what's happening today.</p>
                </div>
                <?php if($pending_courses > 0): ?>
                <a href="course-list.php" class="ins-btn ins-btn-outline">
                    <i class="fas fa-clock"></i> <?php echo $pending_courses; ?> Course<?php echo $pending_courses>1?'s':''; ?> Pending
                </a>
                <?php endif; ?>
            </div>

            <!-- ─── Premium Stat Cards ─────────────────────────────── -->
            <div class="row g-3 mb-4">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-indigo">
                        <div class="adm-stat__top">
                            <div class="adm-stat__icon"><i class="fas fa-user-graduate"></i></div>
                            <span class="adm-stat__badge">+<?php echo $total_students; ?></span>
                        </div>
                        <div>
                            <div class="adm-stat__value"><?php echo number_format($total_students); ?></div>
                            <div class="adm-stat__label">Total Students</div>
                        </div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:<?php echo min(100, $total_students*10); ?>%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-cyan">
                        <div class="adm-stat__top">
                            <div class="adm-stat__icon"><i class="fas fa-chalkboard-teacher"></i></div>
                            <span class="adm-stat__badge">+<?php echo $total_instructors; ?></span>
                        </div>
                        <div>
                            <div class="adm-stat__value"><?php echo number_format($total_instructors); ?></div>
                            <div class="adm-stat__label">Instructors</div>
                        </div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:65%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-emerald">
                        <div class="adm-stat__top">
                            <div class="adm-stat__icon"><i class="fas fa-book"></i></div>
                            <span class="adm-stat__badge">Live</span>
                        </div>
                        <div>
                            <div class="adm-stat__value"><?php echo number_format($total_courses); ?></div>
                            <div class="adm-stat__label">Total Courses</div>
                        </div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:80%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-amber">
                        <div class="adm-stat__top">
                            <div class="adm-stat__icon"><i class="fas fa-shopping-cart"></i></div>
                            <span class="adm-stat__badge">All time</span>
                        </div>
                        <div>
                            <div class="adm-stat__value"><?php echo number_format($total_enrollments); ?></div>
                            <div class="adm-stat__label">Enrollments</div>
                        </div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:<?php echo min(100,$total_enrollments*8); ?>%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-rose">
                        <div class="adm-stat__top">
                            <div class="adm-stat__icon"><i class="fas fa-rupee-sign"></i></div>
                            <span class="adm-stat__badge">Revenue</span>
                        </div>
                        <div>
                            <div class="adm-stat__value" style="font-size:1.4rem;">₹<?php echo number_format($total_revenue); ?></div>
                            <div class="adm-stat__label">Total Revenue</div>
                        </div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:75%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-violet">
                        <div class="adm-stat__top">
                            <div class="adm-stat__icon"><i class="fas fa-th-large"></i></div>
                            <span class="adm-stat__badge"><?php echo $pending_courses; ?> pending</span>
                        </div>
                        <div>
                            <div class="adm-stat__value"><?php echo number_format($total_categories); ?></div>
                            <div class="adm-stat__label">Categories</div>
                        </div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:60%"></div></div>
                    </div>
                </div>
            </div>

            <!-- ─── Charts Row ──────────────────────────────────────── -->
            <div class="row g-4 mb-4">
                <!-- Enrollment Bar Chart -->
                <div class="col-lg-8">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header">
                            <div>
                                <h2 class="adm-chart-card__title">Course Enrollments</h2>
                                <div class="adm-chart-card__sub">Number of students per course</div>
                            </div>
                            <a href="enrollments.php" class="ins-btn ins-btn-outline ins-btn-sm">View All</a>
                        </div>
                        <div class="adm-chart-card__body">
                            <canvas id="enrollChart" style="height:230px;max-height:230px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Category Doughnut -->
                <div class="col-lg-4">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header">
                            <div>
                                <h2 class="adm-chart-card__title">By Category</h2>
                                <div class="adm-chart-card__sub">Course distribution</div>
                            </div>
                        </div>
                        <div class="adm-chart-card__body d-flex flex-column align-items-center">
                            <canvas id="catChart" style="height:200px;max-height:200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── Tables Row ──────────────────────────────────────── -->
            <div class="row g-4">
                <!-- Recent Courses -->
                <div class="col-lg-7">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <h2 class="adm-chart-card__title">Recent Courses</h2>
                                <div class="adm-chart-card__sub">Lately added to the platform</div>
                            </div>
                            <a href="course-list.php" class="ins-btn ins-btn-outline ins-btn-sm">View All</a>
                        </div>
                        <div class="ins-table-wrap" style="border-radius:0;border:none;border-top:1px solid var(--border-light);">
                            <table class="ins-table">
                                <thead><tr>
                                    <th>Course</th>
                                    <th>Instructor</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr></thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($recent_courses_q)>0): while($c=mysqli_fetch_assoc($recent_courses_q)): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="../<?php echo htmlspecialchars($c['course_image']); ?>"
                                                    style="width:38px;height:28px;border-radius:6px;object-fit:cover;flex-shrink:0;"
                                                    onerror="this.src='../assets/img/EduCat (4).png'">
                                                <span style="font-weight:600;font-size:0.8rem;"><?php echo htmlspecialchars(mb_substr($c['course_name'],0,30)); ?>...</span>
                                            </div>
                                        </td>
                                        <td style="font-size:0.8rem;color:var(--text-muted);"><?php echo htmlspecialchars($c['instructor_name'] ?? '—'); ?></td>
                                        <td style="font-weight:700;color:#4F46E5;">₹<?php echo number_format($c['course_price']); ?></td>
                                        <td>
                                            <?php if($c['course_approved_by_admin']==1): ?>
                                                <span class="ins-badge ins-badge-success">Approved</span>
                                            <?php else: ?>
                                                <span class="ins-badge ins-badge-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="4" style="text-align:center;padding:28px;color:var(--text-muted);"><i class="fas fa-book-open d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>No courses yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="col-lg-5">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header mb-2">
                            <div>
                                <h2 class="adm-chart-card__title">Recent Users</h2>
                                <div class="adm-chart-card__sub">Latest registrations</div>
                            </div>
                            <a href="users.php" class="ins-btn ins-btn-outline ins-btn-sm">View All</a>
                        </div>
                        <div class="adm-chart-card__body" style="padding-top:12px;">
                            <?php if(mysqli_num_rows($recent_users_q)>0): while($usr=mysqli_fetch_assoc($recent_users_q)): ?>
                            <div class="adm-user-row">
                                <img src="../<?php echo htmlspecialchars($usr['user_profile_photo']); ?>"
                                    style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;border:2px solid var(--border);"
                                    onerror="this.src='../assets/img/EduCat (4).png'">
                                <div style="min-width:0;flex:1;">
                                    <div style="font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($usr['user_name']); ?></div>
                                    <div style="font-size:0.72rem;color:var(--text-muted);"><?php echo htmlspecialchars($usr['user_email']); ?></div>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    <span class="ins-badge <?php echo $usr['role']==2?'ins-badge-primary':'ins-badge-muted'; ?>">
                                        <?php echo $usr['role']==2?'Instructor':'Student'; ?>
                                    </span>
                                    <span class="ins-badge <?php echo $usr['isActive']==0?'ins-badge-success':'ins-badge-danger'; ?>">
                                        <?php echo $usr['isActive']==0?'Active':'Blocked'; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endwhile; else: ?>
                            <div style="text-align:center;padding:28px;color:var(--text-muted);"><i class="fas fa-users d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>No users yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Enrollment Bar Chart ──────────────────────────────────────
const enrollLabels = <?php echo json_encode($chart_labels ?: ['No data']); ?>;
const enrollData   = <?php echo json_encode($chart_data   ?: [0]); ?>;
new Chart(document.getElementById('enrollChart'), {
    type: 'bar',
    data: {
        labels: enrollLabels,
        datasets: [{
            label: 'Enrollments',
            data: enrollData,
            backgroundColor: function(ctx) {
                var chart = ctx.chart, gradient = chart.ctx.createLinearGradient(0,0,0,chart.height);
                gradient.addColorStop(0,'rgba(79,70,229,.8)');
                gradient.addColorStop(1,'rgba(124,58,237,.2)');
                return gradient;
            },
            borderColor: '#4F46E5',
            borderWidth: 0,
            borderRadius: 10,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#fff',
                bodyColor: '#94a3b8',
                padding: 12,
                cornerRadius: 10,
                callbacks: { label: ctx => ' ' + ctx.parsed.y + ' Enrollments' }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { precision: 0, color: '#94a3b8', font: {size:11} },
                grid: { color: '#f1f5f9', drawBorder: false }
            },
            x: {
                ticks: { color: '#94a3b8', font: {size:11} },
                grid: { display: false }
            }
        }
    }
});

// ── Category Doughnut ─────────────────────────────────────────
const colors = ['#4F46E5','#06B6D4','#10B981','#F59E0B','#EF4444'];
new Chart(document.getElementById('catChart'), {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($cat_labels); ?>,
        datasets: [{
            data: <?php echo json_encode($cat_data); ?>,
            backgroundColor: colors,
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: {size:11}, boxWidth: 10, padding: 14, color: '#64748b' }
            },
            tooltip: {
                backgroundColor: '#1e293b',
                titleColor: '#fff',
                bodyColor: '#94a3b8',
                padding: 12,
                cornerRadius: 10,
            }
        }
    }
});
</script>
</body>
</html>