<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    // Mark all as read
    if(isset($_GET['mark_read'])){
        mysqli_query($conn, "UPDATE notifications_master SET is_read = 1 WHERE user_id = $user_id");
        header("Location: notifications.php"); exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications — EduCat Instructor</title>
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
                    <h1 class="ins-page-title">Notifications</h1>
                    <p class="ins-page-subtitle">Stay up to date with your course activity</p>
                </div>
                <a href="notifications.php?mark_read=true" class="ins-btn ins-btn-outline"><i class="fas fa-check-double"></i> Mark All as Read</a>
            </div>

            <div class="ins-card" style="max-width:780px;">
                <div class="ins-card__body">
                <?php
                    $sql = "SELECT * FROM notifications_master WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 30";
                    $res = mysqli_query($conn, $sql);
                    if($res && mysqli_num_rows($res) > 0){
                        while($row = mysqli_fetch_assoc($res)){
                            $unread = $row['is_read'] == 0;
                            $icon='fa-info-circle'; $bg='#F3F4F6'; $color='#6B7280';
                            if(stripos($row['notification_title'],'enroll')!==false){ $icon='fa-user-plus'; $bg='#EEF2FF'; $color='#4F46E5'; }
                            elseif(stripos($row['notification_title'],'review')!==false){ $icon='fa-star'; $bg='#FEF3C7'; $color='#D97706'; }
                            elseif(stripos($row['notification_title'],'message')!==false){ $icon='fa-comment-alt'; $bg='#ECFEFF'; $color='#0891b2'; }
                            elseif(stripos($row['notification_title'],'publish')!==false){ $icon='fa-check-circle'; $bg='#D1FAE5'; $color='#059669'; }
                ?>
                <div class="ins-notif-item" style="<?php echo $unread ? 'background:var(--primary-light);padding:14px;border-radius:var(--radius-sm);margin-bottom:4px;border:none;' : ''; ?>">
                    <div class="ins-notif-icon" style="background:<?php echo $bg;?>;color:<?php echo $color;?>;">
                        <i class="fas <?php echo $icon;?>"></i>
                    </div>
                    <div style="flex:1;">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                            <p class="ins-notif-title"><?php echo htmlspecialchars($row['notification_title']); ?> <?php if($unread) echo '<span class="ins-badge ins-badge-primary" style="font-size:.65rem;">New</span>'; ?></p>
                        </div>
                        <p class="ins-notif-body"><?php echo htmlspecialchars($row['notification_message']); ?></p>
                        <p class="ins-notif-time"><i class="far fa-clock"></i> <?php echo date('M d, Y · H:i', strtotime($row['created_at'])); ?></p>
                    </div>
                </div>
                <?php } } else { echo '<div class="ins-empty"><i class="fas fa-bell"></i><h5>No notifications</h5><p>You\'re all caught up!</p></div>'; } ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
</body>
</html>
