<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn,$_GET['search']) : '';
$swhere = $search ? "AND (user_name LIKE '%$search%' OR user_email LIKE '%$search%')" : '';

$students_q = mysqli_query($conn,
    "SELECT u.*,
        (SELECT COUNT(*) FROM purchased_course_master WHERE user_id=u.user_id) as enrollment_count
     FROM user_master u WHERE u.role=3 $swhere ORDER BY u.user_id DESC");
$total = mysqli_num_rows($students_q);
$success = $_SESSION['admin_success'] ?? ''; unset($_SESSION['admin_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Students — EduCat Admin</title>
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
                    <h2 class="ins-page-title">Student Management</h2>
                    <p class="ins-page-subtitle"><strong><?php echo $total; ?></strong> students registered on the platform.</p>
                </div>
            </div>
            <?php if($success): ?><div class="alert alert-success border-0 rounded-4 mb-3"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>

            <div class="ins-card mb-24">
                <div class="ins-card__body">
                    <form class="row g-3 align-items-end">
                        <div class="col-md-7">
                            <label class="ins-form-label">Search Students</label>
                            <input type="text" name="search" class="ins-form-control" placeholder="Name or email..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="ins-btn ins-btn-primary me-2">Search</button>
                            <a href="students-list.php" class="ins-btn ins-btn-outline">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead><tr><th>Student</th><th>Email</th><th>Enrolled Courses</th><th>Joined</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                            <tbody>
                                <?php if($total>0): while($s=mysqli_fetch_assoc($students_q)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="../<?php echo $s['user_profile_photo']; ?>" class="ins-avatar" style="width:36px;height:36px;" onerror="this.src='../assets/img/EduCat (4).png'">
                                            <span class="fw-600"><?php echo htmlspecialchars($s['user_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($s['user_email']); ?></td>
                                    <td><span class="ins-badge ins-badge-primary"><?php echo $s['enrollment_count']; ?></span></td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($s['registration_date']); ?></td>
                                    <td><?php echo $s['isActive']==0 ? '<span class="ins-badge ins-badge-success">Active</span>' : '<span class="ins-badge ins-badge-danger">Blocked</span>'; ?></td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <?php if($s['isActive']==0): ?>
                                                <a href="user-action.php?id=<?php echo $s['user_id']; ?>&action=block&ref=students" class="ins-btn-icon text-warning" title="Block user" onclick="return confirm('Block this student?')"><i class="fas fa-ban"></i></a>
                                            <?php else: ?>
                                                <a href="user-action.php?id=<?php echo $s['user_id']; ?>&action=unblock&ref=students" class="ins-btn-icon text-success" title="Unblock"><i class="fas fa-check-circle"></i></a>
                                            <?php endif; ?>
                                            <a href="user-action.php?id=<?php echo $s['user_id']; ?>&action=delete&ref=students" class="ins-btn-icon text-danger" title="Delete" onclick="return confirm('Delete student permanently?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center py-5"><i class="fas fa-user-graduate fs-2 d-block mb-2 opacity-25"></i>No students registered yet.</td></tr>
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