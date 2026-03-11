<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    // Stats
    $total_students = 0;
    $res_s = mysqli_query($conn, "SELECT SUM(course_purchases) as total FROM course_master WHERE course_instructor = $user_id");
    if($r = mysqli_fetch_assoc($res_s)) $total_students = $r['total'] ?: 0;

    // Monthly enrollment data
    $months_labels   = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $enrollment_data = array_fill(0, 12, 0);
    $monthly_res = mysqli_query($conn, "SELECT pcm.purchase_date FROM purchased_course_master pcm JOIN course_master c ON pcm.course_id = c.course_id WHERE c.course_instructor = $user_id");
    if($monthly_res){ while($mrow = mysqli_fetch_assoc($monthly_res)){ $ts = strtotime($mrow['purchase_date']); if($ts){ $enrollment_data[date('n',$ts)-1]++; } } }

    // Progress data
    $progress_data = [0,0,0,0,0];
    $prog_res = mysqli_query($conn, "SELECT pcm.course_progress, pcm.course_completed FROM purchased_course_master pcm JOIN course_master c ON pcm.course_id = c.course_id WHERE c.course_instructor = $user_id");
    if($prog_res){ while($prow = mysqli_fetch_assoc($prog_res)){ $pg = floatval($prow['course_progress']); if($prow['course_completed']==1||$pg>=100) $progress_data[4]++; elseif($pg>=76) $progress_data[3]++; elseif($pg>=51) $progress_data[2]++; elseif($pg>=26) $progress_data[1]++; else $progress_data[0]++; } }

    // Top courses
    $top_courses = [];
    $top_res = mysqli_query($conn, "SELECT course_name, course_purchases FROM course_master WHERE course_instructor = $user_id ORDER BY course_purchases DESC LIMIT 5");
    if($top_res) while($trow = mysqli_fetch_assoc($top_res)) $top_courses[] = $trow;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics — EduCat Instructor</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="../dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("includes/sidebar.php"); ?>
    <div class="ins-main">
        <?php include("includes/header.php"); ?>
        <div class="ins-content">

            <div class="ins-page-header">
                <div>
                    <h1 class="ins-page-title">Analytics</h1>
                    <p class="ins-page-subtitle">Detailed insights into your course performance</p>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-3 mb-24">
                <div class="ins-stat-card ins-stat--blue">
                    <div class="ins-stat-icon"><i class="fas fa-users"></i></div>
                    <div class="ins-stat-info">
                        <div class="ins-stat-value"><?php echo number_format($total_students); ?></div>
                        <div class="ins-stat-label">Total Enrollments</div>
                        <div style="margin-top:8px;"><span class="ins-badge ins-badge-success"><i class="fas fa-arrow-up"></i> 12.5%</span></div>
                    </div>
                </div>
                <div class="ins-stat-card ins-stat--cyan">
                    <div class="ins-stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="ins-stat-info">
                        <div class="ins-stat-value">68%</div>
                        <div class="ins-stat-label">Avg. Completion Rate</div>
                        <div style="margin-top:8px;"><span class="ins-badge ins-badge-primary"><i class="fas fa-arrow-up"></i> 4.2%</span></div>
                    </div>
                </div>
                <div class="ins-stat-card ins-stat--green">
                    <div class="ins-stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="ins-stat-info">
                        <div class="ins-stat-value">$<?php echo number_format($total_students * 45); ?></div>
                        <div class="ins-stat-label">Total Earnings</div>
                        <div style="margin-top:8px;"><span class="ins-badge ins-badge-success"><i class="fas fa-arrow-up"></i> 8.1%</span></div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;margin-bottom:24px;">
                <div class="ins-card">
                    <div class="ins-card__header">
                        <h4 class="ins-card__title">Enrollment Growth</h4>
                        <span class="ins-badge ins-badge-muted">2025</span>
                    </div>
                    <div class="ins-card__body"><canvas id="enrollmentChart" height="260"></canvas></div>
                </div>
                <div class="ins-card">
                    <div class="ins-card__header"><h4 class="ins-card__title">Top Performing Courses</h4></div>
                    <div class="ins-card__body">
                        <?php foreach($top_courses as $tc):
                            $pct = $total_students > 0 ? round(($tc['course_purchases']/$total_students)*100) : 0;
                        ?>
                        <div class="mb-16">
                            <div class="flex-between mb-4">
                                <span style="font-size:.8rem;font-weight:600;color:var(--text);max-width:70%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($tc['course_name']); ?></span>
                                <span style="font-size:.8rem;font-weight:700;color:var(--primary);"><?php echo $tc['course_purchases']; ?></span>
                            </div>
                            <div class="ins-progress"><div class="ins-progress-bar" style="width:<?php echo $pct; ?>%"></div></div>
                        </div>
                        <?php endforeach; if(empty($top_courses)) echo '<p class="text-muted text-center">No data yet.</p>'; ?>
                    </div>
                </div>
            </div>

            <!-- Completion Chart -->
            <div class="ins-card">
                <div class="ins-card__header"><h4 class="ins-card__title">Student Completion Distribution</h4></div>
                <div class="ins-card__body"><canvas id="completionChart" height="200"></canvas></div>
            </div>

        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('enrollmentChart').getContext('2d'), {
    type:'line',
    data:{
        labels:<?php echo json_encode($months_labels);?>,
        datasets:[{ label:'New Enrollments', data:<?php echo json_encode($enrollment_data);?>, borderColor:'#4F46E5', backgroundColor:'rgba(79,70,229,.08)', borderWidth:2.5, pointBackgroundColor:'#fff', pointBorderColor:'#4F46E5', pointBorderWidth:2, fill:true, tension:.4 }]
    },
    options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'}}, x:{grid:{display:false}} } }
});
new Chart(document.getElementById('completionChart').getContext('2d'), {
    type:'bar',
    data:{
        labels:['0-25%','26-50%','51-75%','76-99%','Completed'],
        datasets:[{ label:'Students', data:<?php echo json_encode($progress_data);?>, backgroundColor:['#E2E8F0','#CBD5E1','#06B6D4','#4F46E5','#10B981'], borderRadius:6, barPercentage:.55 }]
    },
    options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{display:false}}, scales:{ y:{beginAtZero:true,grid:{color:'rgba(0,0,0,.04)'}}, x:{grid:{display:false}} } }
});
</script>
<?php include("includes/footer.php"); ?>
</body>
</html>
