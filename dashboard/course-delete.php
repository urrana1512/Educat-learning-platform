<?php
    require '../includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
        if($user_role != 1 && $user_role != 2){
            header("Location: ../404.php");
        }
    }else{
        header("Location: ../sign-in.php");
    }

    if(!isset($_GET['course']) || (!isset($_GET['course']) && !isset($_GET['chapter']))){
        header("Location: ../404.php");
    }
    
    $courseId = $_GET['course'];
    $sql011 = "SELECT course_id FROM course_master WHERE course_instructor = $user_id AND course_id = $courseId";
    $result001 = mysqli_query($conn, $sql011);
    if(mysqli_num_rows($result001) == 0){
        header("Location: ../404.php");
    }
    

    // To delete whole course.
    if (isset($_GET['course']) && !isset($_GET['chapter']) && !isset($_GET['video'])) {
        $courseChapterSql = "SELECT course_chapter_id FROM `course_chapter_list` WHERE `course_id`  = $courseId";
        $courseChapterResult = mysqli_query($conn,$courseChapterSql);
        
        if (mysqli_num_rows($courseChapterResult) > 0) {
            while($rowForChapter = mysqli_fetch_assoc($courseChapterResult)){
                $chapterId = $rowForChapter["course_chapter_id"];
                $sqlForFindVideos = "SELECT video_id, video_path FROM videos_master WHERE video_of_chapter = $chapterId";
                $resultForFindVideos = mysqli_query($conn, $sqlForFindVideos);
                $rowForFindVideos = mysqli_fetch_assoc($resultForFindVideos);
                if (mysqli_num_rows($resultForFindVideos) > 0) {
                    while ($rowForFindVideos = mysqli_fetch_assoc($resultForFindVideos)) {
                        // Delete each video file
                        if ($rowForFindVideos["video_path"] != "assets/img/notfound.png" && file_exists("../" . $rowForFindVideos["video_path"])) {
                            unlink("../" . $rowForFindVideos["video_path"]);
                        }
                    }
                    // if ($rowForFindVideos["video_path"] != "assets/img/notfound.png" && file_exists("../" . $rowForFindVideos["video_path"])) {
                    //     unlink("../" . $rowForFindVideos["video_path"]);
                    // }
                    $sqlToDeleteVideo = "DELETE FROM videos_master WHERE video_of_chapter = $chapterId";
                    $resultToDeleteVideo = mysqli_query($conn, $sqlToDeleteVideo);
                    if($resultToDeleteVideo){
                        $sqlToDeleteChapter = "DELETE FROM course_chapter_list WHERE course_chapter_id = $chapterId";
                        $resultToDeleteChapter = mysqli_query($conn, $sqlToDeleteChapter);
                        if($resultToDeleteChapter){
                            $sqlToSelectCourseDetails = "SELECT course_image, course_demo_lecture FROM course_master WHERE course_id = $courseId";
                            if($resultToSelectCourseDetails = mysqli_query($conn, $sqlToSelectCourseDetails)){
                                $rowToSelectCourseDetails = mysqli_fetch_assoc($resultToSelectCourseDetails);
                                if ($rowToSelectCourseDetails["course_image"] != "assets/img/notfound.png" && file_exists("../" . $rowToSelectCourseDetails["course_image"])) {
                                    if(unlink("../" . $rowToSelectCourseDetails["course_image"])){
                                        if (unlink("../" . $rowToSelectCourseDetails["course_demo_lecture"])) {
                                            $sqlToDeleteCourse = "DELETE FROM `course_master` WHERE course_id = $courseId";
                                            $resultToDeleteCourse = mysqli_query($conn, $sqlToDeleteCourse);
                                            if($resultToDeleteCourse){
                                                $_SESSION["educat_success_message"] = "Course deleted successfully.";
                                                header("Location: course-list.php");
                                            }else{
                                                $_SESSION["educat_error_message"] = "Course is not deleted, Error while deleting course.";
                                                header("Location: course-list.php");
                                            }
                                        }else{
                                            $_SESSION["educat_error_message"] = "Course is not deleted, Error while deleting featured video.";
                                            header("Location: course-list.php");
                                        }
                                    }else{
                                        $_SESSION["educat_error_message"] = "Course is not deleted, Error while deleting featured image.";
                                        header("Location: course-list.php");
                                    }
                                }
                            }
                        }else{
                            $_SESSION["educat_error_message"] = "Course is not deleted, Error while deleting chapters.";
                            header("Location: course-list.php");
                        }
                    }else{
                        $_SESSION["educat_error_message"] = "Course is not deleted, Error while deleting chapter's videos.";
                        header("Location: course-list.php");
                    }
                }else{
                    $sqlToDeleteChapter = "DELETE FROM course_chapter_list WHERE course_id = $courseId";
                    $resultToDeleteChapter = mysqli_query($conn, $sqlToDeleteChapter);
                }
                $courseChapterResult = mysqli_query($conn,$courseChapterSql);
            }
            // $sqlToFetchChapterVideoes = "SELECT chapter_total_videos FROM course_chapter_list WHERE course_chapter_id = ";
        }
        elseif (mysqli_num_rows($courseChapterResult) == 0) {
            $sqlToSelectCourseDetails = "SELECT course_image FROM course_master WHERE course_id = $courseId";
            if($resultToSelectCourseDetails = mysqli_query($conn, $sqlToSelectCourseDetails)){
                $rowToSelectCourseDetails = mysqli_fetch_assoc($resultToSelectCourseDetails);
                if ($rowToSelectCourseDetails["course_image"] != "assets/img/notfound.png" && file_exists("../" . $rowToSelectCourseDetails["course_image"])) {
                    if(unlink("../" . $rowToSelectCourseDetails["course_image"])){
                        $sqlToDeleteCourse = "DELETE FROM `course_master` WHERE course_id = $courseId";
                        $resultToDeleteCourse = mysqli_query($conn, $sqlToDeleteCourse);
                        if($resultToDeleteCourse){
                            $_SESSION["educat_success_message"] = "Course deleted successfully.";
                            header("Location: course-list.php");
                        }
                    }
                }
            }
    
        }
    }

    // To delete chapters.
    if(isset($_GET['course']) && isset($_GET['chapter']) && !isset($_GET['video'])){
        $courseID = $_GET['course'];
        $chapterID = $_GET['chapter'];
        $sql011 = "SELECT course_id FROM course_master WHERE course_instructor = $user_id AND course_id = $courseId";
        $result001 = mysqli_query($conn, $sql011);
        if(mysqli_num_rows($result001) == 0){
            header("Location: ../404.php");
        }else{
            $sqlCHAPTER = "SELECT * FROM course_chapter_list WHERE course_chapter_id = $chapterID AND course_id = $courseID";
            if($resultCHAPTER =  mysqli_query($conn, $sqlCHAPTER)){
                $sqlForFindVideos = "SELECT video_id, video_path FROM videos_master WHERE video_of_chapter = $chapterID";
                $resultForFindVideos = mysqli_query($conn, $sqlForFindVideos);
                $rowForFindVideos = mysqli_fetch_assoc($resultForFindVideos);
                if (mysqli_num_rows($resultForFindVideos) > 0) {
                    while ($rowForFindVideos = mysqli_fetch_assoc($resultForFindVideos)) {
                        // Delete each video file
                        if ($rowForFindVideos["video_path"] != "assets/img/notfound.png" && file_exists("../" . $rowForFindVideos["video_path"])) {
                            unlink("../" . $rowForFindVideos["video_path"]);
                        }
                    }
                    $resultForFindVideos2 = mysqli_query($conn, $sqlForFindVideos);
                    $rowForFindVideos2 = mysqli_fetch_assoc($resultForFindVideos2);
                    if (mysqli_num_rows($resultForFindVideos2) == 0) {
                        $sqlVIDEOS = "DELETE FROM videos_master WHERE video_of_chapter = $chapterID";
                        if($resultVIDEOS = mysqli_query($conn, $sqlVIDEOS)){
                            $sqlDELETEchapter = "DELETE FROM course_chapter_list WHERE course_chapter_id = $chapterID AND course_id = $courseID";
                            if(mysqli_query($conn, $sqlDELETEchapter)){
                                $updateCourseQuery = "UPDATE course_master SET course_chapters = course_chapters - 1 WHERE course_id = $courseID";
                                if(mysqli_query($conn, $updateCourseQuery)){
                                    $_SESSION["educat_success_message"] = "Chapter deleted successfully.";
                                    header("Location: course-chapter-list.php");
                                }
                            }
                        }
                    }
                }else{
                    // $sqlVIDEOS = "DELETE FROM videos_master WHERE video_of_chapter = $chapterID";
                    //     if($resultVIDEOS = mysqli_query($conn, $sqlVIDEOS)){
                            $sqlDELETEchapter = "DELETE FROM course_chapter_list WHERE course_chapter_id = $chapterID AND course_id = $courseID";
                            if(mysqli_query($conn, $sqlDELETEchapter)){
                                $updateCourseQuery = "UPDATE course_master SET course_chapters = course_chapters - 1 WHERE course_id = $courseID";
                                if(mysqli_query($conn, $updateCourseQuery)){
                                    $_SESSION["educat_success_message"] = "Chapter deleted successfully.";
                                    header("Location: course-chapter-list.php?course=" . $courseID);
                                }
                            }
                        // }
                }
            }
        }
    }


    // To delete videos.
    if(isset($_GET['course']) && isset($_GET['chapter']) && isset($_GET['video'])){
        $courseID = $_GET['course'];
        $chapterID = $_GET['chapter'];
        $videoID = $_GET['video'];
        $sqlTODELETEVIDEOS = "DELETE FROM videos_master WHERE video_id = $videoID";
        if(mysqli_query($conn, $sqlTODELETEVIDEOS)){
            $updateQuery = "UPDATE course_chapter_list SET chapter_total_videos - 1 WHERE course_chapter_id = $chapterID";
            if(mysqli_query($conn, $updateQuery)){
                $_SESSION["educat_success_message"] = "Video deleted successfully.";
                header("Location: course-chapter-list.php?course=" . $courseID . "&chapter=" . $chapterID);
            }
        }
        
    }
    
    

?>