<?php
include 'header.php';
include 'sidebar.php';

// Mark all as read if requested
if (isset($_GET['mark_all_read'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id");
    header("Location: notifications.php");
    exit();
}

// Mark single as read
if (isset($_GET['read_id'])) {
    $read_id = $_GET['read_id'];
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE id = $read_id AND user_id = $user_id");
    header("Location: notifications.php");
    exit();
}

// Fetch notifications
$notif_res = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = $user_id ORDER BY created_at DESC");
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 100px 30px 30px;
        transition: all 0.3s ease;
    }

    @media (max-width: 992px) {
        .main-content {
            margin-left: 0;
        }
    }

    .notif-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 18px;
        padding: 20px 25px;
        margin-bottom: 15px;
        display: flex;
        gap: 20px;
        align-items: center;
        transition: all 0.2s;
        position: relative;
    }

    .notif-card.unread {
        border-left: 4px solid var(--primary-color);
        background: #fcfcff;
    }

    .notif-card:hover {
        background: #f8fafc;
        transform: scale(1.01);
    }

    .notif-icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .notif-content {
        flex: 1;
    }

    .notif-content h6 {
        margin: 0;
        font-weight: 700;
        margin-bottom: 3px;
    }

    .notif-content p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--text-muted);
    }

    .notif-time {
        font-size: 0.75rem;
        color: var(--text-muted);
        text-align: right;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="page-header d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="fw-bold">Notifications</h2>
                <p class="text-muted">Stay updated with your learning activity.</p>
            </div>
            <a href="?mark_all_read=1" class="btn btn-outline-primary btn-sm rounded-pill px-4">Mark all as read</a>
        </div>

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <?php if (mysqli_num_rows($notif_res) > 0): ?>
                    <?php while ($n = mysqli_fetch_assoc($notif_res)): 
                        $bg = '#e0e7ff'; $color = '#4338ca'; $icon = 'fa-bell';
                        if ($n['type'] == 'quiz') { $bg = '#fef3c7'; $color = '#d97706'; $icon = 'fa-list-check'; }
                        if ($n['type'] == 'cert') { $bg = '#dcfce7'; $color = '#15803d'; $icon = 'fa-award'; }
                        if ($n['type'] == 'msg') { $bg = '#fae8ff'; $color = '#a21caf'; $icon = 'fa-comment'; }
                        if ($n['type'] == 'course') { $bg = '#dbeafe'; $color = '#2563eb'; $icon = 'fa-book-open'; }
                        if ($n['type'] == 'review') { $bg = '#fdf4ff'; $color = '#c026d3'; $icon = 'fa-star'; }
                    ?>
                        <div class="notif-card <?php echo $n['is_read'] ? '' : 'unread'; ?>">
                            <div class="notif-icon-box" style="background: <?php echo $bg; ?>; color: <?php echo $color; ?>;">
                                <i class="fa-solid <?php echo $icon; ?>"></i>
                            </div>
                            <div class="notif-content">
                                <a href="?read_id=<?php echo $n['id']; ?>" class="text-decoration-none text-main">
                                    <h6><?php echo $n['title']; ?></h6>
                                    <p><?php echo $n['message']; ?></p>
                                </a>
                            </div>
                            <div class="notif-time">
                                <?php echo date('d M, h:i A', strtotime($n['created_at'])); ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fa-regular fa-bell-slash fs-1 text-muted opacity-25 mb-3"></i>
                        <h4>No Notifications</h4>
                        <p class="text-muted">We'll notify you when something important happens.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
