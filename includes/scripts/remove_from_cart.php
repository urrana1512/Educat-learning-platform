<?php
    require 'connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
    } else {
        header("Location: ../../sign-in.php"); exit;
    }
    if(isset($_GET['id'])){
        $cart_id = intval($_GET['id']);
        mysqli_query($conn, "DELETE FROM user_cart_master WHERE cart_id = $cart_id AND user_id = $user_id");
    }
    header("Location: ../../mycart.php");
?>
