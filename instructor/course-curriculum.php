<?php
require '../includes/scripts/connection.php';
session_start();
if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
    $user_id  = $_SESSION['educat_logedin_user_id'];
    $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
    if($userdata["role"] != 2) header("Location: ../404.php");
} else { header("Location: ../sign-in.php"); }

$course_id = isset($_GET["course"]) ? intval($_GET["course"]) : 0;
if($course_id == 0){ header("Location: my-courses.php"); exit; }

/* ──────────────────────────────────────────────
   SECTION ACTIONS
────────────────────────────────────────────── */

// ADD SECTION
if(isset($_POST["add_section"])){
    $st = mysqli_real_escape_string($conn, $_POST["section_title"]);
    $sd = mysqli_real_escape_string($conn, $_POST["section_description"]);
    if(mysqli_query($conn, "INSERT INTO `course_chapter_list`(`course_chapter_name`,`course_chapter_description`,`course_id`) VALUES ('$st','$sd','$course_id')")){
        mysqli_query($conn, "UPDATE course_master SET course_chapters = course_chapters + 1 WHERE course_id = '$course_id'");
        
        // Notify students
        $c_name_query = mysqli_query($conn, "SELECT course_name FROM course_master WHERE course_id = $course_id");
        $course_name_fetch = mysqli_fetch_assoc($c_name_query)['course_name'] ?? 'Course';
        $ins_name = $userdata['user_name'];
        $notif_title = "New Section Added!";
        $notif_msg = "$ins_name added a new section '$st' to '$course_name_fetch'.";
        
        $enrolled = mysqli_query($conn, "SELECT user_id FROM purchased_course_master WHERE course_id = $course_id");
        while($es = mysqli_fetch_assoc($enrolled)){
            $sid = $es['user_id'];
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($sid, '$notif_title', '$notif_msg', 'course')");
        }
        
        $_SESSION["curriculum_success"] = "Section added successfully!";
    } else { $_SESSION["curriculum_error"] = "Failed to add section."; }
    header("Location: course-curriculum.php?course=$course_id"); exit;
}

// UPDATE SECTION
if(isset($_POST["update_section"])){
    $ch_id = intval($_POST["chapter_id"]);
    $st    = mysqli_real_escape_string($conn, $_POST["section_title"]);
    $sd    = mysqli_real_escape_string($conn, $_POST["section_description"]);
    if(mysqli_query($conn, "UPDATE `course_chapter_list` SET `course_chapter_name`='$st', `course_chapter_description`='$sd' WHERE `course_chapter_id`='$ch_id' AND `course_id`='$course_id'")){
        $_SESSION["curriculum_success"] = "Section updated successfully!";
    } else { $_SESSION["curriculum_error"] = "Failed to update section."; }
    header("Location: course-curriculum.php?course=$course_id"); exit;
}

// DELETE SECTION
if(isset($_GET["delete_section"])){
    $ch_id = intval($_GET["delete_section"]);
    // Delete all videos in this chapter first
    $vids = mysqli_query($conn, "SELECT video_id FROM videos_master WHERE video_of_chapter = '$ch_id'");
    $vid_count = mysqli_num_rows($vids);
    mysqli_query($conn, "DELETE FROM videos_master WHERE video_of_chapter = '$ch_id'");
    mysqli_query($conn, "DELETE FROM course_chapter_list WHERE course_chapter_id = '$ch_id' AND course_id = '$course_id'");
    mysqli_query($conn, "UPDATE course_master SET course_chapters = GREATEST(course_chapters - 1, 0), course_videos = GREATEST(course_videos - $vid_count, 0) WHERE course_id = '$course_id'");
    $_SESSION["curriculum_success"] = "Section and all its lessons deleted.";
    header("Location: course-curriculum.php?course=$course_id"); exit;
}

/* ──────────────────────────────────────────────
   LESSON ACTIONS
────────────────────────────────────────────── */

// UPDATE LESSON (name only – file stays same unless re-uploaded)
if(isset($_POST["update_lesson"])){
    $vid_id  = intval($_POST["video_id"]);
    $ch_id   = intval($_POST["chapter_id"]);
    $vname   = mysqli_real_escape_string($conn, $_POST["video_name"]);
    // Handle optional file re-upload
    $file_sql = "";
    if(!empty($_FILES["new_video_file"]["name"])){
        $target_dir = "course/uploads/course_videos/";
        if(!file_exists("../".$target_dir)) mkdir("../".$target_dir, 0777, true);
        $fn   = uniqid()."_".basename($_FILES["new_video_file"]["name"]);
        $tp   = $target_dir.$fn;
        $ext  = strtolower(pathinfo($fn, PATHINFO_EXTENSION));
        if(in_array($ext, ["mp4","webm","m4v"]) && move_uploaded_file($_FILES["new_video_file"]["tmp_name"], "../".$tp)){
            $file_sql = ", `video_path`='$tp'";
        }
    }
    if(mysqli_query($conn, "UPDATE `videos_master` SET `video_name`='$vname'$file_sql WHERE `video_id`='$vid_id' AND `video_of_chapter`='$ch_id'")){
        $_SESSION["curriculum_success"] = "Lesson updated successfully!";
    } else { $_SESSION["curriculum_error"] = "Failed to update lesson."; }
    header("Location: course-curriculum.php?course=$course_id"); exit;
}

// DELETE LESSON
if(isset($_GET["delete_lesson"])){
    $vid_id = intval($_GET["delete_lesson"]);
    $ch_id  = intval($_GET["ch"]);
    mysqli_query($conn, "DELETE FROM videos_master WHERE video_id = '$vid_id'");
    mysqli_query($conn, "UPDATE course_chapter_list SET chapter_total_videos = GREATEST(chapter_total_videos - 1, 0) WHERE course_chapter_id = '$ch_id'");
    mysqli_query($conn, "UPDATE course_master SET course_videos = GREATEST(course_videos - 1, 0) WHERE course_id = '$course_id'");
    $_SESSION["curriculum_success"] = "Lesson deleted successfully.";
    header("Location: course-curriculum.php?course=$course_id"); exit;
}

// Get course name
$cn_row      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT course_name FROM course_master WHERE course_id = $course_id"));
$course_name = $cn_row['course_name'] ?? 'Course';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Curriculum — EduCat Instructor</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="../dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
    <style>
        /* ─── Modal overlay ─── */
        .cur-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.50);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }
        .cur-modal-overlay.active { display: flex !important; }
        .cur-modal {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 32px;
            width: 100%;
            max-width: 500px;
            box-shadow: var(--shadow-lg);
            max-height: 90vh;
            overflow-y: auto;
            animation: slideUp .2s ease;
        }
        @keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }
        .cur-modal__header { display:flex;justify-content:space-between;align-items:center;margin-bottom:22px; }
        .cur-modal__title  { margin:0;font-family:'Poppins',sans-serif;font-weight:700;font-size:1.05rem; }
        .cur-modal__close  { background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--text-muted);line-height:1;padding:0; }
        /* ─── Confirm box ─── */
        .cur-confirm {
            background: #fff;
            border-radius: var(--radius-lg);
            padding: 40px 32px;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
            text-align: center;
            animation: slideUp .2s ease;
        }
        .cur-confirm > i { font-size:3rem;margin-bottom:14px;display:block; }
        /* ─── Section header: don't make entire div clickable ─── */
        .ins-accordion__header { cursor: default !important; }
        .sec-actions { display:flex;gap:8px;align-items:center;flex-shrink:0; }
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
                    <h1 class="ins-page-title">Course Curriculum</h1>
                    <p class="ins-page-subtitle"><?php echo htmlspecialchars($course_name); ?></p>
                </div>
                <div style="display:flex;gap:10px;">
                    <a href="my-courses.php" class="ins-btn ins-btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                    <button class="ins-btn ins-btn-primary" onclick="openCurModal('sectionModal')"><i class="fas fa-plus"></i> Add Section</button>
                </div>
            </div>

            <?php if(isset($_SESSION["curriculum_success"])): ?>
            <div style="background:#D1FAE5;color:#065F46;padding:13px 18px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION["curriculum_success"]; unset($_SESSION["curriculum_success"]); ?>
            </div>
            <?php endif; ?>
            <?php if(isset($_SESSION["curriculum_error"])): ?>
            <div style="background:var(--danger-light);color:var(--danger);padding:13px 18px;border-radius:var(--radius-sm);margin-bottom:20px;display:flex;align-items:center;gap:10px;font-weight:600;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION["curriculum_error"]; unset($_SESSION["curriculum_error"]); ?>
            </div>
            <?php endif; ?>

            <?php
            $sections = mysqli_query($conn, "SELECT * FROM `course_chapter_list` WHERE `course_id` = '$course_id' ORDER BY course_chapter_id ASC");
            if($sections && mysqli_num_rows($sections) > 0):
                $sIdx = 1;
                while($sec = mysqli_fetch_assoc($sections)):
                    $ch_id = $sec['course_chapter_id'];
            ?>
            <div class="ins-accordion">
                <div class="ins-accordion__header">
                    <div>
                        <p class="ins-accordion__title">
                            <i class="fas fa-layer-group" style="color:var(--primary);margin-right:8px;font-size:.85rem;"></i>
                            Section <?php echo $sIdx; ?>: <?php echo htmlspecialchars($sec['course_chapter_name']); ?>
                        </p>
                        <?php if($sec['course_chapter_description']): ?>
                        <p style="font-size:.78rem;color:var(--text-muted);margin:3px 0 0;"><?php echo htmlspecialchars($sec['course_chapter_description']); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="sec-actions">
                        <a href="content-upload.php?course=<?php echo $course_id;?>&chapter=<?php echo $ch_id;?>" class="ins-btn ins-btn-primary ins-btn-sm" onclick="event.stopPropagation()">
                            <i class="fas fa-plus"></i> Add Lesson
                        </a>
                        <button class="ins-btn ins-btn-outline ins-btn-sm"
                            onclick="event.stopPropagation(); openCurModal('editSectionModal'); fillEditSection(<?php echo $ch_id;?>, '<?php echo addslashes(htmlspecialchars($sec['course_chapter_name']));?>', '<?php echo addslashes(htmlspecialchars($sec['course_chapter_description']));?>')">
                            <i class="fas fa-pen"></i> Edit
                        </button>
                        <button class="ins-btn-icon danger" title="Delete Section"
                            onclick="event.stopPropagation(); openDeleteSection(<?php echo $ch_id;?>, '<?php echo addslashes(htmlspecialchars($sec['course_chapter_name']));?>')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="ins-accordion__body" style="padding:0;">
                    <?php
                    $lessons = mysqli_query($conn, "SELECT * FROM `videos_master` WHERE `video_of_chapter` = '$ch_id' ORDER BY video_id ASC");
                    if($lessons && mysqli_num_rows($lessons) > 0):
                        $lIdx = 1;
                        while($les = mysqli_fetch_assoc($lessons)):
                    ?>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:13px 20px;border-bottom:1px solid var(--border-light);">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:32px;height:32px;border-radius:var(--radius-sm);background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.8rem;flex-shrink:0;">
                                <i class="fas fa-play"></i>
                            </div>
                            <div>
                                <p style="margin:0;font-weight:600;font-size:.875rem;color:var(--text);">Lesson <?php echo $lIdx;?>: <?php echo htmlspecialchars($les['video_name']);?></p>
                                <p style="margin:0;font-size:.72rem;color:var(--text-muted);">
                                    <?php if($les['video_path']): ?>
                                    <i class="fas fa-file-video"></i> <?php echo basename($les['video_path']); ?>
                                    <?php else: ?><i class="fas fa-link"></i> External<?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center;">
                            <button class="ins-btn ins-btn-outline ins-btn-sm"
                                onclick="openCurModal('editLessonModal'); fillEditLesson(<?php echo $les['video_id'];?>, <?php echo $ch_id;?>, '<?php echo addslashes(htmlspecialchars($les['video_name']));?>')">
                                <i class="fas fa-pen"></i> Edit
                            </button>
                            <button class="ins-btn-icon danger" title="Delete Lesson"
                                onclick="openDeleteLesson(<?php echo $les['video_id'];?>, <?php echo $ch_id;?>, '<?php echo addslashes(htmlspecialchars($les['video_name']));?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php $lIdx++; endwhile; else: ?>
                    <div style="text-align:center;padding:24px;color:var(--text-muted);font-style:italic;font-size:.875rem;">
                        No lessons yet — <a href="content-upload.php?course=<?php echo $course_id;?>&chapter=<?php echo $ch_id;?>" style="color:var(--primary);font-weight:600;">Add first lesson</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php $sIdx++; endwhile; else: ?>
            <div class="ins-card">
                <div class="ins-empty">
                    <i class="fas fa-layer-group"></i>
                    <h5>No sections yet</h5>
                    <p>Start building your curriculum by adding your first section.</p>
                    <button class="ins-btn ins-btn-primary mt-16" onclick="openCurModal('sectionModal')"><i class="fas fa-plus"></i> Add First Section</button>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- /ins-content -->
    </div>
</div>

<!-- ────────────── MODAL: ADD SECTION ────────────── -->
<div id="sectionModal" class="cur-modal-overlay" onclick="if(event.target===this)closeCurModal('sectionModal')">
    <div class="cur-modal">
        <div class="cur-modal__header">
            <h4 class="cur-modal__title"><i class="fas fa-layer-group" style="color:var(--primary);margin-right:8px;"></i>Add New Section</h4>
            <button class="cur-modal__close" onclick="closeCurModal('sectionModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <div class="ins-form-group">
                <label class="ins-form-label">Section Title <span class="required">*</span></label>
                <input type="text" name="section_title" class="ins-form-control" placeholder="e.g. Introduction to React" required>
            </div>
            <div class="ins-form-group">
                <label class="ins-form-label">Description</label>
                <textarea name="section_description" class="ins-form-control" rows="3" placeholder="Brief overview of this section..."></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeCurModal('sectionModal')" class="ins-btn ins-btn-outline">Cancel</button>
                <button type="submit" name="add_section" class="ins-btn ins-btn-primary"><i class="fas fa-plus"></i> Add Section</button>
            </div>
        </form>
    </div>
</div>

<!-- ────────────── MODAL: EDIT SECTION ────────────── -->
<div id="editSectionModal" class="cur-modal-overlay" onclick="if(event.target===this)closeCurModal('editSectionModal')">
    <div class="cur-modal">
        <div class="cur-modal__header">
            <h4 class="cur-modal__title"><i class="fas fa-pen" style="color:var(--primary);margin-right:8px;"></i>Edit Section</h4>
            <button class="cur-modal__close" onclick="closeCurModal('editSectionModal')">&times;</button>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="chapter_id" id="editSectionChapterId">
            <div class="ins-form-group">
                <label class="ins-form-label">Section Title <span class="required">*</span></label>
                <input type="text" name="section_title" id="editSectionTitle" class="ins-form-control" required>
            </div>
            <div class="ins-form-group">
                <label class="ins-form-label">Description</label>
                <textarea name="section_description" id="editSectionDesc" class="ins-form-control" rows="3"></textarea>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeCurModal('editSectionModal')" class="ins-btn ins-btn-outline">Cancel</button>
                <button type="submit" name="update_section" class="ins-btn ins-btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ────────────── MODAL: EDIT LESSON ────────────── -->
<div id="editLessonModal" class="cur-modal-overlay" onclick="if(event.target===this)closeCurModal('editLessonModal')">
    <div class="cur-modal">
        <div class="cur-modal__header">
            <h4 class="cur-modal__title"><i class="fas fa-video" style="color:var(--primary);margin-right:8px;"></i>Edit Lesson</h4>
            <button class="cur-modal__close" onclick="closeCurModal('editLessonModal')">&times;</button>
        </div>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="video_id"   id="editLessonVideoId">
            <input type="hidden" name="chapter_id" id="editLessonChapterId">
            <div class="ins-form-group">
                <label class="ins-form-label">Lesson Title <span class="required">*</span></label>
                <input type="text" name="video_name" id="editLessonName" class="ins-form-control" required>
            </div>
            <div class="ins-form-group">
                <label class="ins-form-label">Replace Video File <span style="color:var(--text-muted);font-weight:400;font-size:.75rem;">(optional — leave blank to keep existing)</span></label>
                <div class="ins-upload-area" style="padding:20px;" onclick="document.getElementById('newVideoFile').click()">
                    <div style="font-size:1.5rem;color:var(--primary);margin-bottom:8px;"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div style="font-size:.875rem;color:var(--text-muted);">Click to select new video (MP4, WebM)</div>
                </div>
                <input type="file" id="newVideoFile" name="new_video_file" accept="video/mp4,video/webm" style="display:none;" onchange="showNewFile(this)">
                <p id="newFileName" style="font-size:.8rem;color:var(--secondary);margin-top:8px;display:none;"></p>
            </div>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" onclick="closeCurModal('editLessonModal')" class="ins-btn ins-btn-outline">Cancel</button>
                <button type="submit" name="update_lesson" class="ins-btn ins-btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<!-- ────────────── MODAL: CONFIRM DELETE SECTION ────────────── -->
<div id="confirmDeleteSectionModal" class="cur-modal-overlay" onclick="if(event.target===this)closeCurModal('confirmDeleteSectionModal')">
    <div class="cur-confirm">
        <i class="fas fa-trash-alt" style="color:var(--danger);"></i>
        <h4 style="font-weight:700;margin-bottom:8px;">Delete Section?</h4>
        <p id="deleteSectionMsg" style="color:var(--text-muted);font-size:.875rem;margin-bottom:24px;"></p>
        <div style="background:var(--danger-light);color:var(--danger);border-radius:var(--radius-sm);padding:10px 16px;font-size:.8rem;font-weight:600;margin-bottom:24px;">
            <i class="fas fa-exclamation-triangle"></i> This will permanently delete the section and ALL its lessons.
        </div>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="closeCurModal('confirmDeleteSectionModal')" class="ins-btn ins-btn-outline">Cancel</button>
            <a id="deleteSectionLink" href="#" class="ins-btn ins-btn-danger"><i class="fas fa-trash"></i> Delete Section</a>
        </div>
    </div>
</div>

<!-- ────────────── MODAL: CONFIRM DELETE LESSON ────────────── -->
<div id="confirmDeleteLessonModal" class="cur-modal-overlay" onclick="if(event.target===this)closeCurModal('confirmDeleteLessonModal')">
    <div class="cur-confirm">
        <i class="fas fa-trash-alt" style="color:var(--danger);"></i>
        <h4 style="font-weight:700;margin-bottom:8px;">Delete Lesson?</h4>
        <p id="deleteLessonMsg" style="color:var(--text-muted);font-size:.875rem;margin-bottom:24px;"></p>
        <div style="display:flex;gap:12px;justify-content:center;">
            <button onclick="closeCurModal('confirmDeleteLessonModal')" class="ins-btn ins-btn-outline">Cancel</button>
            <a id="deleteLessonLink" href="#" class="ins-btn ins-btn-danger"><i class="fas fa-trash"></i> Delete Lesson</a>
        </div>
    </div>
</div>

<script>
/* ===========================================================
   Curriculum Modals — pure vanilla JS, no jQuery dependency
   =========================================================== */

function openCurModal(id) {
    var el = document.getElementById(id);
    if (el) {
        el.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeCurModal(id) {
    var el = document.getElementById(id);
    if (el) {
        el.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function closeAllCurModals() {
    document.querySelectorAll('.cur-modal-overlay.active').forEach(function(m) {
        m.classList.remove('active');
    });
    document.body.style.overflow = '';
}

// ── Fill + open: Edit Section ──────────────────────────────────
function fillEditSection(chId, title, desc) {
    document.getElementById('editSectionChapterId').value = chId;
    document.getElementById('editSectionTitle').value     = title;
    document.getElementById('editSectionDesc').value      = desc;
}

// ── Fill + open: Delete Section confirm ───────────────────────
function openDeleteSection(chId, title) {
    document.getElementById('deleteSectionMsg').textContent =
        'You are about to delete "' + title + '" and all its lessons.';
    document.getElementById('deleteSectionLink').href =
        'course-curriculum.php?course=<?php echo $course_id;?>&delete_section=' + chId;
    openCurModal('confirmDeleteSectionModal');
}

// ── Fill + open: Edit Lesson ───────────────────────────────────
function fillEditLesson(vidId, chId, name) {
    document.getElementById('editLessonVideoId').value   = vidId;
    document.getElementById('editLessonChapterId').value = chId;
    document.getElementById('editLessonName').value      = name;
    var fn = document.getElementById('newFileName');
    if (fn) fn.style.display = 'none';
}

// ── Fill + open: Delete Lesson confirm ────────────────────────
function openDeleteLesson(vidId, chId, name) {
    document.getElementById('deleteLessonMsg').textContent =
        'You are about to permanently delete "' + name + '".';
    document.getElementById('deleteLessonLink').href =
        'course-curriculum.php?course=<?php echo $course_id;?>&delete_lesson=' + vidId + '&ch=' + chId;
    openCurModal('confirmDeleteLessonModal');
}

// ── File name preview ──────────────────────────────────────────
function showNewFile(input) {
    if (input.files && input.files[0]) {
        var el = document.getElementById('newFileName');
        el.style.display = 'block';
        el.innerHTML = '<i class="fas fa-check-circle"></i> ' + input.files[0].name;
    }
}

// ── Close on backdrop click ────────────────────────────────────
document.querySelectorAll('.cur-modal-overlay').forEach(function(overlay) {
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// ── Close on Escape key ────────────────────────────────────────
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeAllCurModals();
});
</script>
<?php include("includes/footer.php"); ?>
</body>
</html>
