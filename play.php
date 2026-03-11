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
     header("Location: sign-in.php");
}
    if(!isset($_GET["id"])){
     header("Location: 404.php");
    }else{

    }

    if (isset($_GET["videoId"])) {
        $videoId = $_GET["videoId"];
        $sqlForVIDEOpath = "SELECT video_path, video_name FROM videos_master WHERE video_id = $videoId";
        $resultForVIDEOpath = $conn->query($sqlForVIDEOpath);
        $rowForVIDEOpath = $resultForVIDEOpath->fetch_assoc();
        $videoPATH = $rowForVIDEOpath["video_path"];
        $videoTITLE = $rowForVIDEOpath["video_name"];
    }else{
        $courseIDFORDEMOVIDEO = $_GET["id"];
        $sqlForVIDEOpath = "SELECT course_demo_lecture FROM course_master WHERE course_id = $courseIDFORDEMOVIDEO";
        $resultForVIDEOpath = $conn->query($sqlForVIDEOpath);
        $rowForVIDEOpath = $resultForVIDEOpath->fetch_assoc();
        $videoPATH = $rowForVIDEOpath["course_demo_lecture"];
        $videoTITLE = "Demo Lecture";
    }

    $id = $_GET['id'];
    $sql = "SELECT * FROM course_master WHERE course_id = $id";
    $result = mysqli_query($conn,$sql);
    $rowdata= mysqli_fetch_assoc($result);
    
    
    $instructor = $rowdata['course_instructor'];
    $sql1 = "SELECT * FROM `user_master` WHERE `user_id` = $instructor";
    $result1 = mysqli_query($conn,$sql1);
    $rowdata1 = mysqli_fetch_assoc($result1);

    // fetch number of how many student buy this course
    $sql3 = "SELECT COUNT(*) as row_count FROM purchased_course_master WHERE `course_id` = $id";
    $result3 = mysqli_query($conn,$sql3);
    $rowdata2 = mysqli_fetch_assoc($result3);

    // language id
    $langauge_id = $rowdata['course_language'];
    $sqlForLanguage = "SELECT * FROM `languages_master` WHERE `language_id` = $langauge_id";
    $resultForLanguage = mysqli_query($conn,$sqlForLanguage);
    $rowdataforLanguage = mysqli_fetch_assoc($resultForLanguage);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduCat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">         
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="./assets/css/videop.css">
</head>
<style>
    /* Style the checkboxes and arrows */
    input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        outline: none;
        border: 2px solid #666;
        border-radius: 50%;
        width: 15px;
        height: 15px;
        cursor: pointer;
    }

    input[type="checkbox"]:checked {
        background-color: #666;
    }

    .heart {
        cursor: pointer;
    }

    .heart.clicked {
        color: red;
    }

    .sidenav {
        height: 98%;
        width: 95%;
        z-index: 1;
    }

    /* Style the sidenav links and the dropdown button */
    .sidenav a,
    .dropdown-btn {
        padding: 16px 16px;
        text-decoration: none;
        font-size: 20px;
        color: rgb(94, 94, 94);
        border-radius: 9px;
        display: flex;
        border: none;
        background: rgba(206, 206, 206, 0.212);
        transition: all 0.3s ease;
        margin-bottom: 5px;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    /* On mouse-over */
    .sidenav a:hover,
    .dropdown-btn:hover {
        color: #161616;
        background: rgba(206, 206, 206, 0.836);
    }

    /* Add an active class to the active dropdown button */
    .active {
        background: rgba(206, 206, 206, 0.74);
        color: rgb(0, 0, 0);
    }

    /* Dropdown container (hidden by default). Optional: add a lighter background color and some left padding to change the design of the dropdown content */
    .dropdown-container {
        display: none;
        width: 90%;
    }

    .dropdown-container a {
        background-color: rgba(128, 128, 128, 0.712);
        color: rgb(255, 255, 255);
        transition: all 0.5s ease;
        padding: 11px 16px;
    }

    .dropdown-container a:hover {
        background-color: rgb(128, 128, 128);
        color: white;
    }

    /* Some media queries for responsiveness */
    @media screen and (max-height: 450px) {
        .sidenav {
            padding-top: 15px;
        }

        .sidenav a {
            font-size: 18px;
        }
    }

    .submleft {
        width: 10%;
        float: left;
    }

    .submright {
        width: 89%;
        float: right;
    }

    .playvideo{
        position: relative;
    }

    .watermark {
    position: absolute;
    left: 3%; top: 3%;
    color: white;
    animation: 15s moving-watermark infinite;
  }
</style>

<body oncontextmenu="return false;">
<?php 
    include("includes/components/header-two.php");
    ?>
    <div class="container">
        <div class="video">
            <div class="playvideo">
                <video title="<?php echo $videoTITLE;?>" controls controlsList="nodownload" oncontextmenu="return false;">
                    <source src="<?php echo $videoPATH;?>" type='video/webm;codecs="vp8, opus"' />
                </video>
                <i class="watermark"><?php echo $videoTITLE;?></i>
            </div>
            <div class="desc">
                <div class="title">
                    <?php 
                        $courseIDforFetch = $_GET["id"];
                        $sql = "SELECT * FROM course_master WHERE course_id = $courseIDforFetch";
                        $res = mysqli_query($conn, $sql);
                        if($res){
                            $courseData = mysqli_fetch_assoc($res);
                        }

                        $courseId = $_GET["id"];
                        $sql = "SELECT * FROM course_chapter_list WHERE course_id = $courseId ORDER BY course_chapter_id ASC";
                        $result = mysqli_query($conn,$sql);
                        $rowdatafordesc = mysqli_fetch_Assoc($result);
                    ?>
                    <h2>About this Course</h2>
                    <p><?php echo $courseData["course_description"];?></p>
                </div>
                <hr>
                <h3>About this chapter</h3>
                <p><?php echo $rowdatafordesc["course_chapter_description"];?></p>
                <hr>
                <div class="detail">
                    <div class="f">
                        <p>By the number</p>
                    </div>
                    <div class="sc">
                        <p><b>Student(s):</b>&nbsp<?php echo $rowdata2['row_count'];?></p>
                        <p><b>Total Chapters:</b>&nbsp<?php echo $rowdata['course_chapters'];?></p>
                        <p><b>Language:</b>&nbsp<?php echo $rowdataforLanguage['language_name'];?></p>
                    </div>
                    <div class="t">
                        <p><b>Lectures:</b>&nbsp<?php echo $rowdata['course_chapters'];?></p>
                        <p><b>Started:</b>&nbsp<?php echo $rowdata['course_lanuched_on'];?></p>
                        <p><b>Updated:</b>&nbsp<?php echo $rowdata['course_last_updated_on'];?></p>
                    </div>
                </div>
                <hr>
                <div class="detail">
                    <div class="f1">
                        <p>Certificates</p>
                    </div>
                    <div class="f2">
                        <!-- <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit. Corrupti, ab. Lorem, ipsum dolor
                            sit
                            amet consectetur adipisicing elit. Commodi ratione reprehenderit similique sit amet eos
                            recusandae, beatae nam asperiores corrupti repellat dolorem molestias quasi!</p> -->
                        <!-- <br> -->
                        <a href="certificate.php?id=<?php echo $_GET['id'];?>"><button>EduCat Certificate</button></a>
                    </div>
                </div>
                <hr>
                <div class="detail">
                    <div class="f1">
                        <p>Instructor</p>
                    </div>
                    <div class="f2 f-2">
                        <div class="person">
                            <div class="l">
                                <img src="<?php echo $rowdata1['user_profile_photo'];?>" alt="" height="60px" width="60px" class="img_ins">
                            </div>
                            <div class="r">
                                <h3><?php echo $rowdata1['user_name'];?></h3>
                                <h4>Instructor</h4>
                            </div>
                        </div>
                        <div class="cont">
                            <p><?php echo $rowdata1['user_email'];?></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="detail">
                    <div class="f1">
                        <p>Quiz</p>
                    </div>
                    <div class="f2 f-2">
                        <div class="cont">
                            <a href="studentquiz.php?id=<?php echo $courseId; ?>"><button>Attempt Quiz</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="anothervideos" style="margin-bottom: 10px;">
            <div class="sidenav">
                <?php
                    $courseId = $_GET["id"];
                    $sql = "SELECT * FROM course_chapter_list WHERE course_id = $courseId ORDER BY course_chapter_id ASC";
                    $result = $conn->query($sql);
                    // Display the retrieved data
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $chapterId =  $row["course_chapter_id"];
                            ?>
                    <div class="dropbox">
                    <button class="dropdown-btn"><?php echo $row["course_chapter_name"];?>
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-container">
                        <?php
                        $sql2 = "SELECT * FROM videos_master WHERE video_of_chapter = $chapterId ORDER BY video_id ASC";
                        $result2 = $conn->query($sql2);
                        // Display the retrieved data
                        if ($result2->num_rows > 0) {
                            while ($rowOfVideo = $result2->fetch_assoc()) {
                                $courseVideoId = $rowOfVideo["video_id"];                               
                                ?>
                        <a href="play.php?id=<?php echo $courseId;?>&videoId=<?php echo $courseVideoId;?>">
                            <div class="submleft" style="display:flex; align-items:center;"><input type="checkbox" name="" style="margin:0;" id="" disabled></div>
                            <div class="submright"><?php echo $rowOfVideo["video_name"];?></div>
                        </a>
                        <?php
                        }
                    } else {
                        echo "0 results";
                    }

                ?>
                    </div>
                </div>

                            <?php
                        }
                    } else {
                        echo "0 results";
                    }

                ?>
              
                <!-- <div>
                    <button class="dropdown-btn">Basics
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-container">
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" checked disabled></div>
                            <div class="submright">Video 1</div>
                        </a>
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" disabled></div>
                            <div class="submright">Video 2</div>
                        </a>
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" disabled></div>
                            <div class="submright">Video 3</div>
                        </a>
                    </div>
                </div>
                <div>
                    <button class="dropdown-btn">Beginner
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-container">
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" disabled></div>
                            <div class="submright">Video 1</div>
                        </a>
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" checked disabled></div>
                            <div class="submright">Video 2</div>
                        </a>
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" disabled></div>
                            <div class="submright">Video 3</div>
                        </a>
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" checked disabled></div>
                            <div class="submright">Video 4</div>
                        </a>
                    </div>
                </div>
                <div>
                    <button class="dropdown-btn">Pro
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-container">
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" checked disabled></div>
                            <div class="submright">Video 1</div>
                        </a>
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" disabled></div>
                            <div class="submright">Video 2</div>
                        </a>
                    </div>
                </div>
                <div>
                    <button class="dropdown-btn">ultra
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <div class="dropdown-container">
                        <a href="videoplayer.php">
                            <div class="submleft"><input type="checkbox" name="" id="" checked disabled></div>
                            <div class="submright">Video 1</div>
                        </a>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
    
    <?php 
    include("includes/components/footer.php");
    ?>

</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleHeartColor() {
        var heart = document.getElementById("clickable");
        heart.classList.toggle("clicked");
    }

    var dropdowns = document.getElementsByClassName("dropdown-btn");
    var i;

    for (i = 0; i < dropdowns.length; i++) {
        dropdowns[i].addEventListener("click", function () {
            // Close all open dropdowns except the clicked one
            var openDropdowns = document.querySelectorAll('.dropdown-btn.active');
            openDropdowns.forEach(function (openDropdown) {
                if (openDropdown !== this) {
                    openDropdown.classList.remove('active');
                    openDropdown.nextElementSibling.style.display = "none";
                }
            }.bind(this));

            // Toggle the clicked dropdown
            this.classList.toggle("active");
            var dropdownContent = this.nextElementSibling;
            dropdownContent.style.display = dropdownContent.style.display === "block" ? "none" : "block";
        });
    }
</script>


<script>
// Your video file data (replace with your own logic to fetch the video data)
const videoData = <?php echo addslashes($videoPATH);?>;
const videoType = "video/mp4";

// Create a Blob from the video data
const blob = new Blob([videoData], { type: videoType });

// Create a temporary URL for the Blob
const tempURL = URL.createObjectURL(blob);

// Get the video player element
const videoPlayer = document.getElementById('videoPlayer');

// Set the source of the video player to the temporary URL
videoPlayer.src = tempURL;

// Optional: revoke the URL when the video is no longer needed to free up resources
videoPlayer.addEventListener('ended', () => {
  URL.revokeObjectURL(tempURL);
});

videoPlayer.addEventListener('error', (event) => {
  console.error('Video error:', event.target.error);
});

</script>

</html>