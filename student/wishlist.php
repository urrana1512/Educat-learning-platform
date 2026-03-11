<?php
include 'header.php';
include 'sidebar.php';

// Fetch wishlist courses
$wish_query = "SELECT f.*, c.*, u.user_name as instructor_name, cat.course_category_name 
               FROM user_fav_master f 
               JOIN course_master c ON f.course_id = c.course_id 
               JOIN user_master u ON c.course_instructor = u.user_id 
               LEFT JOIN course_category_master cat ON c.course_category = cat.course_category_id
               WHERE f.user_id = $user_id 
               ORDER BY f.date DESC";
$wish_res = mysqli_query($conn, $wish_query);

// Handle Remove from Wishlist
if (isset($_GET['remove_id'])) {
    $remove_id = $_GET['remove_id'];
    mysqli_query($conn, "DELETE FROM user_fav_master WHERE user_id = $user_id AND course_id = $remove_id");
    header("Location: wishlist.php");
    exit();
}
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 100px 30px 30px;
        transition: all 0.3s ease;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
        }
    }

    .wishlist-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        padding: 15px;
        gap: 20px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }

    .wishlist-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        border-color: var(--primary-color);
    }

    .wish-img {
        width: 180px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
    }

    .wish-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .wish-info h5 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .wish-info p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    .price-tag {
        font-weight: 800;
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    .wish-actions {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 10px;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="page-header mb-5">
            <h2 class="fw-bold">My Wishlist</h2>
            <p class="text-muted">Courses you're interested in taking later.</p>
        </div>

        <div class="row">
            <?php if (mysqli_num_rows($wish_res) > 0): ?>
                <?php while ($course = mysqli_fetch_assoc($wish_res)): ?>
                    <div class="col-lg-12">
                        <div class="wishlist-card shadow-sm">
                            <img src="../<?php echo $course['course_image']; ?>" class="wish-img" alt="Course">
                            <div class="wish-info">
                                <span class="badge bg-light text-primary mb-2 align-self-start py-2 px-3" style="border-radius: 8px;"><?php echo $course['course_category_name']; ?></span>
                                <h5 class="text-main"><?php echo $course['course_name']; ?></h5>
                                <p>by <b><?php echo $course['instructor_name']; ?></b></p>
                                <div class="price-tag">₹<?php echo $course['course_price']; ?></div>
                            </div>
                            <div class="wish-actions">
                                <a href="../course-overview.php?id=<?php echo $course['course_id']; ?>" class="btn btn-primary rounded-pill px-4">Enroll Now</a>
                                <a href="?remove_id=<?php echo $course['course_id']; ?>" class="btn btn-outline-danger btn-sm rounded-pill px-4"><i class="fa-regular fa-trash-can me-1"></i> Remove</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-regular fa-heart fs-1 text-muted mb-3" style="font-size: 4rem !important;"></i>
                    <h3>Your wishlist is empty</h3>
                    <p class="text-muted">Save courses you're interested in and they'll appear here.</p>
                    <a href="../courses.php" class="btn btn-primary mt-3 px-5 py-2 rounded-pill">Browse Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
