<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

$id=$_GET['id'] ?? 0; $action=$_GET['action'] ?? '';
if($id && $action) {
    switch($action) {
        case 'approve':
            mysqli_query($conn,"UPDATE course_master SET course_approved_by_admin=1 WHERE course_id=$id");
            $_SESSION['admin_success']="Course approved successfully."; break;
        case 'unapprove':
            mysqli_query($conn,"UPDATE course_master SET course_approved_by_admin=0 WHERE course_id=$id");
            $_SESSION['admin_success']="Course unapproved."; break;
        case 'delete':
            mysqli_query($conn,"DELETE FROM videos_master WHERE video_of_chapter IN (SELECT course_chapter_id FROM course_chapter_list WHERE course_id=$id)");
            mysqli_query($conn,"DELETE FROM course_chapter_list WHERE course_id=$id");
            mysqli_query($conn,"DELETE FROM purchased_course_master WHERE course_id=$id");
            mysqli_query($conn,"DELETE FROM course_master WHERE course_id=$id");
            $_SESSION['admin_success']="Course deleted."; break;
    }
}
header("Location: course-list.php"); exit();
?>
