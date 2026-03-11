<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$userdata = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$userdata || $userdata['role']!=1){ header("Location: ../404.php"); exit(); }

// ─── KPIs ────────────────────────────────────────────────────
$total_students    = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM user_master WHERE role=3"))['c'];
$total_instructors = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM user_master WHERE role=2"))['c'];
$total_courses     = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM course_master"))['c'];
$total_enrollments = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM purchased_course_master"))['c'];
$total_revenue     = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(payment_amount),0) r FROM purchased_course_master"))['r'];
$total_categories  = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM course_category_master"))['c'];
$approved_courses  = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM course_master WHERE course_approved_by_admin=1"))['c'];
$pending_courses   = $total_courses - $approved_courses;

// Course enrollment bar data
$enrl_labels=[]; $enrl_data=[];
$eq=mysqli_query($conn,"SELECT cm.course_name,COUNT(pcm.pcm_id) cnt FROM course_master cm LEFT JOIN purchased_course_master pcm ON cm.course_id=pcm.course_id GROUP BY cm.course_id ORDER BY cnt DESC LIMIT 7");
while($r=mysqli_fetch_assoc($eq)){ $enrl_labels[]=mb_substr($r['course_name'],0,14); $enrl_data[]=(int)$r['cnt']; }

// Category pie
$cat_labels=[]; $cat_data=[];
$cq=mysqli_query($conn,"SELECT ccm.course_category_name,COUNT(cm.course_id) cnt FROM course_category_master ccm LEFT JOIN course_master cm ON cm.course_category=CAST(ccm.course_category_id AS CHAR) GROUP BY ccm.course_category_id HAVING cnt>0 ORDER BY cnt DESC LIMIT 6");
while($r=mysqli_fetch_assoc($cq)){ $cat_labels[]=$r['course_category_name']; $cat_data[]=(int)$r['cnt']; }
if(empty($cat_data)){ $cat_labels=['No data']; $cat_data=[1]; }

// Top courses by enrollments
$top_q = mysqli_query($conn,"SELECT cm.course_name, cm.course_image, cm.course_price, COUNT(pcm.pcm_id) cnt, um.user_name instructor_name FROM course_master cm LEFT JOIN purchased_course_master pcm ON cm.course_id=pcm.course_id LEFT JOIN user_master um ON cm.course_instructor=um.user_id GROUP BY cm.course_id ORDER BY cnt DESC LIMIT 5");

// Recent transactions
$txn_q = mysqli_query($conn,"SELECT pcm.*,um.user_name,cm.course_name FROM purchased_course_master pcm LEFT JOIN user_master um ON pcm.user_id=um.user_id LEFT JOIN course_master cm ON pcm.course_id=cm.course_id ORDER BY pcm.pcm_id DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Analytics — EduCat Admin</title>
<link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../instructor/assets/css/instructor.css">
<style>
/* ── Premium Gradient Stat Cards ───────────────────────────── */
.adm-stat {
    border-radius: 16px; padding: 22px 20px;
    display: flex; flex-direction: column; gap: 10px;
    position: relative; overflow: hidden;
    border: none; color: #fff;
    box-shadow: 0 6px 28px rgba(0,0,0,.12);
    transition: transform .2s ease, box-shadow .2s ease;
}
.adm-stat:hover { transform: translateY(-4px); box-shadow: 0 10px 36px rgba(0,0,0,.2); }
.adm-stat__top { display:flex; align-items:flex-start; justify-content:space-between; }
.adm-stat__icon {
    width:46px; height:46px; border-radius:13px;
    background: rgba(255,255,255,.2);
    display:flex; align-items:center; justify-content:center;
    font-size:1.3rem;
}
.adm-stat__badge {
    background:rgba(255,255,255,.2); border-radius:8px;
    padding:3px 9px; font-size:.7rem; font-weight:700;
    backdrop-filter: blur(4px);
}
.adm-stat__value { font-size:1.85rem; font-weight:800; letter-spacing:-.5px; line-height:1; font-family:'Poppins',sans-serif; }
.adm-stat__label { font-size:.8rem; opacity:.85; font-weight:500; }
.adm-stat__bar { height:4px; border-radius:99px; background:rgba(255,255,255,.25); margin-top:4px; }
.adm-stat__bar-fill { height:100%; border-radius:99px; background:rgba(255,255,255,.7); }
.adm-grad-indigo  { background: linear-gradient(135deg,#4F46E5 0%,#7C3AED 100%); }
.adm-grad-cyan    { background: linear-gradient(135deg,#06B6D4 0%,#0284C7 100%); }
.adm-grad-emerald { background: linear-gradient(135deg,#10B981 0%,#059669 100%); }
.adm-grad-amber   { background: linear-gradient(135deg,#F59E0B 0%,#D97706 100%); }
.adm-grad-rose    { background: linear-gradient(135deg,#EF4444 0%,#DC2626 100%); }
.adm-grad-violet  { background: linear-gradient(135deg,#8B5CF6 0%,#7C3AED 100%); }

/* ── Chart Card ─────────────────────────────────────────────── */
.adm-chart-card {
    background:var(--card-bg); border:1px solid var(--border);
    border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.06);
    overflow:hidden;
}
.adm-chart-card__header {
    padding:18px 22px 0;
    display:flex; align-items:center; justify-content:space-between;
}
.adm-chart-card__title { font-size:.95rem; font-weight:700; font-family:'Poppins',sans-serif; color:var(--text); margin:0; }
.adm-chart-card__sub   { font-size:.75rem; color:var(--text-muted); margin-top:2px; }
.adm-chart-card__body  { padding:16px 22px 22px; }

/* ── Progress bar for top courses ──────────────────────────── */
.adm-progress { height:7px; border-radius:99px; background:var(--border-light); overflow:hidden; }
.adm-progress-fill { height:100%; border-radius:99px; background:linear-gradient(90deg,#4F46E5,#7C3AED); }

/* ── Metric pill ────────────────────────────────────────────── */
.adm-metric { display:flex; align-items:center; gap:16px; padding:13px 0; border-bottom:1px solid var(--border-light); }
.adm-metric:last-child { border-bottom:none; padding-bottom:0; }
.adm-metric__icon { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.adm-metric__info { flex:1; min-width:0; }
.adm-metric__label { font-size:.8rem; font-weight:600; color:var(--text); }
.adm-metric__sub { font-size:.72rem; color:var(--text-muted); }
.adm-metric__value { font-size:.95rem; font-weight:800; color:var(--text); white-space:nowrap; }
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
                    <h1 class="ins-page-title">Platform Analytics</h1>
                    <p class="ins-page-subtitle">Real-time insights and performance metrics for the entire platform.</p>
                </div>
                <a href="enrollments.php" class="ins-btn ins-btn-outline"><i class="fas fa-download me-2"></i>All Transactions</a>
            </div>

            <!-- ─── KPI Cards ──────────────────────────────────────── -->
            <div class="row g-3 mb-4">
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-indigo">
                        <div class="adm-stat__top"><div class="adm-stat__icon"><i class="fas fa-user-graduate"></i></div><span class="adm-stat__badge">Students</span></div>
                        <div><div class="adm-stat__value"><?php echo number_format($total_students); ?></div><div class="adm-stat__label">Total Students</div></div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:<?php echo min(100,$total_students*10+5); ?>%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-cyan">
                        <div class="adm-stat__top"><div class="adm-stat__icon"><i class="fas fa-chalkboard-teacher"></i></div><span class="adm-stat__badge">Active</span></div>
                        <div><div class="adm-stat__value"><?php echo number_format($total_instructors); ?></div><div class="adm-stat__label">Instructors</div></div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:60%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-emerald">
                        <div class="adm-stat__top"><div class="adm-stat__icon"><i class="fas fa-check-circle"></i></div><span class="adm-stat__badge"><?php echo $pending_courses; ?> pending</span></div>
                        <div><div class="adm-stat__value"><?php echo number_format($approved_courses); ?></div><div class="adm-stat__label">Live Courses</div></div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:<?php echo $total_courses>0?round($approved_courses/$total_courses*100):0; ?>%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-amber">
                        <div class="adm-stat__top"><div class="adm-stat__icon"><i class="fas fa-shopping-cart"></i></div><span class="adm-stat__badge">All time</span></div>
                        <div><div class="adm-stat__value"><?php echo number_format($total_enrollments); ?></div><div class="adm-stat__label">Enrollments</div></div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:<?php echo min(100,$total_enrollments*8+5); ?>%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-rose">
                        <div class="adm-stat__top"><div class="adm-stat__icon"><i class="fas fa-rupee-sign"></i></div><span class="adm-stat__badge">Revenue</span></div>
                        <div><div class="adm-stat__value" style="font-size:1.35rem;">₹<?php echo number_format($total_revenue); ?></div><div class="adm-stat__label">Total Revenue</div></div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:80%"></div></div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-6">
                    <div class="adm-stat adm-grad-violet">
                        <div class="adm-stat__top"><div class="adm-stat__icon"><i class="fas fa-th-large"></i></div><span class="adm-stat__badge">Active</span></div>
                        <div><div class="adm-stat__value"><?php echo number_format($total_categories); ?></div><div class="adm-stat__label">Categories</div></div>
                        <div class="adm-stat__bar"><div class="adm-stat__bar-fill" style="width:70%"></div></div>
                    </div>
                </div>
            </div>

            <!-- ─── Charts Row ──────────────────────────────────────── -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header">
                            <div>
                                <h2 class="adm-chart-card__title">Course Enrollment Distribution</h2>
                                <div class="adm-chart-card__sub">Students enrolled per course</div>
                            </div>
                        </div>
                        <div class="adm-chart-card__body">
                            <canvas id="enrollBar" style="height:230px;max-height:230px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header">
                            <div>
                                <h2 class="adm-chart-card__title">Platform Overview</h2>
                                <div class="adm-chart-card__sub">Users & enrollments</div>
                            </div>
                        </div>
                        <div class="adm-chart-card__body d-flex flex-column align-items-center">
                            <canvas id="overviewDoughnut" style="height:200px;max-height:200px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ─── Top Courses + Transactions ──────────────────────── -->
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header mb-3">
                            <div>
                                <h2 class="adm-chart-card__title">Top Performing Courses</h2>
                                <div class="adm-chart-card__sub">Ranked by enrollments</div>
                            </div>
                            <a href="course-list.php" class="ins-btn ins-btn-outline ins-btn-sm">View All</a>
                        </div>
                        <div class="adm-chart-card__body" style="padding-top:0;">
                            <?php
                            $max_enrl = 1;
                            $top_rows = [];
                            while($tr=mysqli_fetch_assoc($top_q)){ $top_rows[]=$tr; if($tr['cnt']>$max_enrl) $max_enrl=$tr['cnt']; }
                            if(count($top_rows)>0):
                            $colors_list = ['#4F46E5','#06B6D4','#10B981','#F59E0B','#EF4444'];
                            foreach($top_rows as $i=>$tr): ?>
                            <div class="mb-3">
                                <div class="d-flex align-items-center gap-3 mb-1">
                                    <img src="../<?php echo htmlspecialchars($tr['course_image']); ?>"
                                        style="width:36px;height:28px;border-radius:6px;object-fit:cover;flex-shrink:0;"
                                        onerror="this.src='../assets/img/EduCat (4).png'">
                                    <div style="flex:1;min-width:0;">
                                        <div style="font-weight:600;font-size:.82rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?php echo htmlspecialchars($tr['course_name']); ?></div>
                                        <div style="font-size:.72rem;color:var(--text-muted);"><?php echo htmlspecialchars($tr['instructor_name'] ?? '—'); ?> · ₹<?php echo number_format($tr['course_price']); ?></div>
                                    </div>
                                    <div style="font-weight:800;font-size:.9rem;color:<?php echo $colors_list[$i%5]; ?>;white-space:nowrap;"><?php echo $tr['cnt']; ?> <span style="font-weight:400;color:var(--text-muted);font-size:.72rem;">enrolled</span></div>
                                </div>
                                <div class="adm-progress">
                                    <div class="adm-progress-fill" style="width:<?php echo $max_enrl>0?round($tr['cnt']/$max_enrl*100):0; ?>%;background:<?php echo $colors_list[$i%5]; ?>;"></div>
                                </div>
                            </div>
                            <?php endforeach; else: ?>
                            <div style="text-align:center;padding:28px;color:var(--text-muted);">No courses yet.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="adm-chart-card">
                        <div class="adm-chart-card__header mb-2">
                            <div>
                                <h2 class="adm-chart-card__title">Recent Transactions</h2>
                                <div class="adm-chart-card__sub">Latest payments received</div>
                            </div>
                            <a href="enrollments.php" class="ins-btn ins-btn-outline ins-btn-sm">All</a>
                        </div>
                        <div class="adm-chart-card__body" style="padding-top:10px;">
                            <?php while($t=mysqli_fetch_assoc($txn_q)): ?>
                            <div class="adm-metric">
                                <div class="adm-metric__icon" style="background:#EEF2FF;color:#4F46E5;">
                                    <i class="fas fa-rupee-sign"></i>
                                </div>
                                <div class="adm-metric__info">
                                    <div class="adm-metric__label"><?php echo htmlspecialchars($t['user_name'] ?? 'User'); ?></div>
                                    <div class="adm-metric__sub"><?php echo htmlspecialchars(mb_substr($t['course_name'] ?? 'Course',0,22)); ?></div>
                                </div>
                                <div style="text-align:right;">
                                    <div class="adm-metric__value" style="color:#10B981;">+₹<?php echo number_format($t['payment_amount']); ?></div>
                                    <div style="font-size:.7rem;color:var(--text-muted);"><?php echo htmlspecialchars($t['purchase_date']); ?></div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Enrollment Bar ─────────────────────────────────────────────
new Chart(document.getElementById('enrollBar'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($enrl_labels ?: ['No data']); ?>,
        datasets: [{
            label: 'Enrollments',
            data: <?php echo json_encode($enrl_data ?: [0]); ?>,
            backgroundColor: function(ctx) {
                var chart = ctx.chart, g = chart.ctx.createLinearGradient(0,0,0,chart.height);
                g.addColorStop(0,'rgba(79,70,229,.85)'); g.addColorStop(1,'rgba(124,58,237,.2)');
                return g;
            },
            borderRadius: 10, borderSkipped: false, borderWidth: 0,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: false,
        plugins: {
            legend: {display:false},
            tooltip: { backgroundColor:'#1e293b', titleColor:'#fff', bodyColor:'#94a3b8', padding:12, cornerRadius:10 }
        },
        scales: {
            y: { beginAtZero:true, ticks:{precision:0,color:'#94a3b8',font:{size:11}}, grid:{color:'#f1f5f9',drawBorder:false} },
            x: { ticks:{color:'#94a3b8',font:{size:10}}, grid:{display:false} }
        }
    }
});

// ── Overview Doughnut ──────────────────────────────────────────
new Chart(document.getElementById('overviewDoughnut'), {
    type: 'doughnut',
    data: {
        labels:['Students','Instructors','Enrollments'],
        datasets:[{
            data:[<?php echo "$total_students,$total_instructors,$total_enrollments"; ?>],
            backgroundColor:['#4F46E5','#06B6D4','#10B981'],
            borderWidth:3, borderColor:'#ffffff', hoverOffset:8
        }]
    },
    options:{
        responsive:true, maintainAspectRatio:false, cutout:'65%',
        plugins:{
            legend:{position:'bottom',labels:{font:{size:11},boxWidth:10,padding:14,color:'#64748b'}},
            tooltip:{backgroundColor:'#1e293b',titleColor:'#fff',bodyColor:'#94a3b8',padding:12,cornerRadius:10}
        }
    }
});
</script>
</body>
</html>
