<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students — EduCat Instructor</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="../dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("includes/sidebar.php"); ?>
    <div class="ins-main">
        <?php include("includes/header.php"); ?>
        <div class="ins-content">

            <div class="ins-page-header">
                <div>
                    <h1 class="ins-page-title">Students</h1>
                    <p class="ins-page-subtitle">View students enrolled in your courses</p>
                </div>
            </div>

            <div class="ins-card">
                <div class="ins-table-wrap">
                    <table class="ins-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Course Enrolled</th>
                                <th style="width:180px;">Progress</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $sql = "SELECT pcm.course_progress, pcm.course_completed, pcm.purchase_date,
                                           u.user_id, u.user_name, u.user_email, u.user_profile_photo,
                                           c.course_name
                                    FROM purchased_course_master pcm
                                    JOIN user_master u ON pcm.user_id = u.user_id
                                    JOIN course_master c ON pcm.course_id = c.course_id
                                    WHERE c.course_instructor = $user_id ORDER BY pcm.pcm_id DESC";
                            $res = mysqli_query($conn, $sql);
                            if($res && mysqli_num_rows($res) > 0){
                                while($row = mysqli_fetch_assoc($res)){
                                    $prog = intval($row['course_progress']);
                                    $done = $row['course_completed'] == 1 || $prog >= 100;
                        ?>
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <img src="../<?php echo $row['user_profile_photo']; ?>" class="ins-avatar ins-avatar-sm" onerror="this.src='../assets/img/EduCat (4).png'">
                                    <div>
                                        <p style="margin:0;font-weight:600;font-size:.875rem;color:var(--text);"><?php echo htmlspecialchars($row['user_name']); ?></p>
                                        <p style="margin:0;font-size:.775rem;color:var(--text-muted);"><?php echo htmlspecialchars($row['user_email']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p style="margin:0;font-weight:500;font-size:.875rem;color:var(--text);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($row['course_name']); ?></p>
                                <p style="margin:0;font-size:.75rem;color:var(--text-muted);">Since <?php echo date('M d, Y', strtotime($row['purchase_date'])); ?></p>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="ins-progress" style="flex:1;"><div class="ins-progress-bar" style="width:<?php echo $prog;?>%;background:<?php echo $done?'#10B981':($prog>=50?'#4F46E5':'#F59E0B');?>"></div></div>
                                    <span style="font-size:.75rem;font-weight:700;color:var(--text-muted);white-space:nowrap;"><?php echo $prog;?>%</span>
                                </div>
                            </td>
                            <td>
                                <?php if($done): ?>
                                    <span class="ins-badge ins-badge-success"><i class="fas fa-check-circle"></i> Completed</span>
                                <?php else: ?>
                                    <span class="ins-badge ins-badge-primary"><i class="fas fa-play-circle"></i> In Progress</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="messages.php?userid=<?php echo $row['user_id']; ?>" class="ins-btn ins-btn-outline ins-btn-sm"><i class="fas fa-comment-alt"></i> Message</a>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="5">
                                <div class="ins-empty"><i class="fas fa-users"></i><h5>No students yet</h5><p>Students will appear here once they enroll in your courses.</p></div>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
</body>
</html>
