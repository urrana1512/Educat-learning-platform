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

$id = $_GET["id"];
$sql = "DELETE FROM `user_master` WHERE `user_id` = $id";
$result = mysqli_query($conn,$sql);
header("location: instructor-list.php");
?>