<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

// contact_us: contact_id, contact_user_name, contact_user_email, contact_user_message, contact_date
if(isset($_GET['action']) && $_GET['action']=='delete' && isset($_GET['id'])){
    mysqli_query($conn,"DELETE FROM contact_us WHERE contact_id=".(int)$_GET['id']);
    $_SESSION['admin_success']="Message deleted.";
    header("Location: messages.php"); exit();
}
$msgs_q = mysqli_query($conn,"SELECT * FROM contact_us ORDER BY contact_id DESC");
$success = $_SESSION['admin_success'] ?? ''; unset($_SESSION['admin_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages — EduCat Admin</title>
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
                    <h2 class="ins-page-title">Contact Messages</h2>
                    <p class="ins-page-subtitle">All support and contact form submissions from users.</p>
                </div>
            </div>
            <?php if($success): ?><div class="alert alert-success border-0 rounded-4 mb-3"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>
            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead><tr><th>#</th><th>From</th><th>Email</th><th>Message</th><th>Date</th><th class="text-end">Action</th></tr></thead>
                            <tbody>
                                <?php if(mysqli_num_rows($msgs_q)>0): $i=1; while($r=mysqli_fetch_assoc($msgs_q)): ?>
                                <tr>
                                    <td class="text-muted"><?php echo $i++; ?></td>
                                    <td class="fw-600 smallest"><?php echo htmlspecialchars($r['contact_user_name']); ?></td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($r['contact_user_email']); ?></td>
                                    <td class="smallest"><?php echo htmlspecialchars($r['contact_user_message']); ?></td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($r['contact_date']); ?></td>
                                    <td class="text-end">
                                        <a href="mailto:<?php echo htmlspecialchars($r['contact_user_email']); ?>" class="ins-btn-icon text-primary" title="Reply"><i class="fas fa-reply"></i></a>
                                        <a href="messages.php?action=delete&id=<?php echo $r['contact_id']; ?>" class="ins-btn-icon text-danger" title="Delete" onclick="return confirm('Delete this message?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5"><i class="fas fa-comment-dots fs-2 d-block mb-2 opacity-25"></i>No messages received yet.</td></tr>
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
