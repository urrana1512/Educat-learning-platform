<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

// Handle delete
if(isset($_GET['action']) && $_GET['action']=='delete' && isset($_GET['id'])){
    $did = (int)$_GET['id'];
    mysqli_query($conn,"DELETE FROM feedback_master WHERE feedback_id=$did");
    $_SESSION['admin_success']="Review deleted.";
    header("Location: reviews.php"); exit();
}

// feedback_master: feedback_id, feedback_by(user_id), feedback_on(course_id), feedback_message, feedback_date
$reviews_q = mysqli_query($conn,
    "SELECT fm.*, um.user_name, cm.course_name
     FROM feedback_master fm
     LEFT JOIN user_master um ON fm.feedback_by = um.user_id
     LEFT JOIN course_master cm ON fm.feedback_on = cm.course_id
     ORDER BY fm.feedback_id DESC");
$success = $_SESSION['admin_success'] ?? ''; unset($_SESSION['admin_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reviews — EduCat Admin</title>
<link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../instructor/assets/css/instructor.css">
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("sidebar.php"); ?>
    <div class="ins-main">
        <?php include("header.php"); ?>
        <div class="ins-content">
            <div class="ins-page-header">
                <div>
                    <h2 class="ins-page-title">Review Moderation</h2>
                    <p class="ins-page-subtitle">View and moderate all student reviews and feedback.</p>
                </div>
            </div>
            <?php if($success): ?><div class="alert alert-success border-0 rounded-4 mb-3"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>
            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead><tr><th>#</th><th>Student</th><th>Course</th><th>Review</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
                            <tbody>
                                <?php if(mysqli_num_rows($reviews_q)>0): $i=1; while($r=mysqli_fetch_assoc($reviews_q)): ?>
                                <tr>
                                    <td class="text-muted"><?php echo $i++; ?></td>
                                    <td class="fw-600 smallest"><?php echo htmlspecialchars($r['user_name'] ?? 'N/A'); ?></td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars(mb_substr($r['course_name'] ?? 'N/A', 0, 30)); ?>...</td>
                                    <td class="smallest"><?php echo htmlspecialchars($r['feedback_message']); ?></td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($r['feedback_date']); ?></td>
                                    <td class="text-end">
                                        <a href="reviews.php?action=delete&id=<?php echo $r['feedback_id']; ?>" class="ins-btn-icon text-danger" onclick="return confirm('Delete this review?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5"><i class="fas fa-star fs-2 d-block mb-2 opacity-25"></i>No reviews submitted yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body></html>
