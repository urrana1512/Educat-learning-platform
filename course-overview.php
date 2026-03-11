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
    if(isset($_GET["id"])){
        $courseID = $_GET["id"];
        $query = "SELECT * FROM course_master WHERE course_id = $courseID";
        $res = $conn->query($query);
        if ($res->num_rows > 0) {
            $rowForData = $res->fetch_assoc();
        }
    }else{
        header("Location: 404.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $rowForData["course_name"];?> | EduCat</title>
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
    <!-- High-Fidelity Course Page Wrapper -->
    <div style="font-family: 'Inter', sans-serif; background-color: #f8fafc; padding-bottom: 60px;">
        
        <!-- HERO SECTION: Deep Charcoal Dark Mode -->
        <section style="background-color: #111827; color: #fff; padding: 60px 0; position: relative;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 350px; gap: 40px;" class="co-main-grid">
                
                <!-- Left Content: Hero Info -->
                <div>
                    <!-- Breadcrumbs -->
                    <div style="color: #9CA3AF; font-size: 0.95rem; margin-bottom: 20px; font-weight: 500;">
                        <?php
                            $categoryID = $rowForData["course_category"];
                            $queryCat = "SELECT course_category_name FROM course_category_master WHERE course_category_id = $categoryID"; 
                            $resCat = $conn->query($queryCat);
                            if ($resCat->num_rows > 0) {
                                $rowForCat = $resCat->fetch_assoc();
                                echo htmlspecialchars($rowForCat["course_category_name"]);
                            }
                        ?>
                        <span style="margin: 0 8px;">&gt;</span>
                        <span style="color: #D1D5DB;"><?php echo htmlspecialchars($rowForData["course_name"]);?></span>
                    </div>

                    <!-- Course Title -->
                    <h1 style="font-family: 'Inter', sans-serif; font-size: 2.8rem; font-weight: 800; margin-bottom: 15px; color: #fff; line-height: 1.2;">
                        <?php echo htmlspecialchars($rowForData["course_name"]);?>
                    </h1>

                    <!-- Badge -->
                    <span style="background: rgba(59, 130, 246, 0.2); color: #3B82F6; padding: 4px 10px; border-radius: 6px; font-size: 0.8rem; font-weight: 600; display: inline-block; margin-bottom: 20px;">
                        Updated
                    </span>

                    <!-- Short Description -->
                    <p style="font-size: 1.1rem; color: #D1D5DB; margin-bottom: 30px; line-height: 1.6; max-width: 90%;">
                        <?php echo htmlspecialchars(implode(' ', array_slice(str_word_count($rowForData["course_description"], 1), 0, 30))); ?>...
                    </p>

                    <!-- Meta Data -->
                    <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: center; font-size: 0.95rem; color: #9CA3AF; margin-bottom: 20px;">
                        
                        <!-- Ratings Logic -->
                        <?php
                        function calculateWeightedAverage($ratings) {
                            $totalWeight = 0; $weightedSum = 0;
                            foreach ($ratings as $key => $value) {
                                $weight = $key + 1;
                                $totalWeight += $weight;
                                $weightedSum += $weight * $value;
                            }
                            return ($totalWeight == 0) ? 0 : ($weightedSum / $totalWeight);
                        }
                        $sql = "SELECT * FROM course_rating WHERE course = $courseID";
                        $result = mysqli_query($conn, $sql);
                        $totalRating = 0; $totalStars = 0;
                        if (mysqli_num_rows($result) > 0) {
                            $totalRatings = mysqli_fetch_assoc($result);
                            $ratings = array($totalRatings['rating_1'], $totalRatings['rating_2'], $totalRatings['rating_3'], $totalRatings['rating_4'], $totalRatings['rating_5']);
                            $courseRating = calculateWeightedAverage($ratings);
                            $totalRating = number_format($courseRating, 2);
                            if (($totalRating > 0) && ($totalRating < 5)) {
                                $parts = explode('.', strval($totalRating));
                                $totalStars = $parts[0];
                            } elseif ($totalRating >= 5) {
                                $totalStars = 5;
                            }
                        } 
                        ?>
                        
                        <!-- Stars -->
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <?php if ($totalRating > 0): ?>
                                <span style="color: #FBBF24; font-weight: 700;"><?php echo ($totalRating >= 5) ? "5.0" : $totalRating; ?></span>
                                <span style="color: #FBBF24; display: flex;">
                                    <?php for($i=0; $i<$totalStars; $i++) echo '<svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'; ?>
                                </span>
                                <span style="color: #6B7280; font-size: 0.85rem; margin-left: 5px;">(<?php echo $rowForData["course_purchases"];?> enrollments)</span>
                            <?php else: ?>
                                <span style="background: #374151; color: #E5E7EB; padding: 2px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">New Course</span>
                                <span style="color: #4B5563; display: flex; gap: 2px; margin-left: 5px;">
                                    <?php for($i=0;$i<5;$i++) echo '<svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>'; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Instructor -->
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            Created by <?php
                                $instructorID = $rowForData["course_instructor"];
                                $queryINST = "SELECT user_name FROM user_master WHERE user_id = $instructorID"; 
                                $resINST = $conn->query($queryINST);
                                if ($resINST->num_rows > 0) {
                                    $rowForINST = $resINST->fetch_assoc();
                                    echo "<strong style='color:#fff;'>" . htmlspecialchars($rowForINST["user_name"]) . "</strong>";
                                }
                            ?>
                        </div>

                        <!-- Last Updated -->
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Last updated <?php echo htmlspecialchars($rowForData["course_last_updated_on"]); ?>
                        </div>
                        
                        <!-- Language -->
                        <?php 
                            $langaugeID = $rowForData["course_language"];
                            $queryForLANG = "SELECT language_name FROM languages_master WHERE language_id = $langaugeID";
                            $resForLANG = mysqli_query($conn, $queryForLANG);
                            if ($resForLANG->num_rows > 0) {
                                $rowForLANG = $resForLANG->fetch_assoc();
                            }
                        ?>
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                            <?php echo htmlspecialchars($rowForLANG["language_name"]); ?>
                        </div>
                    </div>
                </div>

                <!-- Right Content: Sticky Sidebar Glassmorphism Card -->
                <div style="position: relative;" class="co-sidebar-hide-mobile">
                    <div style="position: absolute; top: 0; width: 100%; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; padding: 25px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); z-index: 10;" class="co-sticky-card">
                        
                        <!-- Course Thumbnail -->
                        <div style="border-radius: 8px; overflow: hidden; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); position: relative;">
                            <img src="<?php echo htmlspecialchars($rowForData["course_image"]);?>" style="width: 100%; height: 200px; object-fit: cover; display: block;" alt="Course Thumbnail">
                            <div style="position: absolute; top:50%; left:50%; transform:translate(-50%,-50%); width: 60px; height: 60px; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); border-radius: 50%; display:flex; align-items:center; justify-content:center; color: #fff; cursor: pointer; transition: background 0.2s ease;" onmouseover="this.style.background='rgba(59,130,246,0.8)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6V4z"></path></svg>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div style="margin-bottom: 20px;">
                            <?php 
                            $original_price = $rowForData["course_price"];
                            $discount_rate = $rowForData["course_discount"];
                            $final_price = ($discount_rate > 0) ? $original_price - ($original_price * ($discount_rate / 100)) : $original_price;
                            ?>

                            <?php if ($discount_rate > 0): ?>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="font-size: 2.2rem; font-weight: 800; color: #fff;">&#8377;<?php echo $final_price; ?></span>
                                    <span style="font-size: 1.1rem; color: #9CA3AF; text-decoration: line-through;">&#8377;<?php echo $original_price; ?></span>
                                    <span style="color: #3B82F6; font-weight: 600; font-size: 0.95rem;"><?php echo $discount_rate; ?>% off</span>
                                </div>
                                
                                <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #FCA5A5; padding: 8px 0px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; margin-top: 15px; display: inline-flex; align-items: center; gap: 8px; width: 100%; justify-content: center;">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Few hours left at this price!
                                </div>
                            <?php else: ?>
                                <span style="font-size: 2.2rem; font-weight: 800; color: #fff;">&#8377;<?php echo $final_price; ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- CTA Buttons -->
                        <?php 
                            $isPurchased = false;
                            $isFavorite = false;
                            if(isset($_GET["id"]) && isset($user_id)){
                                $check_query = "SELECT * FROM purchased_course_master WHERE course_id = $courseID AND user_id = $user_id";
                                $check_res = mysqli_query($conn, $check_query);
                                if(mysqli_num_rows($check_res) > 0){
                                    $check_row = $check_res->fetch_assoc();
                                    $isPurchased = true;
                                    $isFavorite = ($check_row["course_favorite"] != 0);
                                }
                            }
                        ?>

                        <?php if($isPurchased): ?>
                            <a href="play.php?id=<?php echo htmlspecialchars($_GET["id"]);?>" style="display: block; width: 100%; text-align: center; background: #3B82F6; color: #fff; padding: 16px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; text-decoration: none; transition: background 0.2s ease, transform 0.2s ease;" onmouseover="this.style.background='#2563EB'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#3B82F6'; this.style.transform='translateY(0)'">
                                Go to Course
                            </a>
                            <a href="myheart.php?id=<?php echo htmlspecialchars($_GET["id"]);?>" style="display: block; width: 100%; text-align: center; background: transparent; border: 1px solid rgba(255,255,255,0.2); color: <?php echo $isFavorite ? '#ef4444' : '#fff'; ?>; padding: 16px; border-radius: 8px; font-size: 1.1rem; font-weight: 600; text-decoration: none; margin-top: 15px; transition: background 0.2s ease;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                                <?php echo $isFavorite ? 'Saved to Wishlist ❤' : 'Add to Wishlist ♡'; ?>
                            </a>
                        <?php else: ?>
                            <?php if(isset($user_id)): ?>
                                <a id="rzp-button1" style="display: block; width: 100%; text-align: center; background: #3B82F6; color: #fff; padding: 16px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; text-decoration: none; cursor: pointer; transition: background 0.2s ease, transform 0.2s ease;" onmouseover="this.style.background='#2563EB'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#3B82F6'; this.style.transform='translateY(0)'">
                                    Enroll Now
                                </a>
                            <?php else: ?>
                                <a href="sign-in.php" style="display: block; width: 91%; text-align: center; background: #3B82F6; color: #fff; padding: 16px; border-radius: 8px; font-size: 1.1rem; font-weight: 700; text-decoration: none; cursor: pointer; transition: background 0.2s ease, transform 0.2s ease;" onmouseover="this.style.background='#2563EB'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='#3B82F6'; this.style.transform='translateY(0)'">
                                    Enroll Now
                                </a>
                            <?php endif; ?>
                            
                            <a href="#" style="display: block; width: 91%; text-align: center; background: transparent; border: 1px solid #000; color: #000; padding: 14px; border-radius: 8px; font-size: 1rem; font-weight: 600; text-decoration: none; margin-top: 15px; transition: background 0.2s ease;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='transparent'">
                                Add to Wishlist ♡
                            </a>
                        <?php endif; ?>

                        <div style="text-align: center; margin-top: 20px;">
                            <span style="color: #9CA3AF; font-size: 0.85rem;">30-Day Money-Back Guarantee</span>
                        </div>

                        <!-- Features list on hover -->
                        <div style="margin-top: 25px; color: #D1D5DB; font-size: 0.95rem;">
                            <p style="font-weight: 700; margin-bottom: 15px; color: #000; font-size: 1.05rem;">This course includes:</p>
                            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
                                <li style="display: flex; align-items: center; gap: 12px; color: #000;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg> Downloadable videos
                                </li>
                                <li style="display: flex; align-items: center; gap: 12px; color: #000;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg> Assignments
                                </li>
                                <li style="display: flex; align-items: center; gap: 12px; color: #000;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Downloadable resources
                                </li>
                                <li style="display: flex; align-items: center; gap: 12px; color: #000;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg> Access on all devices
                                </li>
                                <li style="display: flex; align-items: center; gap: 12px; color: #000;">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg> Certificate of completion
                                </li>
                            </ul>
                        </div>

                        <!-- Share -->
                        <div style="display: flex; justify-content: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
                            <a href="https://wa.me/?text=Hey, i found very usefull course on Educat checkout this: <?php echo urlencode($domain . "/course-overview.php?id=" . $_GET["id"])?>" style="color: #60A5FA; text-align: center; text-decoration: none; font-weight: 500; display: flex; align-items: center; gap: 6px; transition: color 0.2s ease;" onmouseover="this.style.color='#93C5FD'" onmouseout="this.style.color='#60A5FA'">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path></svg>
                                Share
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- MAIN CONTENT -->
    <main style="max-width: 1200px; margin: 0 auto; padding: 60px 20px; display: grid; grid-template-columns: 1fr 350px; gap: 40px; align-items: start;" class="co-main-grid">
        <div style="display: flex; flex-direction: column; gap: 50px;">
            
            <!-- What You'll Learn Grid -->
            <section style="background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #E5E7EB;">
                <h2 style="font-family: 'Inter', sans-serif; font-size: 1.5rem; font-weight: 700; margin-bottom: 30px; color: #111827;">What you'll learn</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
                    <?php
                        $courseId = $_GET["id"];
                        $sql = "SELECT * FROM course_chapter_list WHERE course_id = $courseId ORDER BY course_chapter_id ASC";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row1 = $result->fetch_assoc()) {
                    ?>
                                <div style="display: flex; gap: 12px; align-items: flex-start;">
                                    <svg width="20" height="20" fill="none" stroke="#2563EB" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                    <span style="color: #4B5563; font-size: 0.95rem; line-height: 1.5;"><?php echo htmlspecialchars($row1["course_chapter_name"]);?></span>
                                </div>
                    <?php
                            }
                        } else {
                    ?>
                            <!-- Empty states 4-6 bullet points placeholder -->
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <svg width="20" height="20" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                <span style="color: #6B7280; font-size: 0.95rem; line-height: 1.5;">Master the core concepts of this subject from scratch.</span>
                            </div>
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <svg width="20" height="20" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                <span style="color: #6B7280; font-size: 0.95rem; line-height: 1.5;">Build practical skills and add them to your portfolio.</span>
                            </div>
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <svg width="20" height="20" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                <span style="color: #6B7280; font-size: 0.95rem; line-height: 1.5;">Understand advanced techniques used by professionals.</span>
                            </div>
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <svg width="20" height="20" fill="none" stroke="#9CA3AF" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; margin-top: 2px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                                <span style="color: #6B7280; font-size: 0.95rem; line-height: 1.5;">Gain the confidence to tackle real-world problems.</span>
                            </div>
                    <?php
                        }
                    ?>
                </div>
            </section>

            <!-- Description -->
            <section>
                <h2 style="font-family: 'Inter', sans-serif; font-size: 1.5rem; font-weight: 700; margin-bottom: 25px; color: #111827;">Description</h2>
                <div style="color: #4B5563; line-height: 1.8; font-size: 1.05rem;">
                    <?php echo nl2br(htmlspecialchars($rowForData["course_description"])); ?>
                </div>
            </section>

            <!-- Course Content Accordion -->
            <section style="margin-bottom: 60px;">
                <h2 style="font-family: 'Inter', sans-serif; font-size: 1.5rem; font-weight: 700; margin-bottom: 25px; color: #111827;">Course Content</h2>
                <div style="border: 1px solid #E5E7EB; border-radius: 12px; overflow: hidden; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <?php
                    $sql = "SELECT * FROM course_chapter_list WHERE course_id = $courseId ORDER BY course_chapter_id ASC";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $idx = 0;
                        while ($row = $result->fetch_assoc()) {
                            $chapterId =  $row["course_chapter_id"];
                            $isActive = ($idx === 0) ? 'display: block;' : 'display: none;';
                            $iconRot = ($idx === 0) ? 'transform: rotate(180deg);' : '';
                            $bgStyle = ($idx === 0) ? 'background-color: #F3F4F6;' : 'background-color: #F9FAFB; border-top: 1px solid #E5E7EB;';
                            ?>
                            <div class="elearn-acc-item">
                                <button class="elearn-acc-btn" style="width: 100%; text-align: left; padding: 18px 24px; <?php echo $bgStyle; ?> border: none; font-size: 1.05rem; font-weight: 600; color: #111827; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: background 0.2s ease;" onclick="
                                    var body = this.nextElementSibling;
                                    var icon = this.querySelector('svg');
                                    if(body.style.display === 'block') {
                                        body.style.display = 'none';
                                        this.style.backgroundColor = '#F9FAFB';
                                        icon.style.transform = 'rotate(0deg)';
                                    } else {
                                        body.style.display = 'block';
                                        this.style.backgroundColor = '#F3F4F6';
                                        icon.style.transform = 'rotate(180deg)';
                                    }
                                ">
                                    <?php echo htmlspecialchars($row["course_chapter_name"]);?>
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="transition: transform 0.3s ease; <?php echo $iconRot;?>"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div class="elearn-acc-body" style="<?php echo $isActive; ?> background: #fff;">
                                    <ul style="list-style: none; padding: 0; margin: 0;">
                                        <?php
                                        $sql2 = "SELECT * FROM videos_master WHERE video_of_chapter = $chapterId ORDER BY video_id ASC";
                                        $result2 = $conn->query($sql2);
                                        if ($result2->num_rows > 0) {
                                            while ($rowOfVideo = $result2->fetch_assoc()) {
                                                ?>
                                                <li style="padding: 16px 24px 16px 48px; border-bottom: 1px solid #F3F4F6; display: flex; align-items: center; gap: 15px; color: #4B5563; font-size: 0.95rem;">
                                                    <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" style="color: #2563EB;"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                                    <?php echo htmlspecialchars($rowOfVideo["video_name"]);?>
                                                </li>
                                                <?php
                                            }
                                        } else {
                                            echo '<li style="padding: 16px 24px 16px 48px; border-bottom: 1px solid #F3F4F6; color: #9CA3AF; font-size: 0.95rem;">No lessons uploaded yet.</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <?php
                            $idx++;
                        }
                    } else {
                        echo '<div style="padding: 30px; text-align: center; color: #6B7280;">No course content available.</div>';
                    }
                    ?>
                </div>
            </section>
        </div>
        
        <!-- Empty Div to keep the grid spacing for the sticky sidebar -->
        <div style="display: none;" class="co-sidebar-hide-mobile"></div>
    </main>
</div>
    <?php 
    include("includes/components/footer.php");
    ?>

    <?php
        
        $final_price = ($discount_rate > 0)? $original_price - ($original_price * ($discount_rate / 100)):$original_price;
    ?>

    <script src="assets/js/payment_sim.js"></script>
    <script>
    var rzpbtn = document.getElementById('rzp-button1');
    if (rzpbtn) {
        rzpbtn.onclick = function (e) {
            e.preventDefault();
            RazorpaySim.init({
                "amount": "<?php echo $final_price * 100?>",
                "currency": "INR",
                "name": "EduCat",
                "image": "https://i.ibb.co/QK5xyg0/Edu-Cat-4.png",
                "handler": function (response) {
                    window.location.href = "includes/scripts/purchase_course.php?id=<?php echo $_GET["id"];?>";
                }
            });
            RazorpaySim.open();
        }
    }
    </script>
</body>
</html>