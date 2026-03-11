<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

// Handle Add Category POST
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST['action'])){
    $n = mysqli_real_escape_string($conn,$_POST['cat_name'] ?? '');
    $id= (int)($_POST['cat_id'] ?? 0);
    if($_POST['action']=='add' && $n){
        mysqli_query($conn,"INSERT INTO course_category_master (course_category_name, course_category_owner) VALUES ('$n', ".(int)$_SESSION['educat_logedin_user_id'].")");
        $_SESSION['admin_success']="Category added.";
    } elseif($_POST['action']=='edit' && $id && $n){
        mysqli_query($conn,"UPDATE course_category_master SET course_category_name='$n' WHERE course_category_id=$id");
        $_SESSION['admin_success']="Category updated.";
    } elseif($_POST['action']=='delete' && $id){
        mysqli_query($conn,"DELETE FROM course_category_master WHERE course_category_id=$id");
        $_SESSION['admin_success']="Category deleted.";
    }
    header("Location: category-list.php"); exit();
}

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn,$_GET['search']) : '';
$where = $search ? "WHERE course_category_name LIKE '%$search%'" : '';
$categories_q = mysqli_query($conn,"SELECT ccm.*,
    (SELECT COUNT(*) FROM course_master WHERE course_category=CAST(ccm.course_category_id AS CHAR)) as course_count
    FROM course_category_master ccm $where ORDER BY ccm.course_category_id DESC");

$success = $_SESSION['admin_success'] ?? ''; unset($_SESSION['admin_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Categories — EduCat Admin</title>
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
                    <h2 class="ins-page-title">Category Management</h2>
                    <p class="ins-page-subtitle">Manage and organize course categories on the platform.</p>
                </div>
                <button class="ins-btn ins-btn-primary" data-bs-toggle="modal" data-bs-target="#addCatModal">
                    <i class="fas fa-plus me-2"></i> Add Category
                </button>
            </div>

            <?php if($success): ?><div class="alert alert-success border-0 rounded-4 mb-3"><i class="fas fa-check-circle me-2"></i><?php echo $success; ?></div><?php endif; ?>

            <!-- Search -->
            <div class="ins-card mb-24">
                <div class="ins-card__body">
                    <form class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="ins-form-label">Search Categories</label>
                            <input type="text" name="search" class="ins-form-control" placeholder="Category name..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="ins-btn ins-btn-primary me-2">Search</button>
                            <a href="category-list.php" class="ins-btn ins-btn-outline">Reset</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="ins-card">
                <div class="ins-card__body p-0">
                    <div class="ins-table-wrap">
                        <table class="ins-table">
                            <thead><tr>
                                <th>#</th><th>Category Name</th><th>Courses</th><th class="text-end">Actions</th>
                            </tr></thead>
                            <tbody>
                                <?php if(mysqli_num_rows($categories_q)>0): $i=1; while($cat=mysqli_fetch_assoc($categories_q)): ?>
                                <tr>
                                    <td class="text-muted fw-bold"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="ins-stat-icon ins-stat--primary" style="width:36px;height:36px;border-radius:10px;flex-shrink:0;"><i class="fas fa-th-large"></i></div>
                                            <span class="fw-600"><?php echo htmlspecialchars($cat['course_category_name']); ?></span>
                                        </div>
                                    </td>
                                    <td><span class="ins-badge ins-badge-primary"><?php echo $cat['course_count']; ?> courses</span></td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button class="ins-btn-icon" title="Edit" onclick="openEdit(<?php echo $cat['course_category_id']; ?>,'<?php echo addslashes($cat['course_category_name']); ?>')"><i class="fas fa-edit"></i></button>
                                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete category?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="cat_id" value="<?php echo $cat['course_category_id']; ?>">
                                                <button type="submit" class="ins-btn-icon text-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="4" class="text-center py-5"><i class="fas fa-th-large fs-2 d-block mb-2 opacity-25"></i>No categories found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Add New Category</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <label class="ins-form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="cat_name" class="ins-form-control" placeholder="e.g. Web Development" required>
                </div>
                <div class="modal-footer border-0">
                    <button class="ins-btn ins-btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ins-btn ins-btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0">
                <h6 class="modal-title fw-bold">Edit Category</h6>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="cat_id" id="editCatId">
                    <label class="ins-form-label">Category Name <span class="text-danger">*</span></label>
                    <input type="text" name="cat_name" id="editCatName" class="ins-form-control" required>
                </div>
                <div class="modal-footer border-0">
                    <button class="ins-btn ins-btn-outline" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="ins-btn ins-btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="assets/js/jquery-3.6.0.min.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
function openEdit(id, name) {
    document.getElementById('editCatId').value = id;
    document.getElementById('editCatName').value = name;
    new bootstrap.Modal(document.getElementById('editCatModal')).show();
}
</script>
</body>
</html>