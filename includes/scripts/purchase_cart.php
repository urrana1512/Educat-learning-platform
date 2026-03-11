<?php
    require 'connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
    } else {
        header("Location: ../../sign-in.php"); exit;
    }

    function generateUniqueTransactionID($db) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxAttempts = 10;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $transactionID = '';
            for ($i = 0; $i < 14; $i++) {
                $randomIndex = rand(0, strlen($characters) - 1);
                $transactionID .= $characters[$randomIndex];
            }
            $stmt = $db->prepare("SELECT COUNT(*) FROM purchased_course_master WHERE transaction_id = ?");
            $stmt->bind_param('s', $transactionID);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();
            if ($count == 0) return $transactionID;
        }
        return "TXN".time().rand(100,999);
    }

    $cart_sql = "SELECT cm.*, c.course_price, c.course_discount 
                 FROM user_cart_master cm 
                 JOIN course_master c ON cm.course_id = c.course_id 
                 WHERE cm.user_id = $user_id";
    $cart_res = $conn->query($cart_sql);

    if ($cart_res && $cart_res->num_rows > 0) {
        $purchaseDate = date("d-m-Y");
        $purchased_courses = [];
        while ($row = $cart_res->fetch_assoc()) {
            $course_id = $row['course_id'];
            $original_price = $row['course_price'];
            $discount_rate = $row['course_discount'];
            $payment_amount = ($discount_rate > 0) ? $original_price - ($original_price * ($discount_rate / 100)) : $original_price;

            $check = $conn->query("SELECT pcm_id FROM purchased_course_master WHERE course_id = $course_id AND user_id = $user_id");
            if ($check->num_rows == 0) {
                $transactionID = generateUniqueTransactionID($conn);
                $insert_sql = "INSERT INTO purchased_course_master (`course_id`, `user_id`, `payment_amount`, `original_price`, `discount_given`, `purchase_date`, `transaction_id`) 
                               VALUES ($course_id, $user_id, $payment_amount, $original_price, $discount_rate, '$purchaseDate', '$transactionID')";
                if ($conn->query($insert_sql)) {
                    $purchased_courses[] = $course_id;
                    
                    // Notify Instructor
                    $ins_id = $row['course_instructor'];
                    $course_name = $row['course_name'];
                    
                    // fetch student name
                    $stu_res = $conn->query("SELECT user_name FROM user_master WHERE user_id = $user_id");
                    $student_name = "A student";
                    if ($stu_res && $stu_res->num_rows > 0) {
                        $stu_row = $stu_res->fetch_assoc();
                        $student_name = $stu_row['user_name'];
                    }
                    
                    $notif_title = "New Student Enrolled!";
                    $notif_msg = "$student_name has just enrolled in your course '$course_name'.";
                    mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($ins_id, '$notif_title', '$notif_msg', 'enroll')");
                }
            }
        }
        $conn->query("DELETE FROM user_cart_master WHERE user_id = $user_id");
        
        $_SESSION["educat_success_message"] = "Congratulations! You have been successfully enrolled.";
        $first_id = !empty($purchased_courses) ? $purchased_courses[0] : 0;
        header("Location: ../../success.php?id=" . $first_id);
    } else {
        header("Location: ../../index.php");
    }
?>
