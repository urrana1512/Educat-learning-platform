<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    // Filters
    $filter_course = isset($_GET['course']) ? intval($_GET['course']) : 0;
    $filter_rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
    $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

    $where = "c.course_instructor = $user_id";
    if($filter_course > 0) $where .= " AND r.course_id = $filter_course";
    if($filter_rating > 0) $where .= " AND r.review_stars = $filter_rating";
    if(!empty($search))    $where .= " AND (u.user_name LIKE '%$search%' OR r.review_text LIKE '%$search%')";

    $order = ($sort == 'oldest') ? "r.review_date ASC" : "r.review_date DESC";

    // Avg rating (Total stats for instructor)
    $avg_sql = "SELECT AVG(r.review_stars) as avg_rating, COUNT(*) as total_reviews FROM course_review_master r JOIN course_master c ON r.course_id = c.course_id WHERE c.course_instructor = $user_id AND r.review_stars > 0";
    $avg_row = mysqli_fetch_assoc(mysqli_query($conn, $avg_sql));
    $avg_rating    = round($avg_row['avg_rating'], 1) ?: 0;
    $total_reviews = $avg_row['total_reviews'];

    // Get list of instructor's courses for filter
    $courses_res = mysqli_query($conn, "SELECT course_id, course_name FROM course_master WHERE course_instructor = $user_id ORDER BY course_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviews & Ratings — EduCat Instructor</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="../dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
    <style>
        .ins-filter-bar {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 24px;
            background: var(--card-bg);
            padding: 16px 20px;
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .ins-filter-item { min-width: 160px; }
        .ins-stars .text-light { color: #E2E8F0; }
        
        @media (max-width: 768px) {
            .ins-filter-item { flex: 1; min-width: 140px; }
            .ins-search-wrap { width: 100% !important; order: -1; }
        }
    </style>
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("includes/sidebar.php"); ?>
    <div class="ins-main">
        <?php include("includes/header.php"); ?>
        <div class="ins-content">

            <div class="ins-page-header">
                <div>
                    <h1 class="ins-page-title">Reviews & Ratings</h1>
                    <p class="ins-page-subtitle">Manage student feedback and ratings</p>
                </div>
            </div>

            <form method="GET" class="ins-filter-bar">
                <div class="ins-search-wrap" style="flex:1;min-width:240px;position:relative;">
                    <i class="fas fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-light);font-size:.85rem;"></i>
                    <input type="text" name="search" class="ins-form-control" placeholder="Search reviews or students..." value="<?php echo htmlspecialchars($search); ?>" style="padding-left:38px;">
                </div>
                <div class="ins-filter-item">
                    <select name="course" class="ins-form-control" onchange="this.form.submit()">
                        <option value="0">All Courses</option>
                        <?php while($c = mysqli_fetch_assoc($courses_res)): ?>
                        <option value="<?php echo $c['course_id'];?>" <?php if($filter_course == $c['course_id']) echo 'selected';?>><?php echo htmlspecialchars($c['course_name']);?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="ins-filter-item">
                    <select name="rating" class="ins-form-control" onchange="this.form.submit()">
                        <option value="0">All Ratings</option>
                        <option value="5" <?php if($filter_rating == 5) echo 'selected';?>>5 Stars</option>
                        <option value="4" <?php if($filter_rating == 4) echo 'selected';?>>4 Stars</option>
                        <option value="3" <?php if($filter_rating == 3) echo 'selected';?>>3 Stars</option>
                        <option value="2" <?php if($filter_rating == 2) echo 'selected';?>>2 Stars</option>
                        <option value="1" <?php if($filter_rating == 1) echo 'selected';?>>1 Star</option>
                    </select>
                </div>
                <div class="ins-filter-item">
                    <select name="sort" class="ins-form-control" onchange="this.form.submit()">
                        <option value="newest" <?php if($sort == 'newest') echo 'selected';?>>Newest First</option>
                        <option value="oldest" <?php if($sort == 'oldest') echo 'selected';?>>Oldest First</option>
                    </select>
                </div>
                <?php if($search || $filter_course || $filter_rating || $sort != 'newest'): ?>
                <a href="reviews.php" class="ins-btn ins-btn-outline" title="Clear Filters"><i class="fas fa-times"></i></a>
                <?php endif; ?>
            </form>

            <div style="display:grid;grid-template-columns:1fr 340px;gap:28px;align-items:start;" class="reviews-grid">

                <!-- Reviews List -->
                <div class="reviews-list">
                <?php
                    $sql = "SELECT r.*, u.user_name, u.user_profile_photo, c.course_name
                            FROM course_review_master r
                            JOIN user_master u ON r.reviewer_id = u.user_id
                            JOIN course_master c ON r.course_id = c.course_id
                            WHERE $where ORDER BY $order";
                    $res = mysqli_query($conn, $sql);
                    if($res && mysqli_num_rows($res) > 0){
                        while($row = mysqli_fetch_assoc($res)){
                            $stars = intval($row["review_stars"]);
                ?>
                    <div class="ins-card mb-20" style="transition:var(--transition); border-left: 4px solid <?php echo ($stars >= 4) ? 'var(--success)' : (($stars >= 3) ? 'var(--warning)' : 'var(--danger)'); ?>;">
                        <div class="ins-card__body">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                                <div style="display:flex;align-items:center;gap:14px;">
                                    <div style="position:relative;">
                                        <img src="../<?php echo $row['user_profile_photo']; ?>" class="ins-avatar ins-avatar-md" onerror="this.src='../assets/img/EduCat (4).png'">
                                        <div style="position:absolute;bottom:-2px;right:-2px;width:16px;height:16px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-sm);">
                                            <i class="fas fa-circle" style="font-size:8px;color:var(--success);"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <p style="margin:0;font-weight:700;color:var(--text);font-size:.95rem;"><?php echo htmlspecialchars($row['user_name']); ?></p>
                                        <p style="margin:0;font-size:.75rem;color:var(--text-muted);"><?php echo date('M d, Y • H:i', strtotime($row['review_date'])); ?></p>
                                    </div>
                                </div>
                                <div class="ins-badge ins-badge-primary">
                                    <i class="fas fa-book" style="font-size:.7rem;"></i> <?php echo htmlspecialchars($row['course_name']); ?>
                                </div>
                            </div>

                            <div class="ins-stars" style="font-size:.9rem;margin-bottom:12px;display:flex;gap:2px;">
                                <?php for($i=1;$i<=5;$i++) echo $i<=$stars ? '<i class="fas fa-star"></i>' : '<i class="far fa-star text-light"></i>'; ?>
                                <span style="color:var(--text);font-weight:700;font-size:.85rem;margin-left:8px;"><?php echo $stars; ?>.0</span>
                            </div>

                            <p style="color:var(--text);font-size:.9rem;line-height:1.6;margin:0 0 20px;">
                                <?php echo htmlspecialchars($row['review_text']); ?>
                            </p>

                            <div style="display:flex;justify-content:space-between;align-items:center;padding-top:16px;border-top:1px solid var(--border-light);">
                                <div style="display:flex;gap:12px;">
                                    <button class="ins-btn ins-btn-outline ins-btn-sm" style="border-radius:20px;">
                                        <i class="fas fa-reply"></i> Reply
                                    </button>
                                    <button class="ins-btn ins-btn-outline ins-btn-sm" style="border-radius:20px;color:var(--text-light);border-color:var(--border-light);">
                                        <i class="far fa-thumbs-up"></i> Helpful
                                    </button>
                                </div>
                                <button class="ins-btn-icon" style="background:transparent;border:none;color:var(--text-light);" title="Report">
                                    <i class="fas fa-flag"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } } else { ?>
                    <div class="ins-card">
                        <div class="ins-empty">
                            <i class="fas fa-search"></i>
                            <h5>No reviews found</h5>
                            <p>Try adjusting your search or filters to find what you're looking for.</p>
                        </div>
                    </div>
                <?php } ?>
                </div>

                <!-- Rating Summary -->
                <div class="summary-col" style="position:sticky;top:calc(var(--header-h) + 20px);">
                    <div class="ins-card">
                        <div class="ins-card__header">
                            <h4 class="ins-card__title">Performance Summary</h4>
                        </div>
                        <div class="ins-card__body">
                            <div style="text-align:center;padding:10px 0 24px;border-bottom:1px solid var(--border-light);margin-bottom:24px;">
                                <div style="font-size:3.5rem;font-weight:800;color:var(--text);line-height:1;margin-bottom:10px;font-family:'Poppins',sans-serif;letter-spacing:-1px;">
                                    <?php echo $avg_rating ?: '0.0'; ?>
                                </div>
                                <div class="ins-stars" style="font-size:1.4rem;color:#F59E0B;margin-bottom:12px;justify-content:center;display:flex;gap:4px;">
                                    <?php
                                    $rv = floatval($avg_rating);
                                    for($i=1;$i<=5;$i++){
                                        if($i<=$rv) echo '<i class="fas fa-star"></i>';
                                        elseif($i-0.5<=$rv) echo '<i class="fas fa-star-half-alt"></i>';
                                        else echo '<i class="far fa-star text-light"></i>';
                                    }
                                    ?>
                                </div>
                                <p style="color:var(--text-muted);font-size:.85rem;margin:0;">Instructor Average Rating</p>
                                <p style="font-size:.75rem;color:var(--text-light);margin-top:4px;">Total <strong><?php echo $total_reviews; ?></strong> reviews</p>
                            </div>

                            <!-- Distribution bars -->
                            <div style="display:flex;flex-direction:column;gap:12px;">
                                <?php for($r=5;$r>=1;$r--): 
                                    $cnt_q = "SELECT COUNT(*) as c FROM course_review_master rv JOIN course_master c ON rv.course_id = c.course_id WHERE c.course_instructor = $user_id AND rv.review_stars = $r";
                                    $cnt_row = mysqli_fetch_assoc(mysqli_query($conn, $cnt_q));
                                    $cnt = $cnt_row['c'];
                                    $pct = $total_reviews > 0 ? round(($cnt/$total_reviews)*100) : 0;
                                ?>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <span style="font-size:.8rem;font-weight:700;color:var(--text-muted);width:35px;display:flex;align-items:center;gap:4px;">
                                        <?php echo $r;?><i class="fas fa-star" style="color:#F59E0B;font-size:.7rem;"></i>
                                    </span>
                                    <div class="ins-progress" style="flex:1;height:8px;background:#F1F5F9;">
                                        <div class="ins-progress-bar" style="width:<?php echo $pct;?>%;background:<?php echo $r>=4?'var(--success)':($r>=3?'var(--warning)':'var(--danger)');?>;border-radius:4px;"></div>
                                    </div>
                                    <span style="font-size:.78rem;font-weight:600;color:var(--text-muted);width:32px;text-align:right;"><?php echo $pct;?>%</span>
                                </div>
                                <?php endfor; ?>
                            </div>

                            <div style="margin-top:32px;padding:16px;background:var(--primary-light);border-radius:var(--radius);border:1px dashed var(--primary);">
                                <div style="display:flex;gap:10px;align-items:flex-start;">
                                    <i class="fas fa-lightbulb" style="color:var(--primary);margin-top:2px;"></i>
                                    <p style="margin:0;font-size:0.775rem;line-height:1.5;color:var(--primary-dark);">
                                        <strong>Pro Tip:</strong> Responding to reviews within 24 hours can improve student satisfaction by <strong>15%</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Responsive adjustments
    function checkWidth() {
        const grid = document.querySelector('.reviews-grid');
        const summary = document.querySelector('.summary-col');
        if (window.innerWidth < 1000) {
            grid.style.gridTemplateColumns = '1fr';
            summary.style.position = 'static';
            summary.style.order = '-1';
        } else {
            grid.style.gridTemplateColumns = '1fr 340px';
            summary.style.position = 'sticky';
            summary.style.order = '0';
        }
    }
    window.addEventListener('resize', checkWidth);
    window.addEventListener('load', checkWidth);
</script>

<?php include("includes/footer.php"); ?>
</body>
</html>
