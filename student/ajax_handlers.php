<?php
session_start();
require_once '../includes/scripts/connection.php';

if (!isset($_SESSION['educat_logedin_user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['educat_logedin_user_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action == 'save_note') {
    $lesson_id = $_POST['lesson_id'];
    $note_content = mysqli_real_escape_string($conn, $_POST['note_content']);
    
    // Check if note exists
    $check = mysqli_query($conn, "SELECT id FROM student_notes WHERE user_id = $user_id AND lesson_id = $lesson_id");
    if (mysqli_num_rows($check) > 0) {
        $query = "UPDATE student_notes SET note_content = '$note_content' WHERE user_id = $user_id AND lesson_id = $lesson_id";
    } else {
        $query = "INSERT INTO student_notes (user_id, lesson_id, note_content) VALUES ($user_id, $lesson_id, '$note_content')";
    }
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($conn)]);
    }
}

if ($action == 'mark_complete') {
    $lesson_id = $_POST['lesson_id'];
    $course_id = $_POST['course_id'];
    
    // Mark lesson as complete
    $check = mysqli_query($conn, "SELECT id FROM lesson_progress WHERE user_id = $user_id AND lesson_id = $lesson_id");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "INSERT INTO lesson_progress (user_id, course_id, lesson_id, is_completed, completed_at) VALUES ($user_id, $course_id, $lesson_id, 1, NOW())");
    }
    
    // Calculate new course progress
    // Total lessons in course
    $total_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM videos_master v JOIN course_chapter_list ch ON v.video_of_chapter = ch.course_chapter_id WHERE ch.course_id = $course_id");
    $total_lessons = mysqli_fetch_assoc($total_res)['total'];
    
    // Completed lessons in course
    $comp_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM lesson_progress WHERE user_id = $user_id AND course_id = $course_id AND is_completed = 1");
    $completed_lessons = mysqli_fetch_assoc($comp_res)['total'];
    
    $progress = ($total_lessons > 0) ? round(($completed_lessons / $total_lessons) * 100) : 0;
    $is_course_completed = ($progress == 100) ? 1 : 0;
    
    mysqli_query($conn, "UPDATE purchased_course_master SET course_progress = $progress, course_completed = $is_course_completed WHERE user_id = $user_id AND course_id = $course_id");
    
    echo json_encode(['status' => 'success', 'progress' => $progress]);
}
?>
