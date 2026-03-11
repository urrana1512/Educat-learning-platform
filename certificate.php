<?php
// Visit https://mpdf.github.io/  for more help
require_once __DIR__.'/includes/vendor/autoload.php';
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

    $id=$_GET['id'];
    $sql = "SELECT * FROM course_master WHERE course_id = $id";
    $result = mysqli_query($conn,$sql);
    $rowdata= mysqli_fetch_assoc($result);

    // language id
    $langauge_id = $rowdata['course_language'];
    $sqlForLanguage = "SELECT * FROM `languages_master` WHERE `language_id` = $langauge_id";
    $resultForLanguage = mysqli_query($conn,$sqlForLanguage);
    $rowdataforLanguage = mysqli_fetch_assoc($resultForLanguage);

    $instructor = $rowdata['course_instructor'];
    $sql1 = "SELECT * FROM `user_master` WHERE `user_id` = $instructor";
    $result1 = mysqli_query($conn,$sql1);
    $rowdata1 = mysqli_fetch_assoc($result1);

    $randomDecimal = rand(1000000, 9999999);
    $randomHex = dechex($randomDecimal);
// $html = "Hello User!";
$html = '
<div class="container">
        <div class="topcer">
            <div class="cerleft">
                <img src="assets/img/EduCat (3).png" alt="" height="300px" width="300px">
            </div>
            <div class="cerright">
                <p>Certificate no: ssdjsgfujdb-ahag287262926-'.$randomHex.'</p>
                <p>certificate url: https://helloworld.com</p>
                <p>Reference number: 000'.$id.'</p>
            </div>
        </div>
        <div class="midcer">
            <p style="font-size: 1.3rem; font-weight: bolder; color: rgb(53, 53, 53);">CERTIFICATE OF COMPLETION</p><br>
            <p style="font-size: 3rem;">सीखेँ <b>'.$rowdata['course_name'].'</b></h1>
            <p style="font-size: 3rem; color: black;">'.$rowdataforLanguage['language_name'].' </p>
            <p style="font-size: 1rem;">Instructors:&nbsp;&nbsp;<b>'.$rowdata1['user_name'].'</b></p>
        </div>
        <div class="botcer">
            <h1 style="font-size: 2rem; font-weight: bolder; color: black;">'.$userdata['user_name'].'</h1><br>
            <p>Date&nbsp;&nbsp;<b>May 29,2023</b></p>
            <p>Length&nbsp;&nbsp;<b>'.$rowdata['course_hours'].' total hours</b></p>
        </div>
    </div>';
$mpdf = new \Mpdf\Mpdf();

$stylesheet = file_get_contents('pdf.css');

$mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY);

// $mpdf = new \Mpdf\Mpdf(['orientation' => 'L']); //To make pdf in landscape mode.
// $mpdf->WriteHTML($html); // To use php variables
// $mpdf->WriteHTML('Insert yout html code here!!'); // To write html

// Define a page using all default values except "L" for Landscape orientation
$mpdf->SetHeader('Product Category', 0, 0, 'C');
$mpdf->Output();
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+Sans+SC&family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <title>EduCat</title>
</head>
<style>
    body{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .container{
        height: 98vh;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        margin-top: -30px;
        width: 90%;
    }
    .topcer{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .topcer p{
        text-align: end;
        font-weight: bold;
        color: rgb(36, 36, 36);
    }
</style>
<body>
    <div class="container">
        <div class="topcer">
            <div class="cerleft">
                <img src="assets/img/EduCat (3).png" alt="" height="300px" width="300px">
            </div>
            <div class="cerright">
                <p>Certificate no: ssdjsgfujdb-ahag287262926-00hdhfgf</p>
                <p>certificate url: https://helloworld.com</p>
                <p>Reference number: 0004</p>
            </div>
        </div>
        <div class="midcer">
            <p style="font-size: 1.3rem; font-weight: bolder; color: rgb(53, 53, 53);">CERTIFICATE OF COMPLETION</p><br>
            <p style="font-size: 3rem;">सीखेँ <b>Javascript Programming </b></h1>
            <p style="font-size: 3rem; color: black;">हिन्दी में  </p>
            <p style="font-size: 1rem;">Instructors:&nbsp;&nbsp;<b>Shubham saurav</b></p>
        </div>
        <div class="botcer">
            <h1 style="font-size: 2rem; font-weight: bolder; color: black;">Samarth Jayswal</h1><br>
            <p>Date&nbsp;&nbsp;<b>May 29,2023</b></p>
            <p>Length&nbsp;&nbsp;<b>5.5 total hours</b></p>
        </div>
    </div>
</body>
</html> -->