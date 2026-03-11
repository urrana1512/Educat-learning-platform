<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $result  = mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id");
        $userdata = mysqli_fetch_assoc($result);
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    $search        = isset($_GET['search'])  ? mysqli_real_escape_string($conn, $_GET['search'])  : '';
    $status_filter = isset($_GET['status'])  ? mysqli_real_escape_string($conn, $_GET['status'])  : 'all';

    $where = "course_instructor = $user_id";
    if(!empty($search))      $where .= " AND course_name LIKE '%$search%'";
    if($status_filter == 'published') $where .= " AND course_approved_by_admin = 1";
    elseif($status_filter == 'draft') $where .= " AND course_approved_by_admin = 0";

    $result = mysqli_query($conn, "SELECT course_id, course_name, course_image, course_purchases, course_rating, course_approved_by_admin FROM course_master WHERE $where ORDER BY course_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses — EduCat Instructor</title>
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

            <!-- Page Header -->
            <div class="ins-page-header">
                <div>
                    <h1 class="ins-page-title">My Courses</h1>
                    <p class="ins-page-subtitle">Manage and organise your courses</p>
                </div>
                <a href="create-course.php" class="ins-btn ins-btn-primary">
                    <i class="fas fa-plus"></i> Create New Course
                </a>
            </div>

            <!-- Filters -->
            <div class="ins-card mb-24">
                <div class="ins-card__body">
                    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;">
                        <div style="flex:1;min-width:220px;position:relative;">
                            <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-light);font-size:.85rem;"></i>
                            <input type="text" name="search" class="ins-form-control" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>" style="padding-left:38px;">
                        </div>
                        <select name="status" class="ins-form-control" style="width:180px;" onchange="this.form.submit()">
                            <option value="all"      <?php if($status_filter=='all')       echo 'selected';?>>All Status</option>
                            <option value="published"<?php if($status_filter=='published') echo 'selected';?>>Published</option>
                            <option value="draft"    <?php if($status_filter=='draft')     echo 'selected';?>>Draft</option>
                        </select>
                        <button type="submit" class="ins-btn ins-btn-outline"><i class="fas fa-filter"></i> Filter</button>
                        <?php if($search||$status_filter!='all'): ?>
                        <a href="my-courses.php" class="ins-btn ins-btn-outline"><i class="fas fa-times"></i> Clear</a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Course Grid -->
            <?php if($result && mysqli_num_rows($result) > 0): ?>
            <div class="grid grid-3">
                <?php while($row = mysqli_fetch_assoc($result)):
                    $published = $row['course_approved_by_admin'] == 1;
                ?>
                <div class="ins-course-card">
                    <div style="position:relative;">
                        <img src="../<?php echo $row['course_image']; ?>" class="ins-course-card__img" onerror="this.src='../assets/img/notfound.png'" alt="">
                        <span class="ins-badge <?php echo $published ? 'ins-badge-success' : 'ins-badge-warning'; ?>" style="position:absolute;top:12px;right:12px;">
                            <?php echo $published ? '<i class="fas fa-check-circle"></i> Published' : '<i class="fas fa-clock"></i> Draft'; ?>
                        </span>
                    </div>
                    <div class="ins-course-card__body">
                        <h5 class="ins-course-card__title"><?php echo htmlspecialchars($row['course_name']); ?></h5>
                        <div class="ins-course-card__meta">
                            <span><i class="fas fa-users" style="color:var(--primary);margin-right:4px;"></i><?php echo $row['course_purchases']; ?> Students</span>
                            <span class="ins-stars">
                                <?php
                                $r = floatval($row['course_rating']);
                                for($i=1;$i<=5;$i++) echo $i<=$r ? '<i class="fas fa-star"></i>' : '<i class="far fa-star empty"></i>';
                                echo $r > 0 ? " <span style='color:var(--text-muted);'>{$r}</span>" : " <span style='color:var(--text-light);font-size:.7rem;'>No ratings</span>";
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="ins-course-card__actions">
                        <a href="../dashboard/course-edit.php?courseId=<?php echo $row['course_id']; ?>" class="ins-btn ins-btn-outline ins-btn-sm"><i class="fas fa-pen"></i> Edit</a>
                        <a href="course-curriculum.php?course=<?php echo $row['course_id']; ?>" class="ins-btn ins-btn-outline ins-btn-sm"><i class="fas fa-list"></i> Content</a>
                        <a href="../courses/course-detail.php?id=<?php echo $row['course_id']; ?>" class="ins-btn ins-btn-outline ins-btn-sm" target="_blank"><i class="fas fa-eye"></i> Preview</a>
                        <a href="../dashboard/confirmation.php?course=<?php echo $row['course_id']; ?>" class="ins-btn-icon danger" title="Delete"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="ins-card">
                <div class="ins-empty">
                    <i class="fas fa-book-open"></i>
                    <h5>No courses found</h5>
                    <p><?php echo $search ? "No courses match your search. Try a different term." : "You haven't created any courses yet."; ?></p>
                    <a href="create-course.php" class="ins-btn ins-btn-primary mt-16">Create Your First Course</a>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
</body>
</html>
