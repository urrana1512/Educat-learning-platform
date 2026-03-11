<?php
include 'header.php';
// We don't include sidebar.php here because we need a custom layout for the player

if (!isset($_GET['id'])) {
    header("Location: my-courses.php");
    exit();
}

$course_id = $_GET['id'];

// Check if user has purchased this course
$val_query = "SELECT * FROM purchased_course_master WHERE user_id = $user_id AND course_id = $course_id";
$val_res = mysqli_query($conn, $val_query);
if (mysqli_num_rows($val_res) == 0) {
    header("Location: ../course-overview.php?id=$course_id");
    exit();
}
$enrollment_data = mysqli_fetch_assoc($val_res);

// Fetch Course Details
$course_query = "SELECT c.*, u.user_name as instructor_name FROM course_master c JOIN user_master u ON c.course_instructor = u.user_id WHERE c.course_id = $course_id";
$course_res = mysqli_query($conn, $course_query);
$course = mysqli_fetch_assoc($course_res);

// Fetch Lessons/Videos
$lessons = [];
$chapters_query = "SELECT * FROM course_chapter_list WHERE course_id = $course_id ORDER BY course_chapter_id ASC";
$chapters_res = mysqli_query($conn, $chapters_query);
while ($chapter = mysqli_fetch_assoc($chapters_res)) {
    $chap_id = $chapter['course_chapter_id'];
    $videos_query = "SELECT * FROM videos_master WHERE video_of_chapter = $chap_id ORDER BY video_id ASC";
    $videos_res = mysqli_query($conn, $videos_query);
    $chap_videos = [];
    while ($video = mysqli_fetch_assoc($videos_res)) {
        // Check if lesson is completed
        $vid_id = $video['video_id'];
        $prog_query = "SELECT * FROM lesson_progress WHERE user_id = $user_id AND lesson_id = $vid_id";
        $prog_res = mysqli_query($conn, $prog_query);
        $video['is_completed'] = (mysqli_num_rows($prog_res) > 0);
        $chap_videos[] = $video;
    }
    $chapter['videos'] = $chap_videos;
    $lessons[] = $chapter;
}

// Current Lesson (Default to first if not set)
$current_video_id = isset($_GET['videoId']) ? $_GET['videoId'] : null;
$current_video = null;

if (!$current_video_id && !empty($lessons) && !empty($lessons[0]['videos'])) {
    $current_video = $lessons[0]['videos'][0];
    $current_video_id = $current_video['video_id'];
} elseif ($current_video_id) {
    $cv_query = "SELECT * FROM videos_master WHERE video_id = $current_video_id";
    $cv_res = mysqli_query($conn, $cv_query);
    $current_video = mysqli_fetch_assoc($cv_res);
}

// Fetch resources for current lesson
$resources_res = mysqli_query($conn, "SELECT * FROM lesson_resources WHERE lesson_id = " . ($current_video_id ?: 0));

// Fetch notes for current lesson
$notes_res = mysqli_query($conn, "SELECT * FROM student_notes WHERE user_id = $user_id AND lesson_id = " . ($current_video_id ?: 0));
$lesson_note = mysqli_fetch_assoc($notes_res);
?>

<style>
    body {
        background-color: #0f172a; /* Dark background for player */
        color: #f8fafc;
    }

    .player-container {
        display: flex;
        height: calc(100vh - 70px);
        margin-top: 70px;
        overflow: hidden;
    }

    .main-player-area {
        flex: 1;
        overflow-y: auto;
        padding: 0;
        background: #020617;
    }

    .sidebar-curriculum {
        width: 380px;
        background: #1e293b;
        border-left: 1px solid #334155;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .video-wrapper {
        width: 100%;
        max-height: 65vh;
        background: black;
        position: relative;
    }

    video {
        width: 100%;
        height: 100%;
        max-height: 65vh;
        outline: none;
    }

    .lesson-meta {
        padding: 25px 40px;
        background: #0f172a;
    }

    .lesson-meta h2 {
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 20px;
    }

    .tabs-nav {
        display: flex;
        gap: 30px;
        border-bottom: 1px solid #334155;
        margin-bottom: 25px;
    }

    .tab-item {
        padding: 12px 5px;
        cursor: pointer;
        color: #94a3b8;
        font-weight: 600;
        font-size: 0.95rem;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }

    .tab-item.active {
        color: #6366f1;
        border-bottom-color: #6366f1;
    }

    .tab-content-panel {
        display: none;
    }

    .tab-content-panel.active {
        display: block;
    }

    /* Sidebar Content */
    .sidebar-header {
        padding: 20px;
        background: #1e293b;
        border-bottom: 1px solid #334155;
    }

    .sidebar-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .accordion-item {
        background: transparent !important;
        border: none !important;
        border-bottom: 1px solid #334155 !important;
    }

    .accordion-button {
        background: #1e293b !important;
        color: #f8fafc !important;
        font-weight: 600 !important;
        padding: 15px 20px !important;
        box-shadow: none !important;
    }

    .accordion-button:not(.collapsed) {
        background: #1e293b !important;
        color: #6366f1 !important;
    }

    .accordion-button::after {
        filter: brightness(0) invert(1);
    }

    .lesson-item {
        padding: 12px 20px 12px 45px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s;
        position: relative;
    }

    .lesson-item:hover {
        background: #334155;
        color: white;
    }

    .lesson-item.active {
        background: #4f46e5;
        color: white;
    }

    .lesson-item i.status-icon {
        position: absolute;
        left: 20px;
        font-size: 0.8rem;
    }

    /* Notes Feature */
    .notes-box {
        background: #1e293b;
        border: 1px solid #334155;
        border-radius: 12px;
        padding: 15px;
    }

    .notes-box textarea {
        background: transparent;
        border: none;
        color: #f8fafc;
        width: 100%;
        min-height: 150px;
        outline: none;
        font-family: inherit;
        resize: vertical;
    }

    .not-found-player {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 60vh;
        text-align: center;
    }

    @media (max-width: 992px) {
        .player-container {
            flex-direction: column;
            height: auto;
            overflow: visible;
        }
        .sidebar-curriculum {
            width: 100%;
            height: 500px;
        }
        .main-player-area {
            height: auto;
        }
    }
</style>

<div class="player-container">
    <!-- Main Player Area -->
    <div class="main-player-area">
        <?php if ($current_video): ?>
            <div class="video-wrapper">
                <video id="courseVideo" controls controlsList="nodownload">
                    <source src="../<?php echo $current_video['video_path']; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            
            <div class="lesson-meta">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h2><?php echo $current_video['video_name']; ?></h2>
                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3" id="markCompleteBtn">
                        <i class="fa-solid fa-check me-1"></i> Mark as Completed
                    </button>
                </div>

                <div class="tabs-nav">
                    <div class="tab-item active" data-tab="overview">Overview</div>
                    <div class="tab-item" data-tab="resources">Resources</div>
                    <div class="tab-item" data-tab="notes">My Notes</div>
                </div>

                <div id="overview" class="tab-content-panel active">
                    <div class="instructor-box d-flex align-items-center gap-3 mb-4">
                        <img src="../assets/img/EduCat (4)_rm.png" alt="Instructor" style="width: 45px; height: 45px; border-radius: 50%;">
                        <div>
                            <p class="mb-0 fw-bold"><?php echo $course['instructor_name']; ?></p>
                            <p class="mb-0 text-muted" style="font-size: 0.8rem;">Course Instructor</p>
                        </div>
                    </div>
                    <h5>About this lesson</h5>
                    <p class="text-secondary"><?php echo $course['course_description']; ?></p> <!-- Placeholder: Real implementation would have lesson description -->
                </div>

                <div id="resources" class="tab-content-panel">
                    <h5>Downloadable Materials</h5>
                    <div class="row mt-3">
                        <?php if (mysqli_num_rows($resources_res) > 0): ?>
                            <?php while ($res = mysqli_fetch_assoc($resources_res)): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="p-3 rounded-3 border border-secondary bg-dark d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <i class="fa-solid fa-file-pdf text-danger fs-4"></i>
                                            <div>
                                                <p class="mb-0 fw-bold" style="font-size: 0.9rem;"><?php echo $res['file_name']; ?></p>
                                                <p class="mb-0 text-muted" style="font-size: 0.75rem;">PDF • 2.4 MB</p>
                                            </div>
                                        </div>
                                        <a href="../<?php echo $res['file_path']; ?>" download class="btn btn-sm btn-link text-primary"><i class="fa-solid fa-download"></i></a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No resources available for this lesson.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="notes" class="tab-content-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Course Notes</h5>
                        <button class="btn btn-primary btn-sm px-3" id="saveNoteBtn" style="border-radius: 8px;">Save Note</button>
                    </div>
                    <div class="notes-box">
                        <textarea id="lessonNoteInput" placeholder="Write your personal notes here..."><?php echo $lesson_note ? $lesson_note['note_content'] : ''; ?></textarea>
                    </div>
                    <p class="text-muted mt-2" style="font-size: 0.8rem;"><i class="fa-solid fa-circle-info me-1"></i> Notes are automatically saved to your profile for this specific lesson.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="not-found-player">
                <i class="fa-regular fa-folder-open fs-1 text-muted mb-3"></i>
                <h4>No Lessons Found</h4>
                <p class="text-muted">This course doesn't have any lessons uploaded yet.</p>
                <a href="my-courses.php" class="btn btn-primary btn-sm px-4 mt-2">Go Back</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sidebar Curriculum -->
    <div class="sidebar-curriculum">
        <div class="sidebar-header">
            <h5>Course Content</h5>
            <div class="mt-2" style="font-size: 0.8rem; color: #94a3b8;">
                <span class="text-primary fw-bold"><?php echo $enrollment_data['course_progress']; ?>%</span> Complete
            </div>
        </div>
        
        <div class="accordion" id="curriculumAccordion">
            <?php foreach ($lessons as $index => $chap): ?>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?php echo $index == 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#chap<?php echo $chap['course_chapter_id']; ?>">
                            Chapter <?php echo $index + 1; ?>: <?php echo $chap['course_chapter_name']; ?>
                        </button>
                    </h2>
                    <div id="chap<?php echo $chap['course_chapter_id']; ?>" class="accordion-collapse collapse <?php echo $index == 0 ? 'show' : ''; ?>" data-bs-parent="#curriculumAccordion">
                        <div class="accordion-body p-0">
                            <?php foreach ($chap['videos'] as $vid): ?>
                                <a href="?id=<?php echo $course_id; ?>&videoId=<?php echo $vid['video_id']; ?>" class="lesson-item <?php echo $vid['video_id'] == $current_video_id ? 'active' : ''; ?>">
                                    <?php if ($vid['is_completed']): ?>
                                        <i class="fa-solid fa-circle-check text-success status-icon"></i>
                                    <?php else: ?>
                                        <i class="fa-regular fa-circle status-icon"></i>
                                    <?php endif; ?>
                                    <span><?php echo $vid['video_name']; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Final Quiz Placeholder -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <a href="quiz.php?course_id=<?php echo $course_id; ?>" class="accordion-button collapsed text-decoration-none" style="display: block;">
                        <i class="fa-solid fa-list-check me-2"></i> Final Assessment
                    </a>
                </h2>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab Switching Logic
    document.querySelectorAll('.tab-item').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content-panel').forEach(c => c.classList.remove('active'));
            
            tab.classList.add('active');
            document.getElementById(tab.dataset.tab).classList.add('active');
        });
    });

    // Save Notes Logic
    document.getElementById('saveNoteBtn')?.addEventListener('click', function() {
        const note = document.getElementById('lessonNoteInput').value;
        const lessonId = <?php echo $current_video_id ?: 0; ?>;
        
        $.ajax({
            url: 'ajax_handlers.php',
            type: 'POST',
            data: {
                action: 'save_note',
                lesson_id: lessonId,
                note_content: note
            },
            success: function(response) {
                alert('Note saved successfully!');
            }
        });
    });

    // Mark Complete Logic
    document.getElementById('markCompleteBtn')?.addEventListener('click', function() {
        const lessonId = <?php echo $current_video_id ?: 0; ?>;
        const courseId = <?php echo $course_id; ?>;
        
        $.ajax({
            url: 'ajax_handlers.php',
            type: 'POST',
            data: {
                action: 'mark_complete',
                lesson_id: lessonId,
                course_id: courseId
            },
            success: function(response) {
                window.location.reload();
            }
        });
    });
</script>

<?php include 'footer.php'; ?>
