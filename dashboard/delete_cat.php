<?php
require '../includes/scripts/connection.php';  
session_start();
if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
    $user_id = $_SESSION['educat_logedin_user_id'];
    $query = "SELECT * FROM user_master WHERE user_id = $user_id";
    $result = mysqli_query($conn, $query);
    $userdata = mysqli_fetch_assoc($result);
    $user_role = $userdata["role"];
    if($user_role != 1 && $user_role != 2){
        header("Location: ../404.php");
    }
}else{
    header("Location: ../sign-in.php");
}
$id = $_GET['id'];

$sql = "DELETE FROM `course_category_master` WHERE `course_category_id` = $id";
$result = mysqli_query($conn,$sql);
if($result){
    $_SESSION["educat_success_message"] = "Category deleted successfully.";
    header("location: category-list.php");
}else{
    $_SESSION["educat_error_message"] = "Error: Category is not deleted.";
    header("location: category-list.php");
}

?>