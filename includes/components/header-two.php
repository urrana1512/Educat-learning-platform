<?php
$currentURL = $_SERVER['PHP_SELF'];
$currentPage = basename($currentURL);
?>

<header>
    <nav class="navigation">
        <div class="logo">
            <a href="index.php"><img src="assets/img/EduCat (3).png" alt="Logo"></a>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Search...">
            <!-- <a href="#" style="display: flex; align-items: center;"><img src="assets/img/search.svg" alt="search" height="25px" width="25px"></a> -->
        </div>
        <button class="menu-btn">☰</button>
        <div class="sidebar">
            <div class="menu-content">
                <a href="student/index.php" class="buttons <?php if($currentPage == "mycourse.php"){echo "active";}?>">My Courses</a>
                <?php
                    if(($currentPage == "mycourse.php") || ($currentPage == "mycourse-completed.php") || ($currentPage == "mycourse-favourites.php")){
                        ?>
                        <a href="mycourse-completed.php" class="buttons <?php if($currentPage == "mycourse-completed.php"){echo "active";}?>">Completed Courses</a>
                        <a href="mycourse-favourites.php" class="buttons <?php if($currentPage == "mycourse-favourites.php"){echo "active";}?>" style="color:red; font-size: 1rem">❤</a>
                        <?php
                    }
                    ?>
                <!-- <a href="myaccount-security.php" class="buttons <?php if($currentPage == "myaccount-security.php"){echo "active";}?>">Security</a> -->
                <a href="myaccount.php" class="buttons <?php if($currentPage == "myaccount.php"){echo "active";}?>">My Profile</a>
            </div>
        </div>
    </nav>
</header>