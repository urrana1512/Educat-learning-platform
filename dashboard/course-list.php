<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id']) || trim($_SESSION['educat_logedin_user_id'])===''){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

// Filters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn,$_GET['search']) : '';
$status_f = isset($_GET['status']) && in_array($_GET['status'],['0','1']) ? (int)$_GET['status'] : '';

$where = "WHERE 1";
if($search)   $where .= " AND cm.course_name LIKE '%$search%'";
if($status_f !== '') $where .= " AND cm.course_approved_by_admin = $status_f";

$courses_q = mysqli_query($conn,"SELECT cm.*, um.user_name as instructor_name,
    (SELECT COUNT(*) FROM purchased_course_master WHERE course_id=cm.course_id) as enrollment_count,
    ccm.course_category_name
    FROM course_master cm
    LEFT JOIN user_master um ON cm.course_instructor = um.user_id
    LEFT JOIN course_category_master ccm ON cm.course_category = CAST(ccm.course_category_id AS CHAR)
    $where ORDER BY cm.course_id DESC");

$success = $_SESSION['admin_success'] ?? ''; unset($_SESSION['admin_success']);
$error   = $_SESSION['admin_error']   ?? ''; unset($_SESSION['admin_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Course Catalog — EduCat Admin</title>
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
                    <h2 class="ins-page-title">Course Catalog</h2>
                    <p class="ins-page-subtitle">Review, approve, and manage all courses on the platform.</p>
                </div>
            </div>

            <?php if($success): ?><div class="alert alert-success border-0 rounded-4 mb-3"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>
            <?php if($error):   ?><div class="alert alert-danger  border-0 rounded-4 mb-3"><i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?></div><?php endif; ?>

            <!-- Filter -->
            <div class="ins-card mb-24">
                <div class="ins-card__body">
                    <form class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="ins-form-label">Search Courses</label>
                            <input type="text" name="search" class="ins-form-control" placeholder="Course name..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="ins-form-label">Approval Status</label>
                            <select name="status" class="ins-form-control">
                                <option value="">All Courses</option>
                                <option value="1" <?php if($status_f===1) echo 'selected'; ?>>Approved</option>
                                <option value="0" <?php if($status_f===0) echo 'selected'; ?>>Pending Review</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="ins-btn ins-btn-primary me-2">Filter</button>
                            <a href="course-list.php" class="ins-btn ins-btn-outline">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead><tr>
                                <th>Course</th><th>Instructor</th><th>Category</th><th>Price</th><th>Enrollments</th><th>Status</th><th class="text-end">Actions</th>
                            </tr></thead>
                            <tbody>
                                <?php if(mysqli_num_rows($courses_q) > 0): while($c=mysqli_fetch_assoc($courses_q)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="../<?php echo $c['course_image']; ?>" style="width:42px;height:32px;border-radius:6px;object-fit:cover;" onerror="this.src='../assets/img/EduCat (4).png'">
                                            <div>
                                                <div class="fw-600 smallest"><?php echo htmlspecialchars(mb_substr($c['course_name'],0,35)); ?>...</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="smallest text-muted"><?php echo htmlspecialchars($c['instructor_name'] ?? 'N/A'); ?></td>
                                    <td class="smallest"><?php echo htmlspecialchars($c['course_category_name'] ?? '—'); ?></td>
                                    <td class="fw-600">₹<?php echo number_format($c['course_price']); ?></td>
                                    <td><span class="ins-badge ins-badge-primary"><?php echo $c['enrollment_count']; ?></span></td>
                                    <td>
                                        <?php if($c['course_approved_by_admin']==1): ?>
                                            <span class="ins-badge ins-badge-success">Approved</span>
                                        <?php else: ?>
                                            <span class="ins-badge ins-badge-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="course-chapter-list.php?course=<?php echo $c['course_id']; ?>" class="ins-btn-icon text-primary" title="View Curriculum"><i class="fas fa-layer-group"></i></a>
                                            <a href="course-edit.php?courseId=<?php echo $c['course_id']; ?>" class="ins-btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                            <?php if($c['course_approved_by_admin']==0): ?>
                                                <a href="course-action.php?id=<?php echo $c['course_id']; ?>&action=approve" class="ins-btn-icon text-success" title="Approve"><i class="fas fa-check-circle"></i></a>
                                            <?php else: ?>
                                                <a href="course-action.php?id=<?php echo $c['course_id']; ?>&action=unapprove" class="ins-btn-icon text-warning" title="Unapprove"><i class="fas fa-times-circle"></i></a>
                                            <?php endif; ?>
                                            <a href="course-action.php?id=<?php echo $c['course_id']; ?>&action=delete" class="ins-btn-icon text-danger" title="Delete" onclick="return confirm('Delete this course and all its content?')"><i class="fas fa-trash"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="7" class="text-center py-5"><i class="fas fa-book fs-2 d-block mb-2 opacity-25"></i>No courses found.</td></tr>
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