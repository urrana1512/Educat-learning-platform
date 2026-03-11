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
    include("includes/components/header.php");
    ?>
    <!-- Header END -->
    
    <!-- Banner Slider START -->
    <div style="position: relative; width: 100%; overflow: hidden; background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%); color: #fff; padding: 50px 0; margin-bottom: 60px;">
        <div style="max-width: 1200px; margin: 0 auto; position: relative;">
            <?php
                $sql = "SELECT * FROM course_master ORDER BY course_id DESC LIMIT 5;";
                $res = $conn->query($sql);

                if ($res->num_rows > 0) {
                    while ($row = $res->fetch_assoc()) {
            ?>
            <div class="image" style="display: none; animation: fadeIn 0.5s ease-in-out;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: center; padding: 0 60px;">
                    <!-- Left Hero Content -->
                    <div>
                        <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(59, 130, 246, 0.2); padding: 6px 16px; border-radius: 99px; margin-bottom: 24px; border: 1px solid rgba(59, 130, 246, 0.3);">
                            <span style="display: block; width: 8px; height: 8px; background: #60A5FA; border-radius: 50%; box-shadow: 0 0 8px #60A5FA;"></span>
                            <span style="color: #93C5FD; font-size: 0.85rem; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">New Course Uploaded</span>
                        </div>
                        <h1 style="font-size: 3.5rem; font-weight: 800; line-height: 1.1; margin-bottom: 24px; color: #F8FAFC; font-family: 'Inter', sans-serif;">
                            <?php echo htmlspecialchars($row["course_name"]); ?>
                        </h1>
                        <p style="font-size: 1.1rem; color: #94A3B8; line-height: 1.6; margin-bottom: 35px; max-width: 90%;">
                            <?php echo htmlspecialchars(implode(' ', array_slice(str_word_count($row["course_description"], 1), 0, 15))); ?>...
                        </p>
                        <a href="course-overview.php?id=<?php echo $row["course_id"];?>" 
                           style="display: inline-block; background: #2563EB; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-weight: 600; font-size: 1.1rem; transition: all 0.2s ease; box-shadow: 0 4px 14px 0 rgba(37, 99, 235, 0.39);"
                           onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(37, 99, 235, 0.5)';"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px 0 rgba(37, 99, 235, 0.39)';">
                           Enroll Now
                        </a>
                    </div>
                    <!-- Right Hero Image -->
                    <div style="position: relative; display: flex; justify-content: center;">
                        <!-- Soft backdrop blob to hide jagged edges -->
                        <div style="position: absolute; top: 10%; left: 10%; right: 10%; bottom: 10%; background: radial-gradient(circle, rgba(59,130,246,0.3) 0%, rgba(30,58,138,0) 70%); border-radius: 50%; z-index: 0; filter: blur(40px);"></div>
                        <img src="assets/img/teacher.png" alt="Instructor" style="position: relative; z-index: 1; max-height: 450px; object-fit: contain; filter: drop-shadow(0 20px 30px rgba(0,0,0,0.3));">
                    </div>
                </div>
            </div>
            <?php
                    }
                }
            ?>
            <!-- Navigation Arrows -->
            <button onclick="nextimg(-1)" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(4px); color: #fff; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s ease; z-index: 10;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button onclick="nextimg(1)" style="position: absolute; right: 0; top: 50%; transform: translateY(-50%); background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(4px); color: #fff; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.2s ease; z-index: 10;" onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
            </button>

            <!-- Dots -->
            <div style="text-align: center; margin-top: 20px; display: flex; justify-content: center; gap: 8px;">
                <?php
                    if ($res->num_rows > 0) {
                        for ($i = 1; $i <= $res->num_rows; $i++) {
                            echo '<span class="dot" onclick="currentSlide('.$i.')" style="height: 10px; width: 10px; margin: 0 4px; background-color: rgba(255,255,255,0.3); border-radius: 50%; display: inline-block; cursor: pointer; transition: background-color 0.3s ease;"></span>';
                        }
                    }
                ?>
            </div>
            <style>
                .dot.active, .dot:hover { background-color: #3B82F6 !important; }
            </style>
        </div>
    </div>
    <!-- Banner Slider END -->

    <!-- Course Slider START -->
    <div style="max-width: 1200px; margin: 0 auto 80px; padding: 0 20px; font-family: 'Inter', sans-serif;">
        <h2 style="color: #0F172A; margin-bottom: 40px; text-align: center; font-size: 2.2rem; font-weight: 800; letter-spacing: -0.5px;">Students are viewing</h2>
        
        <div class="swiper mySwiper" style="padding-bottom: 40px;">
            <div class="swiper-wrapper">
                <?php
                        $sqlQuery = "SELECT cm.course_id, cm.course_name, cm.course_description, cm.course_image, cm.course_instructor, cm.course_completion_number, um.user_name
                        FROM course_master cm
                        JOIN user_master um ON cm.course_instructor LIMIT 7";

                        $result = mysqli_query($conn, $sqlQuery);
                        if ($result) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>  
                            <div class="swiper-slide" style="height: auto;">
                                <div style="background: #fff; border: 0px solid #E5E7EB; border-radius: 16px; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.05)';">
                                    <a href="course-overview.php?id=<?php echo $row["course_id"];?>" style="text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%;">
                                        
                                        <!-- Thumbnail -->
                                        <div style="width: 100%;background: #E2E8F0; padding: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                            <img src="<?php echo htmlspecialchars($row["course_image"]);?>" alt="" onerror="this.onerror=null;this.src='assets/img/notfound.png';" style="width: 100%; height: 100%; object-fit: fill; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                        </div>

                                        <!-- Content -->
                                        <div style="padding: 24px; display: flex; flex-direction: column; flex-grow: 1;">
                                            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 8px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($row["course_name"]);?></h3>
                                            
                                            <p style="font-size: 0.9rem; color: #64748B; margin: 0 0 16px 0; display: flex; align-items: center; gap: 6px; justify-content:center">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                Instructor: <span style="font-weight: 600; color: #475569;"><?php echo htmlspecialchars($row["user_name"]);?></span>
                                            </p>
                                            
                                            <!-- Short Description (max 3 lines) -->
                                            <p style="font-size: 0.95rem; color: #4B5563; line-height: 1.6; margin: 0 0 24px 0; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; flex-grow: 1;">
                                                <?php echo htmlspecialchars(strip_tags($row["course_description"])); ?>
                                            </p>
                                            
                                            <div style="margin-top: auto;">
                                                <span style="display: inline-flex; align-items: center; justify-content: center; width: 91%; padding: 12px; border-radius: 8px; background: #EFF6FF; color: #2563EB; font-weight: 600; font-size: 0.95rem; transition: background 0.2s ease;" onmouseover="this.style.background='#DBEAFE';" onmouseout="this.style.background='#EFF6FF';">
                                                    View Course Details
                                                </span>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                <?php
                        }
                    } else {
                        echo "Error fetching values from the database: " . mysqli_error($conn);
                    }
                ?>
            </div>
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <!-- Course Slider END -->

    <!-- Course Panel with sidebar START -->
    <?php if(isset($_SESSION['educat_logedin_user_id'])): ?>
    <div style="max-width: 1200px; margin: 0 auto 80px; padding: 0 20px; font-family: 'Inter', sans-serif;">
        <div style="display: grid; grid-template-columns: 280px 1fr; gap: 40px;">
            <!-- Categories Sidebar -->
            <div style="background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 16px; padding: 32px;">
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 24px;">Categories</h2>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                <?php
                    $sql = "SELECT * FROM course_category_master ORDER BY RAND() LIMIT 10";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <a href="index.php?category=<?php echo $row["course_category_id"]?>" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; color: #4B5563; font-weight: 500; text-decoration: none; transition: all 0.2s ease;" onmouseover="this.style.borderColor='#3B82F6'; this.style.color='#2563EB'; this.style.transform='translateX(4px)';" onmouseout="this.style.borderColor='#E5E7EB'; this.style.color='#4B5563'; this.style.transform='translateX(0)';">
                                <?php echo htmlspecialchars($row["course_category_name"]);?>
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            <?php
                        }
                    } else {
                        echo "<p style='color: #64748B;'>No categories found.</p>";
                    }
                ?>
                </div>
            </div>

            <!-- Course Grid -->
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: #0F172A; margin-bottom: 24px;">Recommended for You</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 24px;">
                <?php
                    if(isset($_GET["category"])){
                        $categoryID = intval($_GET["category"]);
                        $sql = "SELECT * FROM `course_master` WHERE course_category = $categoryID ORDER BY RAND() LIMIT 6";
                    } else {
                        $sql = "SELECT * FROM `course_master` ORDER BY RAND() LIMIT 6";
                    }
                    $result = mysqli_query($conn,$sql);
                    while($data = mysqli_fetch_assoc($result)){
                ?>
                    <a href="course-overview.php?id=<?php echo $data['course_id']?>" style="display: block; background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onmouseover="this.style.transform='translateY(-4px)'; this.style.boxShadow='0 10px 15px -3px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.05)';">
                        <div style="width: 100%; aspect-ratio: 16/9; background: #F1F5F9; padding: 20px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?php echo htmlspecialchars($data['course_image']);?>" alt="" onerror="this.onerror=null;this.src='assets/img/notfound.png';" style="max-height: 100%; max-width: 100%; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        </div>
                        <div style="padding: 16px;">
                            <h3 style="font-size: 1.1rem; font-weight: 700; color: #111827; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($data['course_name']);?></h3>
                        </div>
                    </a>
                <?php
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Logged out: Broad Selection -->
    <div style="background: #F8FAFC; padding: 80px 0 100px; border-top: 1px solid #E5E7EB; border-bottom: 1px solid #E5E7EB; margin-bottom: 80px; font-family: 'Inter', sans-serif;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="margin-bottom: 48px;">
                <h2 style="font-size: 2.2rem; font-weight: 800; color: #0F172A; margin: 0 0 16px 0; letter-spacing: -0.5px;">A broad selection of courses</h2>
                <p style="font-size: 1.1rem; color: #64748B; margin: 0; max-width: 600px;">Choose from over 10,000 online video courses with new additions published every month.</p>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px;">
                <?php 
                    $sql2 = "SELECT c.*, u.user_name FROM `course_master` c JOIN `user_master` u ON c.course_instructor = u.user_id ORDER BY RAND() LIMIT 8;";
                    if($result = mysqli_query($conn,$sql2)){
                        if(mysqli_num_rows($result) > 0){
                            while($rowdata = mysqli_fetch_assoc($result)){
                ?>
                                <a href="course-overview.php?id=<?php echo $rowdata['course_id']?>" style="display: flex; flex-direction: column; background: #fff; border: 1px solid #E5E7EB; border-radius: 16px; overflow: hidden; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);" onmouseover="this.style.transform='translateY(-6px)'; this.style.boxShadow='0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px -1px rgba(0,0,0,0.05)';">
                                    <div style="width: auto; aspect-ratio: 16/9; background: #F1F5F9; padding: 0px; display: flex; align-items: center; justify-content: center;">
                                        <img src="<?php echo htmlspecialchars($rowdata['course_image']); ?>" alt="" onerror="this.onerror=null;this.src='assets/img/notfound.png';" style="max-width: 100%; max-height: 100%; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); transition: transform 0.5s ease;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                                    </div>
                                    <div style="padding: 24px; flex-grow: 1; display: flex; flex-direction: column;">
                                        <h3 style="font-size: 1.15rem; font-weight: 700; color: #111827; margin: 0 0 12px 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars($rowdata['course_name']);?></h3>
                                        <p style="font-size: 0.9rem; color: #64748B; margin: 0 0 12px 0;">Instructor: <span style="font-weight: 600; color: #475569;"><?php echo htmlspecialchars($rowdata['user_name']);?></span></p>
                                        <p style="font-size: 0.9rem; color: #4B5563; line-height: 1.5; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo htmlspecialchars(strip_tags($rowdata['course_description']));?></p>
                                    </div>
                                </a>
                <?php
                            }
                        } else {
                            echo "<p style='color: #64748B;'>No courses found.</p>";
                        }
                    }   
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Course Panel with sidebar END -->

    <!-- Become instructor START -->
    <?php if(isset($user_role) && $user_role == 3): ?>
    <div style="max-width: 1200px; margin: 0 auto 100px; padding: 0 20px; font-family: 'Inter', sans-serif;">
        <div style="background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%); border-radius: 24px; overflow: hidden; display: grid; grid-template-columns: 1fr 1.2fr; box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.4);">
            <!-- Image Side -->
            <div style="background: rgba(255,255,255,0.05); display: flex; align-items: flex-end; justify-content: center; padding-top: 40px; position: relative;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, rgba(0,0,0,0) 70%); border-radius: 50%; z-index: 0; filter: blur(30px);"></div>
                <img src="assets/img/teacher.png" alt="Teacher" style="max-height: 400px; position: relative; z-index: 1;">
            </div>
            <!-- Content Side -->
            <div style="padding: 60px; display: flex; flex-direction: column; justify-content: center;">
                <h2 style="font-size: 2.8rem; font-weight: 800; color: #F8FAFC; margin: 0 0 16px 0; letter-spacing: -1px; line-height: 1.1;">Become an Instructor</h2>
                <p style="font-size: 1.2rem; color: #94A3B8; margin: 0 0 32px 0; line-height: 1.6; max-width: 400px;">
                    We provide the tools, support, and platform to help you teach what you love to students worldwide.
                </p>
                <div>
                    <a href="becomeins.php" style="display: inline-block; background: #3B82F6; color: #fff; text-decoration: none; padding: 14px 32px; border-radius: 12px; font-bold: 600; font-size: 1.1rem; transition: all 0.2s ease;" onmouseover="this.style.background='#2563EB'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#3B82F6'; this.style.transform='translateY(0)';">
                        Start teaching today
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Become instructor END -->

    <!-- FAQ START -->
    <style>
        .ta-faq-container { max-width: 800px; margin: 0 auto; font-family: 'Inter', sans-serif; padding: 0 20px 100px; }
        .ta-faq-title { text-align: center; color: #0F172A; font-size: 2.2rem; font-weight: 800; margin-bottom: 48px; letter-spacing: -0.5px; }
        .ta-faq-item { background: #fff; border: 1px solid #E5E7EB; border-radius: 12px; margin-bottom: 16px; overflow: hidden; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.02); cursor: pointer; }
        .ta-faq-item:hover { border-color: #CBD5E1; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .ta-faq-item.active { border-color: #3B82F6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
        .ta-faq-que { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; color: #1E293B; font-weight: 600; font-size: 1.1rem; }
        .ta-faq-icon { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background: #F1F5F9; color: #475569; transition: all 0.3s ease; flex-shrink: 0; margin-left: 20px; }
        .ta-faq-item.active .ta-faq-icon { background: #3B82F6; color: #fff; transform: rotate(45deg); }
        .ta-faq-ans { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out, padding 0.3s ease; padding: 0 24px; color: #64748B; font-size: 1rem; line-height: 1.6; }
        .ta-faq-item.active .ta-faq-ans { max-height: 400px; padding: 0 24px 24px 24px; }
    </style>
    <div class="ta-faq-container">
        <h2 class="ta-faq-title">Frequently Asked Questions</h2>
        <div>
            <!-- Item 1 -->
            <div class="ta-faq-item" onclick="this.classList.toggle('active')">
                <div class="ta-faq-que">
                    What makes EduCat different?
                    <div class="ta-faq-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12"></path></svg></div>
                </div>
                <div class="ta-faq-ans">
                    <p>EduCat distinguishes itself through its comprehensive course curation process. We carefully select and review courses to ensure high-quality content, and our advanced recommendation system provides a personalized learning experience tailored to you.</p>
                </div>
            </div>
            <!-- Item 2 -->
            <div class="ta-faq-item" onclick="this.classList.toggle('active')">
                <div class="ta-faq-que">
                    Are the instructors qualified?
                    <div class="ta-faq-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12"></path></svg></div>
                </div>
                <div class="ta-faq-ans">
                    <p>Absolutely. All potential instructors undergo a rigorous vetting process, which includes an evaluation of their credentials, teaching experience, and a sample lesson prior to being approved.</p>
                </div>
            </div>
            <!-- Item 3 -->
            <div class="ta-faq-item" onclick="this.classList.toggle('active')">
                <div class="ta-faq-que">
                    Is my data secure?
                    <div class="ta-faq-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12"></path></svg></div>
                </div>
                <div class="ta-faq-ans">
                    <p>Yes. The security of user data and transactions is paramount. We employ industry-standard encryption protocols to safeguard sensitive information alongside premium payment processing partners.</p>
                </div>
            </div>
            <!-- Item 4 -->
            <div class="ta-faq-item" onclick="this.classList.toggle('active')">
                <div class="ta-faq-que">
                    Can I access courses on mobile?
                    <div class="ta-faq-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12"></path></svg></div>
                </div>
                <div class="ta-faq-ans">
                    <p>Yes, EduCat is designed to be fully responsive, allowing seamless access on smartphones, tablets, and desktop computers.</p>
                </div>
            </div>
            <!-- Item 5 -->
            <div class="ta-faq-item" onclick="this.classList.toggle('active')">
                <div class="ta-faq-que">
                    Is there support for accessible learning?
                    <div class="ta-faq-icon"><svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-6-6h12"></path></svg></div>
                </div>
                <div class="ta-faq-ans">
                    <p>We are continuously working to improve accessibility. Many of our courses offer closed captions, robust formatting, and screen-readable content layouts to support diverse learners.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- FAQ END -->

    <!-- Footer START -->
    <?php 
    include("includes/components/footer.php");
    ?>
    <!-- Footer END -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            slidesPerView: 3,
            spaceBetween: 30,
            loop: true,
            centerSlide: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
                dynamicBullets: true,
            },
            breakpoints: {
                0: { slidesPerView: 1 },
                640: { slidesPerView: 2 },
                1024: { slidesPerView: 3 },
            }
        });
    </script>
    <script>
        // Banner Slider JavaScript

        var imageno = 1;
        displayimg(imageno);

        function nextimg(n) {
            displayimg(imageno += n)
        }

        function currentSlide(n) {
            displayimg(imageno = n)
        }

        function displayimg(n) {
            var i;
            var image = document.getElementsByClassName("image");
            var dots = document.getElementsByClassName("dot");

            if (image.length === 0) return; // Prevent crash if no images

            if (n > image.length) {
                imageno = 1;
            }

            if (n < 1) {
                imageno = image.length;
            }

            for (i = 0; i < image.length; i++) {
                image[i].style.display = "none";
            }

            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }

            image[imageno - 1].style.display = "block";
            if(dots.length > 0 && dots[imageno - 1]){
                dots[imageno - 1].className += " active";
            }
        }
        
        // Auto-play the banner
        setInterval(function(){
            nextimg(1);
        }, 5000);
    </script>
    <script src="assets/js/swiper-bundle.min.js"></script>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>

</html>