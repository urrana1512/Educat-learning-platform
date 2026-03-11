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
    if(!isset($_GET['search_term'])){
        header("Location: 404.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo "Search - " . $_GET['search_term'];?></title>
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
    <?php 
          $searchTerm = $_GET['search_term'];
          $sql = "SELECT * FROM course_master WHERE course_name LIKE '%$searchTerm%' OR SOUNDEX(course_name) = SOUNDEX('$searchTerm')";
          $result = mysqli_query($conn, $sql);
    ?> 
    <div class="scoursecon" style="margin-bottom:40px; min-height:41vh;">
        <?php 
        if(mysqli_num_rows($result) == 0){
            echo "<center><a>No course available.</a></center>";
          }
        while ($row = $result->fetch_assoc()): 
                $original_price = $row["course_price"];
                $discount_rate = $row["course_discount"];
            ?>
        <a href="course-overview.php?id=<?php echo $row["course_id"];?>" class="searchedvideo">
            <div class="searchl searchm">
               <?php echo '<img src="' . $row['course_image'] . '" alt="" loading="lazy" onerror="this.onerror=null;this.src=\'image/notfound.png\';">';?>
            </div>
            <div class="searchc searchm">
                <h2 style="margin: 0;"><?php echo $row['course_name']; ?></h2>
                <p class="content" id="content" style="font-size: 1.1rem;"><?php echo $row['course_description']; ?></p>
                <h3 style="font-size: 1.6rem;"><?php echo $row['course_rating']; ?> <span style="color: rgb(255, 187, 0); font-size: 1.7rem;">&starf;&starf;&starf;&starf;&starf;</span></h3>
                <p style="font-size: 1.1rem;"><?php echo $row['course_hours']; ?> Total hours  <?php echo $row['course_chapters']; ?> Total Chapters</p>
            </div>
            <div class="searchr searchm">
                <p style="display: flex; flex-direction: column; text-align: right;">
                    <b style="color:red;font-size: 1.7rem;">&#8377; <?php echo ($row["course_discount"] > 0)? $original_price - ($original_price * ($discount_rate / 100)):$row["course_price"];?></b>
                    <s style="color:grey;font-size: 1.2rem;">&#8377; <?php echo ($row["course_discount"] > 0)? $original_price:""; ?></s>
                </p>                
            </div>
        </a>
        <?php endwhile; ?>
    </div> 
    <?php 
    include("includes/components/footer.php");
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
  var paragraph = document.getElementById("content");
  var text = paragraph.textContent;
  var words = text.split(" ");
  
  if (words.length > 30) {
    var truncatedText = words.slice(0, 21).join(" ") + "...";
    paragraph.textContent = truncatedText;
  }
});

    </script>
</body>
</html> 