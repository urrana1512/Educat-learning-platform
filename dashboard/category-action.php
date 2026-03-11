<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }

// Remove standalone category action file — all handled in category-list.php
header("Location: category-list.php"); exit();
?>
