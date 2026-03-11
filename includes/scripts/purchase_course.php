<?php
    require 'connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
    }
    
    $course_Id = (isset($_GET["id"]))? $_GET["id"]: NULL;

    function generateUniqueTransactionID($db) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $maxAttempts = 10;
    
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $transactionID = '';
            for ($i = 0; $i < 14; $i++) {
                $randomIndex = rand(0, strlen($characters) - 1);
                $transactionID .= $characters[$randomIndex];
            }
    
            // Check if the generated ID already exists in the database
            $existingRecord = checkExistingRecord($db, $transactionID);
    
            if (!$existingRecord) {
                // If the ID is unique, return it
                return $transactionID;
            }
        }
    
        // If after maxAttempts still no unique ID is generated, you may handle it as needed.
        return null;
    }
    
    function checkExistingRecord($db, $transactionID) {
        // You need to implement a function to check if the generated ID already exists in the database
        // Replace the following line with your actual database query
        // For example, using mysqli:
        $stmt = $db->prepare("SELECT COUNT(*) FROM purchased_course_master WHERE transaction_id = ?");
        $stmt->bind_param('s', $transactionID); // 's' represents a string, adjust accordingly if your transaction ID is of a different type
        $stmt->execute();
    
        // Bind the result
        $stmt->bind_result($count);
    
        // Fetch the result
        $stmt->fetch();
    
        return $count > 0;
    }

    if ($course_Id != NULL) {
        $query = "SELECT * FROM course_master WHERE course_id = $course_Id";
        $res = $conn->query($query);
        if ($res->num_rows > 0) {
            $rowForData = $res->fetch_assoc();
            $original_price = $rowForData["course_price"];
            $discount_rate = $rowForData["course_discount"];
            $payment_amount = ($discount_rate > 0)? $original_price - ($original_price * ($discount_rate / 100)):$original_price;

            $query2 = "SELECT * FROM purchased_course_master WHERE course_id = $course_Id AND user_id = $user_id";
            $res2 = $conn->query($query2);
            if ($res2->num_rows == 0) {
                $purchaseDate = date("d-m-Y");
                $attemptCount = 0;
                $transactionID = null;
                do {
                    // Generate a new transaction ID
                    $transactionID = generateUniqueTransactionID($conn);

                    // Increment attempt count to avoid infinite loop
                    $attemptCount++;

                    // You can adjust the maximum attempt count as needed
                    if ($attemptCount > 100) {
                        $_SESSION["educat_error_message"] = "Error: Unable to generate a unique transaction ID after multiple attempts.";
                        header("Location: ../../course-overview.php?id=" . $course_Id);
                        break;
                    }
                } while ($transactionID === null);

                $query3 = "INSERT INTO purchased_course_master (`course_id`, `user_id`, `payment_amount`, `original_price`, `discount_given`, `purchase_date`, `transaction_id`) VALUES ($course_Id, $user_id, $payment_amount, $original_price, $discount_rate, '$purchaseDate', '$transactionID')";
                $res3 = $conn->query($query3);
                if ($res3) {
                    // Notify Instructor
                    $ins_id = $rowForData['course_instructor'];
                    $course_name = $rowForData['course_name'];
                    $student_name = $userdata['user_name'];
                    $notif_title = "New Student Enrolled!";
                    $notif_msg = "$student_name has just enrolled in your course '$course_name'.";
                    mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($ins_id, '$notif_title', '$notif_msg', 'enroll')");

                    $_SESSION["educat_success_message"] = "Congratulations, Course purchased successfully.";
                    $_SESSION["educat_data_session_message"] = $transactionID;
                    header("Location: ../../success.php?id=" . $course_Id);
                }
            }else{
                $_SESSION["educat_error_message"] = "Course already purchased, Once course is purchased you can not purchase it again.";
                header("Location: ../../course-overview.php?id=" . $course_Id);
            }
        }
    }
?>