<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id']) || trim($_SESSION['educat_logedin_user_id'])===''){ header("Location: ../sign-in.php"); exit(); }
$user_id = $_SESSION['educat_logedin_user_id'];
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=$user_id"));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

// Filters
$search   = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$role_f   = isset($_GET['role']) && in_array($_GET['role'],['2','3']) ? $_GET['role'] : '';
$status_f = isset($_GET['status']) && in_array($_GET['status'],['0','1']) ? $_GET['status'] : '';

$where = "WHERE 1";
if($search)   $where .= " AND (user_name LIKE '%$search%' OR user_email LIKE '%$search%')";
if($role_f)   $where .= " AND role = $role_f";
if($status_f !== '') $where .= " AND isActive = $status_f";

// Exclude admin accounts from user management
$where .= " AND role != 1";

$users_q = mysqli_query($conn,"SELECT u.*,
    (SELECT COUNT(*) FROM purchased_course_master WHERE user_id=u.user_id) as enrollments
    FROM user_master u $where ORDER BY u.user_id DESC");
$total = mysqli_num_rows($users_q);

$success = $_SESSION['admin_success'] ?? ''; unset($_SESSION['admin_success']);
$error   = $_SESSION['admin_error']   ?? ''; unset($_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management — EduCat Admin</title>
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
                    <h2 class="ins-page-title">User Management</h2>
                    <p class="ins-page-subtitle">Manage <strong><?php echo $total; ?></strong> platform users (students & instructors).</p>
                </div>
            </div>

            <?php if($success): ?><div class="alert alert-success border-0 rounded-4 mb-3"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>
            <?php if($error):   ?><div class="alert alert-danger  border-0 rounded-4 mb-3"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div><?php endif; ?>

            <!-- Search & Filter -->
            <div class="ins-card mb-24">
                <div class="ins-card__body">
                    <form class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="ins-form-label">Search</label>
                            <input type="text" name="search" class="ins-form-control" placeholder="Name or email..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="ins-form-label">Role</label>
                            <select name="role" class="ins-form-control">
                                <option value="">All Roles</option>
                                <option value="2" <?php if($role_f=='2') echo 'selected'; ?>>Instructor</option>
                                <option value="3" <?php if($role_f=='3') echo 'selected'; ?>>Student</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="ins-form-label">Status</label>
                            <select name="status" class="ins-form-control">
                                <option value="">All Status</option>
                                <option value="0" <?php if($status_f==='0') echo 'selected'; ?>>Active</option>
                                <option value="1" <?php if($status_f==='1') echo 'selected'; ?>>Blocked</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="ins-btn ins-btn-primary me-2">Filter</button>
                            <a href="users.php" class="ins-btn ins-btn-outline">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Users Table -->
            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead><tr>
                                <th>User</th><th>Email</th><th>Role</th><th>Enrollments</th><th>Joined</th><th>Status</th><th class="text-end">Actions</th>
                            </tr></thead>
                            <tbody>
                                <?php if(mysqli_num_rows($users_q) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($users_q)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="../<?php echo $row['user_profile_photo']; ?>" class="ins-avatar" style="width:36px;height:36px;" onerror="this.src='../assets/img/EduCat (4).png'">
                                            <span class="fw-600"><?php echo htmlspecialchars($row['user_name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                    <td><?php echo $row['role']==2 ? '<span class="ins-badge ins-badge-blue">Instructor</span>' : '<span class="ins-badge ins-badge-primary">Student</span>'; ?></td>
                                    <td><span class="ins-badge ins-badge-muted"><?php echo $row['enrollments']; ?></span></td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($row['registration_date']); ?></td>
                                    <td>
                                        <?php if($row['isActive']==0): ?>
                                            <span class="ins-badge ins-badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="ins-badge ins-badge-danger">Blocked</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <?php if($row['isActive']==0): ?>
                                                <a href="user-action.php?id=<?php echo $row['user_id']; ?>&action=block" class="ins-btn-icon text-warning" title="Block" onclick="return confirm('Block this user?')"><i class="fas fa-ban"></i></a>
                                            <?php else: ?>
                                                <a href="user-action.php?id=<?php echo $row['user_id']; ?>&action=unblock" class="ins-btn-icon text-success" title="Unblock"><i class="fas fa-check-circle"></i></a>
                                            <?php endif; ?>
                                            <a href="user-action.php?id=<?php echo $row['user_id']; ?>&action=delete" class="ins-btn-icon text-danger" title="Delete" onclick="return confirm('Permanently delete this user?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                                <?php else: ?>
                                <tr><td colspan="7" class="text-center py-5"><i class="fas fa-users fs-2 d-block mb-2 opacity-25"></i>No users found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
