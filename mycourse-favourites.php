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
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/videop.css">
    <link rel="stylesheet" href="./assets/css/style.css">
    <title>All Videos</title>
</head>
<style>
    .container {
        display: flex;
        justify-content: center;
    }

    .wi {
        width: -webkit-fill-available;
        padding-right: 15px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .wi .heart{
        color: rgb(202, 3, 3);
        border-color: rgb(202, 3, 3);
        height: 35px;
        width: 35px;
    }

    .ri{
        width:100% !important;
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
                $sql0 = "SELECT * FROM `user_fav_master` WHERE user_id = $user_id";
                if($result0 = mysqli_query($conn,$sql0)){
                    if(mysqli_num_rows($result0) > 0){
                        while($data0 = mysqli_fetch_assoc($result0)){
                            $cid = $data0['course_id'];
                            $sql2 = "SELECT * FROM `course_master` WHERE course_id = $cid";
                            if ($result2 = mysqli_query($conn,$sql2)) {
                                if (mysqli_num_rows($result2) > 0) {
                                    $rowdata = mysqli_fetch_assoc($result2);
                                    // course instructor
                                    $instructor = $rowdata['course_instructor'];
                                    $sql1 = "SELECT * FROM `user_master` WHERE `user_id` = $instructor";
                                    if ($result1 = mysqli_query($conn,$sql1)) {
                                        if(mysqli_num_rows($result1) > 0){
                                            $rowdata1 = mysqli_fetch_assoc($result1);
                                            ?>
            
            <a href="play.php" class="vcon">
                <div class="vtop">
                    <img src="<?php echo $rowdata['course_image']; ?>" alt="">
                </div>
                <div class="vbottom">
                    <div class="subtop">
                        <?php echo $rowdata['course_description'];?>
                    </div>
                    <div class="subbot">
                        <div class="le">
                            <img src="<?php echo $rowdata1['user_profile_photo'];?>" alt="" height="40px" width="40px">
                        </div>
                        <div class="ri">
                            <b><?php echo $rowdata1['user_name'];?></b> <br> Channel
                        </div>
                        <div class="wi">
                            <div class="heart">
                                <p>&hearts;</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            <?php
                        }
                    }
                }
            }
        }
    }else{
        ?>
        <h3>No courses are in favourites.</h3>
        <?php
    }
}
            ?>
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