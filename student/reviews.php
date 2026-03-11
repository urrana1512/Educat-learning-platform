<?php
include 'header.php';
include 'sidebar.php';

// Fetch all courses that are completed (so they can be reviewed) or already reviewed
$rev_query = "SELECT pc.*, c.course_name, c.course_image, u.user_name as instructor_name 
              FROM purchased_course_master pc 
              JOIN course_master c ON pc.course_id = c.course_id 
              JOIN user_master u ON c.course_instructor = u.user_id 
              WHERE pc.user_id = $user_id AND (pc.course_completed = 1 OR pc.course_rating > 0)";
$rev_res = mysqli_query($conn, $rev_query);

// Handle Review Submission
if (isset($_POST['submit_review'])) {
    $course_id = $_POST['course_id'];
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $today = date('d-m-Y');
    
    // Update purchased_course_master with rating
    mysqli_query($conn, "UPDATE purchased_course_master SET course_rating = $rating WHERE user_id = $user_id AND course_id = $course_id");
    
    // Add to course_review_master
    mysqli_query($conn, "INSERT INTO course_review_master (reviewer_id, course_id, review_text, review_stars) 
                        VALUES ($user_id, $course_id, '$comment', $rating)");
                        
    // Notify Instructor
    $course_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT course_name, course_instructor FROM course_master WHERE course_id = $course_id"));
    if ($course_data) {
        $ins_id = $course_data['course_instructor'];
        $c_name = $course_data['course_name'];
        $notif_title = "New Course Review!";
        
        $student_name = "A student";
        $stu_res = mysqli_query($conn, "SELECT user_name FROM user_master WHERE user_id = $user_id");
        if($stu_res && mysqli_num_rows($stu_res) > 0) {
            $student_name = mysqli_fetch_assoc($stu_res)['user_name'];
        }
        
        $notif_msg = "$student_name gave a $rating-star review for '$c_name'.";
        mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($ins_id, '$notif_title', '$notif_msg', 'review')");
    }
    
    // Update course average rating in course_master
    $avg_query = "SELECT AVG(course_rating) as avg_rating FROM purchased_course_master WHERE course_id = $course_id AND course_rating > 0";
    $avg_res = mysqli_query($conn, $avg_query);
    $new_avg = round(mysqli_fetch_assoc($avg_res)['avg_rating']);
    mysqli_query($conn, "UPDATE course_master SET course_rating = $new_avg WHERE course_id = $course_id");
    
    echo "<script>alert('Review submitted successfully!'); window.location.href='reviews.php';</script>";
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

    .review-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 25px;
        margin-bottom: 25px;
        display: flex;
        gap: 25px;
        align-items: center;
    }

    .course-thumb {
        width: 120px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
    }

    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: flex-end;
        gap: 5px;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 1.5rem;
        color: #e2e8f0;
        cursor: pointer;
        transition: color 0.2s;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #fbbf24;
    }

    .existing-rating {
        color: #fbbf24;
        font-size: 1.1rem;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="page-header mb-5">
            <h2 class="fw-bold">Course Reviews</h2>
            <p class="text-muted">Your feedback helps instructors improve and other students decide.</p>
        </div>

        <div class="row">
            <?php if (mysqli_num_rows($rev_res) > 0): ?>
                <?php while ($course = mysqli_fetch_assoc($rev_res)): ?>
                    <div class="col-lg-12">
                        <div class="review-card">
                            <img src="../<?php echo $course['course_image']; ?>" class="course-thumb" alt="Course">
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-1"><?php echo $course['course_name']; ?></h5>
                                <p class="text-muted small mb-3">Instructor: <?php echo $course['instructor_name']; ?></p>
                                
                                <?php if ($course['course_rating'] > 0): ?>
                                    <div class="existing-rating">
                                        <?php for($i=1; $i<=5; $i++) {
                                            echo $i <= $course['course_rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                        } ?>
                                        <span class="text-muted small ms-2">(You already rated this)</span>
                                    </div>
                                <?php else: ?>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $course['course_id']; ?>">Write a Review</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Review Modal -->
                        <div class="modal fade" id="reviewModal<?php echo $course['course_id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow" style="border-radius: 20px;">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Rate this course</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="" method="POST">
                                        <div class="modal-body p-4">
                                            <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                            <p class="text-muted small mb-4">How was your experience with "<?php echo $course['course_name']; ?>"?</p>
                                            
                                            <div class="mb-4">
                                                <label class="form-label fw-bold small">Rating</label>
                                                <div class="star-rating">
                                                    <input type="radio" id="star5-<?php echo $course['course_id']; ?>" name="rating" value="5" required/><label for="star5-<?php echo $course['course_id']; ?>"><i class="fa-solid fa-star"></i></label>
                                                    <input type="radio" id="star4-<?php echo $course['course_id']; ?>" name="rating" value="4"/><label for="star4-<?php echo $course['course_id']; ?>"><i class="fa-solid fa-star"></i></label>
                                                    <input type="radio" id="star3-<?php echo $course['course_id']; ?>" name="rating" value="3"/><label for="star3-<?php echo $course['course_id']; ?>"><i class="fa-solid fa-star"></i></label>
                                                    <input type="radio" id="star2-<?php echo $course['course_id']; ?>" name="rating" value="2"/><label for="star2-<?php echo $course['course_id']; ?>"><i class="fa-solid fa-star"></i></label>
                                                    <input type="radio" id="star1-<?php echo $course['course_id']; ?>" name="rating" value="1"/><label for="star1-<?php echo $course['course_id']; ?>"><i class="fa-solid fa-star"></i></label>
                                                </div>
                                            </div>

                                            <div class="mb-0">
                                                <label class="form-label fw-bold small">Your Review</label>
                                                <textarea class="form-control border-0 bg-light" name="comment" rows="4" placeholder="Share your thoughts about the course..." style="border-radius: 12px; resize: none;" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="submit_review" class="btn btn-primary rounded-pill px-4">Submit Review</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">You can write reviews for courses you have completed.</p>
                    <a href="my-courses.php" class="btn btn-primary rounded-pill px-4">View My Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
