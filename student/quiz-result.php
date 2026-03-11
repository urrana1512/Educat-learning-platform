<?php
include 'header.php';
include 'sidebar.php';

if (!isset($_POST['quiz_id'])) {
    header("Location: my-courses.php");
    exit();
}

$quiz_id = $_POST['quiz_id'];
$course_id = $_POST['course_id'];
$answers = isset($_POST['answer']) ? $_POST['answer'] : [];

// Fetch Questions and Check Answers
$questions_query = "SELECT quiz_question_id, quiz_true_option, quiz_question_marks FROM quiz_question_master WHERE quiz_id = $quiz_id";
$questions_res = mysqli_query($conn, $questions_query);

$total_marks = 0;
$obtained_marks = 0;
$total_questions = mysqli_num_rows($questions_res);
$correct_answers = 0;

while ($q = mysqli_fetch_assoc($questions_res)) {
    $q_id = $q['quiz_question_id'];
    $correct_opt = $q['quiz_true_option'];
    $marks = $q['quiz_question_marks'];
    
    $total_marks += $marks;
    if (isset($answers[$q_id]) && $answers[$q_id] == $correct_opt) {
        $obtained_marks += $marks;
        $correct_answers++;
    }
}

$percentage = ($total_marks > 0) ? round(($obtained_marks / $total_marks) * 100) : 0;
$passed = ($percentage >= 50); // Setting 50% as passing marks

// Save result or notify (Optional: depends on if you want a quiz_results table)
// For now, let's just create a notification
$status_text = $passed ? "Passed" : "Failed";
$notif_title = "Quiz Result: $status_text";
$notif_msg = "You scored $obtained_marks/$total_marks ($percentage%) in the course quiz.";
mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($user_id, '$notif_title', '$notif_msg', 'quiz')");

// If passed and course is 100% complete, allow certificate
$cert_eligible = false;
if ($passed) {
    $prog_check = mysqli_query($conn, "SELECT course_progress FROM purchased_course_master WHERE user_id = $user_id AND course_id = $course_id");
    $prog = mysqli_fetch_assoc($prog_check)['course_progress'];
    if ($prog >= 100) {
        $cert_eligible = true;
        // Check if certificate already exists
        $cert_check = mysqli_query($conn, "SELECT * FROM certificate_master WHERE certificate_student = $user_id AND certificate_course = $course_id");
        if (mysqli_num_rows($cert_check) == 0) {
            // Fetch instructor ID
            $ins_query = "SELECT course_instructor FROM course_master WHERE course_id = $course_id";
            $ins_id = mysqli_fetch_assoc(mysqli_query($conn, $ins_query))['course_instructor'];
            $today = date('d-m-Y');
            mysqli_query($conn, "INSERT INTO certificate_master (certificate_course, certificate_student, certificate_instructor, certificate_score, certificate_date) 
                                VALUES ($course_id, $user_id, $ins_id, $percentage, '$today')");
            
            // Notify about certificate
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($user_id, 'Certificate Earned!', 'Congratulations! You have earned a certificate for completing the course.', 'cert')");
        }
    }
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

    .result-container {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 24px;
        padding: 50px;
        text-align: center;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid var(--border-color);
    }

    .result-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        margin: 0 auto 30px;
    }

    .score-circle {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 30px auto;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 8px solid #f1f5f9;
        border-radius: 50%;
    }

    .score-circle h2 {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
        color: var(--text-main);
    }

    .score-circle p {
        margin: 0;
        font-size: 0.9rem;
        color: var(--text-muted);
        font-weight: 600;
    }

    .stat-row {
        display: flex;
        justify-content: space-around;
        margin: 40px 0;
        padding: 20px 0;
        border-top: 1px solid #f1f5f9;
        border-bottom: 1px solid #f1f5f9;
    }

    .stat-item h4 {
        margin: 0;
        font-weight: 700;
    }

    .stat-item p {
        margin: 0;
        font-size: 0.8rem;
        color: var(--text-muted);
    }
</style>

<main class="main-content">
    <div class="result-container">
        <?php if ($passed): ?>
            <div class="result-icon bg-success text-white">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <h1 class="fw-bold">Congratulations!</h1>
            <p class="text-muted">You've successfully passed the assessment and demonstrated your expertise.</p>
        <?php else: ?>
            <div class="result-icon bg-danger text-white">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <h1 class="fw-bold">Keep Trying!</h1>
            <p class="text-muted">You didn't reach the passing score this time. Review the course material and try again.</p>
        <?php endif; ?>

        <div class="score-circle" style="border-top-color: <?php echo $passed ? '#22c55e' : '#ef4444'; ?>;">
            <h2><?php echo $percentage; ?>%</h2>
            <p>SCORE</p>
        </div>

        <div class="stat-row">
            <div class="stat-item">
                <h4><?php echo $correct_answers; ?>/<?php echo $total_questions; ?></h4>
                <p>Correct Answers</p>
            </div>
            <div class="stat-item">
                <h4><?php echo $obtained_marks; ?>/<?php echo $total_marks; ?></h4>
                <p>Total Marks</p>
            </div>
        </div>

        <div class="d-grid gap-3">
            <?php if ($cert_eligible): ?>
                <a href="certificates.php" class="btn btn-primary py-3 fw-bold" style="border-radius: 12px;">Get Your Certificate</a>
            <?php endif; ?>
            <a href="learn.php?id=<?php echo $course_id; ?>" class="btn btn-outline-secondary py-3 fw-bold" style="border-radius: 12px;">Return to Course</a>
            <?php if (!$passed): ?>
                <a href="quiz.php?course_id=<?php echo $course_id; ?>" class="btn btn-outline-primary py-3 fw-bold" style="border-radius: 12px;">Retry Quiz</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
