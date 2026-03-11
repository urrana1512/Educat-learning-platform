<?php
include 'header.php';
include 'sidebar.php';

if (!isset($_GET['course_id'])) {
    header("Location: my-courses.php");
    exit();
}

$course_id = $_GET['course_id'];

// Fetch Quizzes for this course
$quiz_query = "SELECT * FROM quiz_master WHERE quiz_for_course = $course_id";
$quiz_res = mysqli_query($conn, $quiz_query);
$quiz = mysqli_fetch_assoc($quiz_res);

if (!$quiz) {
    echo "<main class='main-content'><div class='container'><div class='alert alert-info'>No quiz available for this course yet.</div></div></main>";
    include 'footer.php';
    exit();
}

$quiz_id = $quiz['quiz_id'];
$questions_query = "SELECT * FROM quiz_question_master WHERE quiz_id = $quiz_id";
$questions_res = mysqli_query($conn, $questions_query);
$questions = [];
while ($row = mysqli_fetch_assoc($questions_res)) {
    $questions[] = $row;
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

    .quiz-container {
        max-width: 800px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid var(--border-color);
    }

    .quiz-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .quiz-header h2 {
        font-weight: 700;
        color: var(--text-main);
    }

    .question-box {
        margin-bottom: 35px;
        padding-bottom: 30px;
        border-bottom: 1px solid #f1f5f9;
        display: none; /* Hide for step-by-step */
    }

    .question-box.active {
        display: block;
    }

    .question-text {
        font-weight: 600;
        font-size: 1.15rem;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
    }

    .question-num {
        background: #eef2ff;
        color: var(--primary-color);
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .options-list {
        display: grid;
        gap: 12px;
    }

    .option-item {
        border: 1px solid var(--border-color);
        padding: 15px 20px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .option-item:hover {
        border-color: var(--primary-color);
        background: #f8fafc;
    }

    .option-item.selected {
        border-color: var(--primary-color);
        background: #eef2ff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .option-radio {
        width: 18px;
        height: 18px;
        border: 2px solid #cbd5e1;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .option-item.selected .option-radio {
        border-color: var(--primary-color);
        background: var(--primary-color);
    }

    .option-item.selected .option-radio::after {
        content: '';
        width: 6px;
        height: 6px;
        background: white;
        border-radius: 50%;
    }

    .quiz-navigation {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
    }

    .progress-dots {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 30px;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #e2e8f0;
    }

    .dot.active {
        background: var(--primary-color);
        transform: scale(1.2);
    }

    .dot.completed {
        background: #6366f1;
        opacity: 0.5;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="quiz-container">
            <div class="quiz-header">
                <h2><?php echo $quiz['quiz_name']; ?></h2>
                <p class="text-muted">Answer all questions to complete the assessment.</p>
            </div>

            <div class="progress-dots">
                <?php for($i=0; $i<count($questions); $i++): ?>
                    <div class="dot <?php echo $i == 0 ? 'active' : ''; ?>" id="dot-<?php echo $i; ?>"></div>
                <?php endfor; ?>
            </div>

            <form id="quizForm" action="quiz-result.php" method="POST">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                
                <?php foreach ($questions as $index => $q): ?>
                    <div class="question-box <?php echo $index == 0 ? 'active' : ''; ?>" id="q-<?php echo $index; ?>" data-q-id="<?php echo $q['quiz_question_id']; ?>">
                        <div class="question-text">
                            <span class="question-num"><?php echo $index + 1; ?></span>
                            <span><?php echo $q['question']; ?></span>
                        </div>
                        <div class="options-list">
                            <?php for($opt=1; $opt<=4; $opt++): 
                                $opt_text = "";
                                if($opt == 1) $opt_text = $q['option_one'];
                                if($opt == 2) $opt_text = $q['option_two'];
                                if($opt == 3) $opt_text = $q['option_three'];
                                if($opt == 4) $opt_text = $q['option_four'];
                            ?>
                                <label class="option-item">
                                    <input type="radio" name="answer[<?php echo $q['quiz_question_id']; ?>]" value="<?php echo $opt; ?>" class="d-none quiz-radio">
                                    <div class="option-radio"></div>
                                    <span><?php echo $opt_text; ?></span>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="quiz-navigation">
                    <button type="button" class="btn btn-outline-secondary px-4" id="prevBtn" disabled style="border-radius: 10px;">Previous</button>
                    <?php if (count($questions) > 1): ?>
                        <button type="button" class="btn btn-primary px-4" id="nextBtn" style="border-radius: 10px;">Next Question</button>
                        <button type="submit" class="btn btn-success px-4 d-none" id="submitBtn" style="border-radius: 10px;">Submit Quiz</button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-success px-4" id="submitBtn" style="border-radius: 10px;">Submit Quiz</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    let currentStep = 0;
    const totalSteps = <?php echo count($questions); ?>;

    document.querySelectorAll('.option-item').forEach(item => {
        item.addEventListener('click', function() {
            // Find parent question box
            const box = this.closest('.question-box');
            box.querySelectorAll('.option-item').forEach(i => i.classList.remove('selected'));
            this.classList.add('selected');
        });
    });

    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentStep < totalSteps - 1) {
                document.getElementById(`q-${currentStep}`).classList.remove('active');
                document.getElementById(`dot-${currentStep}`).classList.remove('active');
                document.getElementById(`dot-${currentStep}`).classList.add('completed');
                
                currentStep++;
                
                document.getElementById(`q-${currentStep}`).classList.add('active');
                document.getElementById(`dot-${currentStep}`).classList.add('active');
                
                prevBtn.disabled = false;
                
                if (currentStep === totalSteps - 1) {
                    nextBtn.classList.add('d-none');
                    submitBtn.classList.remove('d-none');
                }
            }
        });
    }

    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) {
            document.getElementById(`q-${currentStep}`).classList.remove('active');
            document.getElementById(`dot-${currentStep}`).classList.remove('active');
            
            currentStep--;
            
            document.getElementById(`q-${currentStep}`).classList.add('active');
            document.getElementById(`dot-${currentStep}`).classList.add('active');
            document.getElementById(`dot-${currentStep}`).classList.remove('completed');
            
            nextBtn.classList.remove('d-none');
            submitBtn.classList.add('d-none');
            
            if (currentStep === 0) {
                prevBtn.disabled = true;
            }
        }
    });
</script>

<?php include 'footer.php'; ?>
