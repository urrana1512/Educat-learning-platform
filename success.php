<?php
    require 'includes/scripts/connection.php';  
    require 'includes/scripts/common.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
    }

    $id = $_GET['id'];
    $sql = "SELECT * FROM `course_master` WHERE `course_id` = $id";
    $result = mysqli_query($conn,$sql);
    $rowdata = mysqli_fetch_assoc($result);

    $instructor = $rowdata['course_instructor'];
    $sql1 = "SELECT * FROM `user_master` WHERE `user_id` = $instructor";
    $result1 = mysqli_query($conn,$sql1);
    $rowdata1 = mysqli_fetch_assoc($result1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
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
    <title>EduCat</title>
</head>
<style>
    /* The alert message box */
.alert {
  padding: 20px;
  background-color: #00bd65e6; /* Red */
  color: rgb(0, 0, 0);
  margin-bottom: 15px;
  border-radius: 15px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* The close button */
.closebtn {
  margin-left: 15px;
  color: rgb(0, 0, 0);
  font-weight: bold;
  float: right;
  font-size: 22px;
  line-height: 20px;
  cursor: pointer;
  transition: 0.3s;
}

/* When moving the mouse over the close button */
.closebtn:hover {
  color: black;
}

.alert {
  opacity: 1;
  transition: opacity 0.6s; /* 600ms to fade out */
}

.conofalert{
    width: 98%;
    margin: auto;
    margin-top: 20px;
}

.alertleft a{
    text-decoration: none;
    color: white;
    font-weight: bold;
    padding: 7px 23px;
    border-radius: 10px;
    font-size: 1.2rem;
    background-color: #00502b;  
    border: 3px solid #00502b;
    transition: 0.2s all ease;
}

.alertleft a:hover{
    background-color: rgba(255, 255, 255, 0.74);
    color: #00502b;
}

.vstartcourse{
    display: flex;
    background-color: #cdcdcd;
    padding: 10px;
    border-radius: 15px;
}

.vstartcourse .vstartl{
    width: 30%;
    height: 40vh;
    border-radius: 15px;
    overflow: hidden;
}

.vstartcourse .vstartr{
    width: 70%;
    height: 40vh;
    padding-left: 20px;
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
}

.vstartcourse .vstartl img{
    height: 100%;
    width: 100%;
}

.vstartr a{
    text-decoration: none;
    color: white;
    font-weight: bold;
    padding: 7px 22px;
    border-radius: 10px;
    font-size: 1.2rem;
    background-color: #003550;  
    border: 3px solid #003550;
    width: fit-content;
    transition: 0.5s all ease;
}

.vstartr a:hover{
    background-color: rgba(255, 255, 255, 0.466);
    color: #003550;
}

</style>
<body>
    <?php
    include("includes/components/header.php");
    ?>

    <div class="conofalert">
        <div class="alert">
            <div class="alertleft">
                <h3 style="margin: 0; font-weight: bold;">Great Choice, <?php echo $userdata["user_name"];?>!</h3>
                <h4 style="margin: 0; margin-bottom: 20px; font-weight: normal;">Thank you for your course purchase! We appreciate your commitment to learning with us. Enjoy your educational journey!</h4>
                <a href="https://wa.me/?text=Hey, i found very usefull course on Educat checkout this: <?php echo urlencode($domain . "/course-overview.php?id=" . $_GET["id"])?>">Share this course</a>
            </div>
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        </div>
        <h2>Jump right in</h2>
        <div class="vstartcourse">
            <div class="vstartl"><img src="<?php echo $rowdata['course_image'];?>" alt=""></div>
            <div class="vstartr">
                <h2 style="margin: 0;"><?php echo $rowdata['course_name'];?>: Hello Friends my name is <?php echo $rowdata1['user_name'];?></h2>
                <h4 style="margin: 0;"><?php echo $rowdata['course_description'];?></h4>
                <h3 style="margin: 0;">By <?php echo $rowdata1['user_name'];?></h3>
                <a href="play.php?id=<?php echo $rowdata['course_id'];?>">Start Course</a>
            </div>
        </div>
    </div>

    <br>

    <?php
    include("includes/components/footer.php");
    ?>

    <script>
        // Get all elements with class="closebtn"
        var close = document.getElementsByClassName("closebtn");
        var i;
        
        // Loop through all close buttons
        for (i = 0; i < close.length; i++) {
          // When someone clicks on a close button
          close[i].onclick = function(){
        
            // Get the parent of <span class="closebtn"> (<div class="alert">)
            var div = this.parentElement;
        
            // Set the opacity of div to 0 (transparent)
            div.style.opacity = "0";
        
            // Hide the div after 600ms (the same amount of milliseconds it takes to fade out)
            setTimeout(function(){ div.style.display = "none"; }, 600);
          }
        }
        </script>
</body>
</html>