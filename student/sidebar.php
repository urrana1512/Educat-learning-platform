<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
    /* Sidebar Styles */
    .student-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 260px;
        background: var(--sidebar-bg);
        border-right: 1px solid var(--border-color);
        z-index: 1001;
        transition: all 0.3s ease;
        padding: 20px 0;
        display: flex;
        flex-direction: column;
    }

    .sidebar-logo {
        padding: 0 30px 30px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-logo img {
        height: 40px;
    }

    .sidebar-logo h4 {
        margin: 0;
        font-weight: 700;
        font-size: 1.4rem;
        background: linear-gradient(to right, var(--primary-color), #818cf8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .sidebar-menu {
        flex: 1;
        padding: 0 15px;
        overflow-y: auto;
    }

    .menu-label {
        padding: 10px 15px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--text-muted);
        letter-spacing: 0.05em;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        color: var(--text-main);
        text-decoration: none;
        border-radius: 12px;
        margin-bottom: 5px;
        transition: all 0.2s;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .menu-item i {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
        color: var(--text-muted);
    }

    .menu-item:hover {
        background: #f1f5f9;
        color: var(--primary-color);
    }

    .menu-item:hover i {
        color: var(--primary-color);
    }

    .menu-item.active {
        background: #eef2ff;
        color: var(--primary-color);
    }

    .menu-item.active i {
        color: var(--primary-color);
    }

    .sidebar-footer {
        padding: 20px 15px 0;
        border-top: 1px solid var(--border-color);
        margin: 0 15px;
    }

    .upgrade-card {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        border-radius: 16px;
        padding: 20px;
        color: white;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .upgrade-card h6 {
        font-weight: 600;
        margin-bottom: 5px;
        position: relative;
        z-index: 1;
    }

    .upgrade-card p {
        font-size: 0.8rem;
        opacity: 0.9;
        margin-bottom: 12px;
        position: relative;
        z-index: 1;
    }

    .upgrade-card .btn-light {
        font-size: 0.8rem;
        font-weight: 600;
        padding: 8px 15px;
        border-radius: 10px;
        position: relative;
        z-index: 1;
    }

    .upgrade-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    @media (max-width: 992px) {
        .student-sidebar {
            left: -260px;
        }
        .student-sidebar.show {
            left: 0;
            box-shadow: 10px 0 30px rgba(0,0,0,0.1);
        }
    }
</style>

<aside class="student-sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="../assets/img/EduCat (4)_rm.png" alt="Logo">
        <h4>EduCat</h4>
    </div>

    <div class="sidebar-menu">
        <div class="menu-label">Main Menu</div>
        <a href="index.php" class="menu-item <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-house"></i>
            <span>Dashboard</span>
        </a>
        <a href="my-courses.php" class="menu-item <?php echo ($current_page == 'my-courses.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-layer-group"></i>
            <span>My Courses</span>
        </a>
        <a href="wishlist.php" class="menu-item <?php echo ($current_page == 'wishlist.php') ? 'active' : ''; ?>">
            <i class="fa-regular fa-heart"></i>
            <span>Wishlist</span>
        </a>
        
        <div class="menu-label mt-4">Personal</div>
        <a href="analytics.php" class="menu-item <?php echo ($current_page == 'analytics.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Learning Analytics</span>
        </a>
        <a href="certificates.php" class="menu-item <?php echo ($current_page == 'certificates.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-award"></i>
            <span>Certificates</span>
        </a>
        <a href="reviews.php" class="menu-item <?php echo ($current_page == 'reviews.php') ? 'active' : ''; ?>">
            <i class="fa-regular fa-star"></i>
            <span>My Reviews</span>
        </a>

        <div class="menu-label mt-4">Communication</div>
        <a href="messages.php" class="menu-item <?php echo ($current_page == 'messages.php') ? 'active' : ''; ?>">
            <i class="fa-regular fa-comments"></i>
            <span>Messages</span>
        </a>
        <a href="notifications.php" class="menu-item <?php echo ($current_page == 'notifications.php') ? 'active' : ''; ?>">
            <i class="fa-regular fa-bell"></i>
            <span>Notifications</span>
        </a>

        <div class="menu-label mt-4">Account</div>
        <a href="profile.php" class="menu-item <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fa-regular fa-user"></i>
            <span>Profile Settings</span>
        </a>
    </div>

    
</aside>
