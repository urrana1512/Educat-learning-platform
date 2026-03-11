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

    
    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="./assets/css/videop.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>All Videos</title>
</head>
<style>
    .container {
        display: flex;
        justify-content: center;
    }
</style>

<body>
   
    <!-- Header START -->
    <?php
        include("./includes/components/header-two.php");
    ?>
    <!-- Header END -->

    <div class="container">
        <div class="videocon">
        <?php
                $query = "SELECT * FROM course_master";
                $res = mysqli_query($conn, $query);
                
                        // Output data of each row
                  while($row = $res->fetch_assoc()) {
                    if($row["course_completion_number"] == 1) {
                    
                     echo '<a href="play.php" class="vcon">
                          <div class="vtop">
                    <img src="'.$row["course_image"].'" alt="">
                </div>
                <div class="vbottom">
                    <div class="subtop">
                        '.$row["course_description"].'
                    </div>
                    <div class="subbot">
                        <div class="le">
                            <img src="./assets/img/Cource.png" alt="" height="40px" width="40px">
                        </div>
                        <div class="ri">
                            <b>'.$row["course_name"].'</b> <br> 
                        </div>
                    </div>
                </div>
            </a>';
           }
        else {
          echo "No courses completion";
        }
         } 
     
    
?>
            <!-- <a href="play.php" class="vcon">
                <div class="vtop">
                    <img src="./assets/img/j1.png" alt="">
                </div>
                <div class="vbottom">
                    <div class="subtop">
                        Lorem ipsum dolor sit amet consectetur, adipisi...
                    </div>
                    <div class="subbot">
                        <div class="le">
                            <img src="./assets/img/Cource.png" alt="" height="40px" width="40px">
                        </div>
                        <div class="ri">
                            <b>PYS</b> <br> Channel
                        </div>
                    </div>
                </div>
            </a> -->
            
        </div>
    </div>

    <!-- Footer START -->
    <?php
        include("./includes/components/footer.php");
    ?>
    <!-- Footer END -->
    <script>
    const input = document.querySelector('input[type="text"]');
    const videoContainers = document.querySelectorAll('.vcon');
    const notVideosMessage = document.createElement('div');
    notVideosMessage.textContent = 'Video Not Found';
    notVideosMessage.style.display = 'none';
    notVideosMessage.style.color = 'red';
    notVideosMessage.style.fontFamily = 'monospace';
    notVideosMessage.style.fontWeight = 'bold';
    notVideosMessage.style.fontSize = '2rem'
    document.querySelector('.videocon').appendChild(notVideosMessage);

    // Add an input event listener to the input field
    input.addEventListener('input', filterVideos);

    function filterVideos() {
        const searchTerm = input.value.toLowerCase();
        let atLeastOneVisible = false;

        videoContainers.forEach((container) => {
            const channelName = container.querySelector('.ri b').textContent.toLowerCase();
            const subtopText = container.querySelector('.subtop').textContent.toLowerCase();

            if (channelName.includes(searchTerm) || subtopText.includes(searchTerm)) {
                container.style.display = 'block';
                atLeastOneVisible = true;
            } else {
                container.style.display = 'none';
            }
        });

        if (!atLeastOneVisible) {
            notVideosMessage.style.display = 'block';
        } else {
            notVideosMessage.style.display = 'none';
        }
    }
    </script>
</body>

</html>