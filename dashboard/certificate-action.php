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
        $cert_id = (int)$_GET['id'];
        $action = $_GET['action'];

        if($action == 'revoke'){
            mysqli_query($conn, "DELETE FROM quiz_certificates WHERE certificate_id = $cert_id");
            header("Location: certificates.php?msg=certificate_revoked");
        }
    }else{
        header("Location: certificates.php");
    }
?>
