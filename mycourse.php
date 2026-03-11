<?php
    require 'includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
    }else{
        header("Location: ../sign-in.php");
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <title>All Videos</title>
</head>
<style>
    .container {
        display: flex;
        justify-content: center;
    }

    input[type="range"][value="100"]::-webkit-slider-thumb {
        transform: translateX(-100%);
    }

    input[type="range"] {
        -webkit-appearance: none;
        position: relative;
        overflow: hidden;
        height: 39px;
        width: 100%;
        /* border: 1px solid black; */
        border-radius: 15px;
        cursor: pointer;
        /* iOS */
        margin-right: 10px;
        z-index: 1;
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
                $query = "SELECT * FROM purchased_course_master WHERE user_id = $user_id";
                $res = mysqli_query($conn, $query);
                if($res){
                    if (mysqli_num_rows($res) == 0) {
                        echo "<b>No course purchased.</b>";
                    } else {
                        while($row = mysqli_fetch_assoc($res)){
                            $courseID = $row["course_id"];
                            $query2 = "SELECT * FROM course_master WHERE course_id = $courseID";
                            $res2 = mysqli_query($conn, $query2);
                            if($res2){
                                while($row2 = mysqli_fetch_assoc($res2)){?>
                                    <a href="play.php?id=<?php echo $courseID; ?>" class="vcon" style="height: 50vh;">
                                        <div class="vtop" style="height: 59%;">
                                            <img src="<?php echo $row2["course_image"];?>" alt="">
                                        </div>
                                        <div class="vbottom">
                                            <div class="subtop">
                                                <?php echo $row2["course_name"];?>
                                            </div>
                                            <div class="subbot" style="display:flex; flex-direction:column;">
                                                <p>By <b>Samarth Jayswal</b></p>
                                                <div class="range-container" style="margin-bottom: 20px;">
                                            <input type="range" value="90" id="rangeInput" min="0" max="100" disabled>
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