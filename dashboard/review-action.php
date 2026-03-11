<?php
    require '../includes/scripts/connection.php';  
    session_start();
    
    // Auth Check
    if(isset($_SESSION['educat_logedin_user_id'])){
        $admin_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT role FROM user_master WHERE user_id = $admin_id";
        $result = mysqli_query($conn, $query);
        $role = mysqli_fetch_assoc($result)['role'];
        if($role != 1){ header("Location: ../404.php"); exit(); }
    }else{ header("Location: ../sign-in.php"); exit(); }

    if(isset($_GET['id']) && isset($_GET['action'])){
        $review_id = (int)$_GET['id'];
        $action = $_GET['action'];

        if($action == 'delete'){
            mysqli_query($conn, "DELETE FROM reviews WHERE review_id = $review_id");
            header("Location: reviews.php?msg=review_deleted");
        }
    }else{
        header("Location: reviews.php");
    }
?>
