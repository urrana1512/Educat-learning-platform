<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id']) || trim($_SESSION['educat_logedin_user_id'])===''){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

$id     = isset($_GET['id'])     ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action']   : '';

if($id > 0 && $action) {
    switch($action) {
        case 'block':
            mysqli_query($conn,"UPDATE user_master SET isActive=1 WHERE user_id=$id");
            $_SESSION['admin_success'] = "User has been blocked.";
            break;
        case 'unblock':
            mysqli_query($conn,"UPDATE user_master SET isActive=0 WHERE user_id=$id");
            $_SESSION['admin_success'] = "User has been unblocked.";
            break;
        case 'delete':
            // Delete user's enrollments, then the user
            mysqli_query($conn,"DELETE FROM purchased_course_master WHERE user_id=$id");
            mysqli_query($conn,"DELETE FROM user_master WHERE user_id=$id AND role != 1");
            $_SESSION['admin_success'] = "User deleted successfully.";
            break;
        default:
            $_SESSION['admin_error'] = "Invalid action.";
    }
}

$ref = isset($_GET['ref']) ? $_GET['ref'] : 'users';
if($ref == 'students') header("Location: students-list.php");
elseif($ref == 'instructors') header("Location: instructors.php");
else header("Location: users.php");
exit();
?>
