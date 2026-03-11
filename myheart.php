
<?php
    require 'includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
    }

    // check user getin via get method or direct
    if(isset($_GET["id"])){

        $id = $_GET["id"];
        $currentDate = date('d-m-Y');
        
        $sql1 = "SELECT course_id, user_id FROM user_fav_master WHERE course_id = $id AND user_id = $user_id";
        $result1 = mysqli_query($conn, $sql1);
        if(mysqli_num_rows($result1) > 0){
            $sqlToRemoveFromFav = "DELETE FROM user_fav_master WHERE course_id = $id AND user_id = $user_id";
            $resToRemoveFromFav = mysqli_query($conn, $sqlToRemoveFromFav);
            if($resToRemoveFromFav){
                $queryTwo3 = "UPDATE `purchased_course_master` SET `course_favorite`= 0 WHERE `course_id` = $id;";
                $resTwo3 = mysqli_query($conn, $queryTwo3);
                if($resTwo3){
                    $_SESSION["educat_success_message"] = "Course removed from favourites.";
                    header("Location: course-overview.php?id=" . $id);
                }else{
                    $_SESSION["educat_error_message"] = "Error while removing course from favourites.";
                    header("Location: course-overview.php?id=" . $id);
                }
            }
        }else{
            $sql = "SELECT course_price FROM `course_master` WHERE `course_id` = $id";
            $result = mysqli_query($conn,$sql);
            $rowdata = mysqli_fetch_assoc($result);
            $coursePrice = $rowdata['course_price'];
    
            $sqlToInsertInCart = "INSERT INTO `user_fav_master`(`course_id`, `user_id`, `date`) VALUES ('$id','$user_id','$currentDate')";
            $result = mysqli_query($conn, $sqlToInsertInCart);
            if($result){
                $queryTwo = "UPDATE `purchased_course_master` SET `course_favorite`= 1 WHERE `course_id` = $id;";
                $resTwo = mysqli_query($conn, $queryTwo);
                if($resTwo){
                    $_SESSION["educat_success_message"] = "Course added to favourites.";
                    header("Location: course-overview.php?id=" . $id);
                }else{
                    $_SESSION["educat_error_message"] = "Error while adding course into favourites.";
                    header("Location: course-overview.php?id=" . $id);
                }
            }else{
                $_SESSION["educat_error_message"] = "Error while adding course into favourites.";
                header("Location: course-overview.php?id=" . $id);
            }
        }


    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
</head>
<body>
    <?php
        include("includes/components/header.php");
    ?>
    <div style=" margin:auto; width: 97%; margin-bottom: 40px;">
        <h1 style="color: black;">My Cart</h1>
    </div>
    <div class="scourseconadd" style="margin-bottom: 40px;">
        <div class="searchedvideoadd slefts">
            <h3 style="padding-bottom: 10px; border-bottom: 2px solid gray; margin-bottom: 20px;"> Course in cart</h3>
            <?php
                $sno = 1;
                $total_price = 0;

                $sql0 = "SELECT * FROM `user_fav_master` WHERE user_id = $user_id";
                if($result0 = mysqli_query($conn,$sql0)){
                    if(mysqli_num_rows($result0) > 0){
                        while($data0 = mysqli_fetch_assoc($result0)){
                            $cid = $data0['course_id'];
                            $sql2 = "SELECT * FROM `course_master` WHERE course_id = $cid";
                            if ($result2 = mysqli_query($conn,$sql2)) {
                                if (mysqli_num_rows($result2) > 0) {
                                    $rowdata = mysqli_fetch_assoc($result2);
                                    $price = $rowdata["course_price"];
                                    $total_price+= $price;
                                    // course instructor
                                    $instructor = $rowdata['course_instructor'];
                                    $sql1 = "SELECT * FROM `user_master` WHERE `user_id` = $instructor";
                                    if ($result1 = mysqli_query($conn,$sql1)) {
                                        if(mysqli_num_rows($result1) > 0){
                                            $rowdata1 = mysqli_fetch_assoc($result1);
                                            ?>
                                            <div class="addedcourse" style="display: flex; width: 100%; justify-content: space-between;">
                                                <div class="searchladd searchmadd">
                                                    <img src="<?php echo $rowdata["course_image"];?>" alt="">
                                                </div>
                                                <div class="searchcadd searchmadd">
                                                    <h2 style="margin: 0;"><?php echo $rowdata['course_name'];?>:</h2>
                                                    <p style="font-size: 1.1rem;">By <?php echo $rowdata1['user_name'];?></p>
                                                    <h3 style="font-size: 1.6rem;"><?php echo $rowdata['course_rating'];?> <span style="color: rgb(255, 187, 0); font-size: 1.7rem;">&starf;&starf;&starf;&starf;&starf; &nbsp;</span><span style="font-size: 1rem;">(75 Ratings)</span></h3>
                                                    <ul style="display: flex; gap: 30px; margin: 0; padding: 0; overflow: hidden;"><li><?php echo $rowdata['course_hours'];?> total hours</li><li><?php echo $rowdata['course_chapters'];?> Lectures</li><li>Beginner</li><p style="font-size: 1.5rem; margin:0; margin-top:-8px; font-weight:bold; color:red; text-align:right; width:36%">&#8377; <?php echo $rowdata['course_price'];?></p></ul>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            ?>
        </div>
        <div class="searchedvideoadd srights">
            <div class="searchradd searchmadd" style="overflow-y:scroll;">
                <table>
                    <tr>
                        <th>Sub-total</th>
                        <th>Price</th>
                    </tr>
                    <?php
                        $sno = 1;
                        $total_price = 0;

                        $sql0 = "SELECT * FROM `user_fav_master` WHERE user_id = $user_id";
                        if($result0 = mysqli_query($conn,$sql0)){
                            if(mysqli_num_rows($result0) > 0){
                                while($data0 = mysqli_fetch_assoc($result0)){
                                    $cid = $data0['course_id'];
                                    $sql2 = "SELECT * FROM `course_master` WHERE course_id = $cid";
                                    if ($result2 = mysqli_query($conn,$sql2)) {
                                        if (mysqli_num_rows($result2) > 0) {
                                            $rowdata = mysqli_fetch_assoc($result2);
                                            $price = $rowdata["course_price"];
                                            $total_price+= $price;
                                            // course instructor
                                            $instructor = $rowdata['course_instructor'];
                                            $sql1 = "SELECT * FROM `user_master` WHERE `user_id` = $instructor";
                                            if ($result1 = mysqli_query($conn,$sql1)) {
                                                if(mysqli_num_rows($result1) > 0){
                                                    $rowdata1 = mysqli_fetch_assoc($result1);
                                                    ?>
                                                    <tr>
                                                        <td></td>
                                                        <td class="prisecheckout"><p>&#8377; <?php echo $price;?></p></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                    ?>
                    <!-- Total -->
                    <tr>
                        <th style="border-top:1px dashed gray;">Total</th>
                        <td class="prisecheckout" style="color:red; font-weight:bold; border-top:1px dashed gray;"><p>&#8377; <?php echo $total_price;?></p></td>
                    </tr>
                </table>
                <div class="checkout"><button type="submit" style="cursor: pointer;">Checkout</button></div>
                <!-- <p style="display: flex; flex-direction: column;">
                    <b style="font-size: 1.2rem;">Pormotions</b>
                </p>
                <div class="search"><input type="text" placeholder="Enter Coupon..."><button>Apply</button></div> -->
            </div>
        </div>
    </div>

    <?php 
        include("includes/components/footer.php");
    ?>
</body>
</html>


