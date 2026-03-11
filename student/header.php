<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../includes/scripts/connection.php';

// Check if user is logged in
if (!isset($_SESSION['educat_logedin_user_id']) || empty($_SESSION['educat_logedin_user_id'])) {
    header("Location: ../sign-in.php");
    exit();
}

$user_id = $_SESSION['educat_logedin_user_id'];
$query = "SELECT * FROM user_master WHERE user_id = $user_id";
$result = mysqli_query($conn, $query);
$userdata = mysqli_fetch_assoc($result);

// Ensure user is a student (Role 3)
if ($userdata['role'] != 3) {
    // If Admin or Instructor, redirect to their respective dashboard or homepage
    // For now, let's just let them stay if they are testing, but normally we'd restrict
    // header("Location: ../index.php");
    // exit();
}

// Fetch notification count
$notif_count_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = $user_id AND is_read = 0";
$notif_count_res = mysqli_query($conn, $notif_count_query);
$notif_count = mysqli_fetch_assoc($notif_count_res)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Panel - EduCat</title>
    <link rel="shortcut icon" type="image/x-icon" href="../assets/img/EduCat (4)_rm.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --bg-color: #f8fafc;
            --sidebar-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
        }

        /* Header Styles */
        .student-header {
            position: fixed;
            top: 0;
            right: 0;
            left: 260px;
            height: 70px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1000;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 0 30px;
            justify-content: space-between;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .search-bar {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 5px 15px;
            display: flex;
            align-items: center;
            width: 300px;
        }

        .search-bar i {
            color: var(--text-muted);
        }

        .search-bar input {
            background: transparent;
            border: none;
            padding: 5px 10px;
            width: 100%;
            outline: none;
            font-size: 0.9rem;
        }

        .icon-btn {
            position: relative;
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            transition: all 0.2s;
            color: var(--text-main);
        }

        .icon-btn:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .icon-btn .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            font-size: 0.7rem;
            padding: 4px 6px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 5px;
            border-radius: 12px;
            transition: all 0.2s;
        }

        .user-profile:hover {
            background: #f1f5f9;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .user-info h6 {
            margin: 0;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-info p {
            margin: 0;
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        @media (max-width: 992px) {
            .student-header {
                left: 0;
            }
            .search-bar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <header class="student-header">
        <div class="header-left">
            <button class="icon-toggle d-lg-none" id="sidebarToggle">
                <i class="fa fa-bars"></i>
            </button>
            <div class="search-bar">
                <i class="fa fa-search"></i>
                <input type="text" placeholder="Search courses, lessons...">
            </div>
        </div>
        
        <div class="header-right">
            <a href="notifications.php" class="icon-btn">
                <i class="fa-regular fa-bell"></i>
                <?php if ($notif_count > 0): ?>
                    <span class="badge"><?php echo $notif_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="messages.php" class="icon-btn">
                <i class="fa-regular fa-comment-dots"></i>
            </a>
            <div class="dropdown">
                <div class="user-profile" data-bs-toggle="dropdown">
                    <img src="../<?php echo $userdata['user_profile_photo']; ?>" alt="Profile">
                    <div class="user-info d-none d-sm-block">
                        <h6><?php echo $userdata['user_name']; ?></h6>
                        <p>Student Member</p>
                    </div>
                </div>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm mt-3" style="border-radius: 15px; padding: 10px;">
                    <li><a class="dropdown-item py-2" href="profile.php" style="border-radius: 8px;"><i class="fa-regular fa-user me-2"></i> My Profile</a></li>
                    <li><a class="dropdown-item py-2" href="certificates.php" style="border-radius: 8px;"><i class="fa-solid fa-award me-2"></i> Certificates</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="../logout.php" style="border-radius: 8px;"><i class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </header>
