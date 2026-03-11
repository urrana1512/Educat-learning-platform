<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

$enrollments_q = mysqli_query($conn,
    "SELECT pcm.*, um.user_name, um.user_email, cm.course_name, cm.course_price
     FROM purchased_course_master pcm
     LEFT JOIN user_master um ON pcm.user_id = um.user_id
     LEFT JOIN course_master cm ON pcm.course_id = cm.course_id
     ORDER BY pcm.pcm_id DESC");

$total = mysqli_num_rows($enrollments_q);
$total_rev = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(payment_amount),0) r FROM purchased_course_master"))['r'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enrollments — EduCat Admin</title>
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
                    <h2 class="ins-page-title">Enrollment Monitor</h2>
                    <p class="ins-page-subtitle"><strong><?php echo $total; ?></strong> total enrollments — <strong>₹<?php echo number_format($total_rev); ?></strong> total revenue collected.</p>
                </div>
            </div>
            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead><tr><th>#</th><th>Student</th><th>Course</th><th>Amount Paid</th><th>Date</th><th>Transaction ID</th></tr></thead>
                            <tbody>
                                <?php if($total>0): $i=1; while($r=mysqli_fetch_assoc($enrollments_q)): ?>
                                <tr>
                                    <td class="text-muted"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="fw-600 smallest"><?php echo htmlspecialchars($r['user_name'] ?? 'N/A'); ?></div>
                                        <div class="smallest text-muted"><?php echo htmlspecialchars($r['user_email'] ?? ''); ?></div>
                                    </td>
                                    <td class="smallest"><?php echo htmlspecialchars(mb_substr($r['course_name'] ?? 'N/A', 0, 40)); ?></td>
                                    <td><span class="fw-600 text-success">₹<?php echo number_format($r['payment_amount']); ?></span></td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($r['purchase_date']); ?></td>
                                    <td><code class="smallest"><?php echo htmlspecialchars($r['transaction_id']); ?></code></td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5"><i class="fas fa-shopping-cart fs-2 d-block mb-2 opacity-25"></i>No enrollments yet.</td></tr>
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
