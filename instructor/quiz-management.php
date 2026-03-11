<?php
require '../includes/scripts/connection.php';
session_start();
if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
    $user_id  = $_SESSION['educat_logedin_user_id'];
    $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
    if($userdata["role"] != 2) header("Location: ../404.php");
} else { header("Location: ../sign-in.php"); }

// CREATE QUIZ
if(isset($_POST['create_quiz'])){
    $course_id  = intval($_POST['course_id']);
    $quiz_name  = mysqli_real_escape_string($conn, $_POST['quiz_name']);
    $pass_marks = intval($_POST['pass_marks']);
    if(mysqli_query($conn, "INSERT INTO `quiz_master`(`quiz_for_course`,`quiz_name`,`quiz_total_marks`,`quiz_added_by`,`quiz_total_questions`) VALUES ($course_id,'$quiz_name',$pass_marks,$user_id,0)")){
        $_SESSION['quiz_success'] = "Quiz created successfully!";
    }
    header("Location: quiz-management.php"); exit;
}

// UPDATE QUIZ
if(isset($_POST['update_quiz'])){
    $quiz_id    = intval($_POST['quiz_id']);
    $quiz_name  = mysqli_real_escape_string($conn, $_POST['quiz_name']);
    $pass_marks = intval($_POST['pass_marks']);
    if(mysqli_query($conn, "UPDATE `quiz_master` SET `quiz_name`='$quiz_name', `quiz_total_marks`=$pass_marks WHERE `quiz_id`='$quiz_id' AND `quiz_added_by`='$user_id'")){
        $_SESSION['quiz_success'] = "Quiz updated successfully!";
    } else { $_SESSION['quiz_error'] = "Failed to update quiz."; }
    header("Location: quiz-management.php"); exit;
}

// DELETE QUIZ
if(isset($_GET['delete_quiz'])){
    $quiz_id = intval($_GET['delete_quiz']);
    mysqli_query($conn, "DELETE FROM quiz_questions_master WHERE quiz_id = '$quiz_id'");
    if(mysqli_query($conn, "DELETE FROM quiz_master WHERE quiz_id = '$quiz_id' AND quiz_added_by = '$user_id'")){
        $_SESSION['quiz_success'] = "Quiz deleted.";
    }
    header("Location: quiz-management.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Management — EduCat Instructor</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="../dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
    <style>
        .qz-modal-overlay { display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:3000;align-items:center;justify-content:center; }
        .qz-modal-overlay.show { display:flex; }
        .qz-modal { background:#fff;border-radius:var(--radius-lg);padding:32px;width:100%;max-width:480px;box-shadow:var(--shadow-lg);max-height:92vh;overflow-y:auto; }
        .qz-confirm { background:#fff;border-radius:var(--radius-lg);padding:40px 32px;width:100%;max-width:400px;box-shadow:var(--shadow-lg);text-align:center; }
        .qz-confirm i { font-size:2.8rem;margin-bottom:16px;display:block; }
        .qz-modal__header { display:flex;justify-content:space-between;align-items:center;margin-bottom:22px; }
        .qz-modal__title { margin:0;font-family:'Poppins',sans-serif;font-weight:700;font-size:1.05rem; }
        .qz-close { background:none;border:none;font-size:1.3rem;cursor:pointer;color:var(--text-muted); }
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
                    <h1 class="ins-page-title">Quiz Management</h1>
                    <p class="ins-page-subtitle">Create, edit, and delete quizzes for your courses</p>
                </div>
                <button class="ins-btn ins-btn-primary" onclick="openQzModal('createModal')"><i class="fas fa-plus"></i> Create New Quiz</button>
            </div>

            <?php if(isset($_SESSION['quiz_success'])): ?>
            <div style="background:#D1FAE5;color:#065F46;padding:13px 18px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['quiz_success']; unset($_SESSION['quiz_success']); ?>
            </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['quiz_error'])): ?>
            <div style="background:var(--danger-light);color:var(--danger);padding:13px 18px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['quiz_error']; unset($_SESSION['quiz_error']); ?>
            </div>
            <?php endif; ?>

            <div class="grid grid-2">
            <?php
            $sql = "SELECT q.*, c.course_name FROM quiz_master q JOIN course_master c ON q.quiz_for_course = c.course_id WHERE q.quiz_added_by = $user_id ORDER BY q.quiz_id DESC";
            $res = mysqli_query($conn, $sql);
            if($res && mysqli_num_rows($res) > 0):
                while($row = mysqli_fetch_assoc($res)):
            ?>
            <div class="ins-card">
                <div class="ins-card__body">
                    <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:12px;">
                        <h4 style="font-weight:700;margin:0;font-family:'Poppins',sans-serif;font-size:1rem;color:var(--text);"><?php echo htmlspecialchars($row['quiz_name']); ?></h4>
                        <span class="ins-badge ins-badge-primary"><i class="fas fa-clipboard-list"></i> Quiz</span>
                    </div>
                    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:20px;">
                        <span style="font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                            <i class="fas fa-book" style="color:var(--primary);"></i> <?php echo htmlspecialchars($row['course_name']); ?>
                        </span>
                        <span style="font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                            <i class="fas fa-question-circle" style="color:#8B5CF6;"></i> <?php echo $row['quiz_total_questions']; ?> Questions
                        </span>
                        <span style="font-size:.8rem;color:var(--text-muted);display:flex;align-items:center;gap:6px;">
                            <i class="fas fa-check-double" style="color:var(--success);"></i> Pass: <?php echo $row['quiz_total_marks']; ?> marks
                        </span>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border-light);">
                        <a href="quiz-add-information.php?id=<?php echo $row['quiz_id']; ?>" class="ins-btn ins-btn-primary ins-btn-sm"><i class="fas fa-list"></i> Manage Questions</a>
                        <button class="ins-btn ins-btn-outline ins-btn-sm"
                            onclick="openEditQuiz(<?php echo $row['quiz_id'];?>, '<?php echo addslashes(htmlspecialchars($row['quiz_name']));?>', <?php echo $row['quiz_total_marks'];?>)">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button class="ins-btn-icon danger" title="Delete Quiz"
                            onclick="confirmDeleteQuiz(<?php echo $row['quiz_id'];?>, '<?php echo addslashes(htmlspecialchars($row['quiz_name']));?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; else: ?>
            <div style="grid-column:1/-1;">
                <div class="ins-card">
                    <div class="ins-empty">
                        <i class="fas fa-clipboard-list"></i>
                        <h5>No quizzes yet</h5>
                        <p>Create quizzes to assess students and award certificates.</p>
                        <button class="ins-btn ins-btn-primary mt-16" onclick="openQzModal('createModal')"><i class="fas fa-plus"></i> Create First Quiz</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<!-- ── MODAL: CREATE QUIZ ── -->
<div id="createModal" class="qz-modal-overlay" onclick="if(event.target===this)closeQzModal('createModal')">
    <div class="qz-modal">
        <div class="qz-modal__header">
            <h4 class="qz-modal__title"><i class="fas fa-plus" style="color:var(--primary);margin-right:8px;"></i>Create New Quiz</h4>
            <button class="qz-close" onclick="closeQzModal('createModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="ins-form-group">
                <label class="ins-form-label">For Course <span class="required">*</span></label>
                <select name="course_id" class="ins-form-control" required>
                    <option value="" disabled selected>Select Course</option>
                    <?php
                    $c_res = mysqli_query($conn, "SELECT course_id, course_name FROM course_master WHERE course_instructor = $user_id AND course_approved_by_admin = 1");
                    if($c_res) while($c_row = mysqli_fetch_assoc($c_res)) echo "<option value='{$c_row['course_id']}'>{$c_row['course_name']}</option>";
                    ?>
                </select>
            </div>
            <div class="ins-form-group">
                <label class="ins-form-label">Quiz Title <span class="required">*</span></label>
                <input type="text" name="quiz_name" class="ins-form-control" placeholder="e.g. React Fundamentals Test" required>
            </div>
            <div class="ins-form-group">
                <label class="ins-form-label">Passing Marks <span class="required">*</span></label>
                <input type="number" name="pass_marks" class="ins-form-control" min="1" placeholder="e.g. 60" required>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeQzModal('createModal')" class="ins-btn ins-btn-outline">Cancel</button>
                <button type="submit" name="create_quiz" class="ins-btn ins-btn-primary"><i class="fas fa-plus"></i> Create Quiz</button>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL: EDIT QUIZ ── -->
<div id="editModal" class="qz-modal-overlay" onclick="if(event.target===this)closeQzModal('editModal')">
    <div class="qz-modal">
        <div class="qz-modal__header">
            <h4 class="qz-modal__title"><i class="fas fa-pen" style="color:var(--primary);margin-right:8px;"></i>Edit Quiz</h4>
            <button class="qz-close" onclick="closeQzModal('editModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="quiz_id" id="editQuizId">
            <div class="ins-form-group">
                <label class="ins-form-label">Quiz Title <span class="required">*</span></label>
                <input type="text" name="quiz_name" id="editQuizName" class="ins-form-control" required>
            </div>
            <div class="ins-form-group">
                <label class="ins-form-label">Passing Marks <span class="required">*</span></label>
                <input type="number" name="pass_marks" id="editQuizMarks" class="ins-form-control" min="1" required>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeQzModal('editModal')" class="ins-btn ins-btn-outline">Cancel</button>
                <button type="submit" name="update_quiz" class="ins-btn ins-btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ── MODAL: CONFIRM DELETE ── -->
<div id="deleteModal" class="qz-modal-overlay" onclick="if(event.target===this)closeQzModal('deleteModal')">
    <div class="qz-confirm">
        <i class="fas fa-trash-alt" style="color:var(--danger);"></i>
        <h4 style="font-weight:700;margin-bottom:8px;">Delete Quiz?</h4>
        <p id="deleteQuizMsg" style="color:var(--text-muted);font-size:.875rem;margin-bottom:16px;"></p>
        <div style="background:var(--danger-light);color:var(--danger);border-radius:var(--radius-sm);padding:10px 14px;font-size:.8rem;font-weight:600;margin-bottom:24px;">
            <i class="fas fa-exclamation-triangle"></i> All questions in this quiz will also be deleted.
        </div>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="closeQzModal('deleteModal')" class="ins-btn ins-btn-outline">Cancel</button>
            <a id="deleteQuizLink" href="#" class="ins-btn ins-btn-danger"><i class="fas fa-trash"></i> Delete</a>
        </div>
    </div>
</div>

<script>
function openQzModal(id)  { document.getElementById(id).classList.add('show'); }
function closeQzModal(id) { document.getElementById(id).classList.remove('show'); }

function openEditQuiz(quizId, name, marks){
    document.getElementById('editQuizId').value    = quizId;
    document.getElementById('editQuizName').value  = name;
    document.getElementById('editQuizMarks').value = marks;
    openQzModal('editModal');
}

function confirmDeleteQuiz(quizId, name){
    document.getElementById('deleteQuizMsg').textContent = 'You are about to permanently delete "' + name + '".';
    document.getElementById('deleteQuizLink').href = 'quiz-management.php?delete_quiz=' + quizId;
    openQzModal('deleteModal');
}

document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
        document.querySelectorAll('.qz-modal-overlay.show').forEach(function(m){ m.classList.remove('show'); });
    }
});
</script>
<?php include("includes/footer.php"); ?>
</body>
</html>
