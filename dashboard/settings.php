<?php
require '../includes/scripts/connection.php';
session_start();
if(!isset($_SESSION['educat_logedin_user_id'])){ header("Location: ../sign-in.php"); exit(); }
$u = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM user_master WHERE user_id=".(int)$_SESSION['educat_logedin_user_id']));
if(!$u || $u['role']!=1){ header("Location: ../404.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Settings — EduCat Admin</title>
<link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/plugins/fontawesome/css/all.min.css">
<link rel="stylesheet" href="../instructor/assets/css/instructor.css">
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("sidebar.php"); ?>
    <div class="ins-main">
        <?php include("header.php"); ?>
        <div class="ins-content">
            <div class="ins-page-header">
                <div>
                    <h2 class="ins-page-title">Platform Settings</h2>
                    <p class="ins-page-subtitle">Configure the platform, notifications, and admin preferences.</p>
                </div>
            </div>

            <!-- Tabs -->
            <ul class="nav mb-4" id="settingsTabs" style="gap:8px;">
                <li class="nav-item"><a href="#general" class="ins-btn ins-btn-primary" onclick="showTab('general',this)">General</a></li>
                <li class="nav-item"><a href="#security" class="ins-btn ins-btn-outline" onclick="showTab('security',this)">Security</a></li>
                <li class="nav-item"><a href="#notifications" class="ins-btn ins-btn-outline" onclick="showTab('notifications',this)">Notifications</a></li>
            </ul>

            <!-- General -->
            <div id="tab-general">
                <div class="ins-card mb-4">
                    <div class="ins-card__header"><h6 class="mb-0 fw-bold"><i class="fas fa-globe me-2 text-primary"></i> General Configuration</h6></div>
                    <div class="ins-card__body">
                        <form>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="ins-form-label">Platform Name</label>
                                <input type="text" class="ins-form-control" value="EduCat">
                            </div>
                            <div class="col-md-6">
                                <label class="ins-form-label">Admin Email</label>
                                <input type="email" class="ins-form-control" value="<?php echo htmlspecialchars($u['user_email']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="ins-form-label">Support Email</label>
                                <input type="email" class="ins-form-control" placeholder="support@educat.com">
                            </div>
                            <div class="col-md-6">
                                <label class="ins-form-label">Platform URL</label>
                                <input type="url" class="ins-form-control" placeholder="https://educat.com">
                            </div>
                            <div class="col-12">
                                <label class="ins-form-label">Platform Description</label>
                                <textarea class="ins-form-control" rows="3" placeholder="A brief description of your platform..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="button" class="ins-btn ins-btn-primary px-5" onclick="alert('Settings saved successfully!')">Save Settings</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Security -->
            <div id="tab-security" style="display:none;">
                <div class="ins-card mb-4">
                    <div class="ins-card__header"><h6 class="mb-0 fw-bold"><i class="fas fa-shield-alt me-2 text-primary"></i> Security & Access</h6></div>
                    <div class="ins-card__body">
                        <form>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="ins-form-label">Current Password</label>
                                <input type="password" class="ins-form-control" placeholder="Enter current password">
                            </div>
                            <div class="col-md-6">
                                <label class="ins-form-label">New Password</label>
                                <input type="password" class="ins-form-control" placeholder="Enter new password">
                            </div>
                            <div class="col-md-6">
                                <label class="ins-form-label">Confirm New Password</label>
                                <input type="password" class="ins-form-control" placeholder="Confirm new password">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="twoFactor" checked>
                                    <label class="form-check-label ms-2" for="twoFactor">Enable Two-Factor Authentication</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="button" class="ins-btn ins-btn-primary px-5" onclick="alert('Security settings updated!')">Update Security</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div id="tab-notifications" style="display:none;">
                <div class="ins-card">
                    <div class="ins-card__header"><h6 class="mb-0 fw-bold"><i class="fas fa-bell me-2 text-primary"></i> Notification Preferences</h6></div>
                    <div class="ins-card__body">
                        <?php
                        $notifs = [
                            ['New User Registration','Notify when a new user registers'],
                            ['New Course Submitted','Alert when instructor submits a new course for review'],
                            ['New Enrollment','Alert on every new enrollment/payment'],
                            ['New Review Posted','Alert on new student reviews'],
                            ['New Contact Message','Alert on support messages'],
                        ];
                        foreach($notifs as $i => $n): ?>
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3" style="border-bottom: 1px solid var(--border-light);">
                            <div>
                                <div class="fw-600"><?php echo $n[0]; ?></div>
                                <div class="smallest text-muted"><?php echo $n[1]; ?></div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="notif<?php echo $i; ?>" checked>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <button type="button" class="ins-btn ins-btn-primary px-5" onclick="alert('Notification preferences saved!')">Save Preferences</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
function showTab(tab, el) {
    event.preventDefault();
    ['general','security','notifications'].forEach(t => {
        document.getElementById('tab-'+t).style.display = 'none';
    });
    document.getElementById('tab-'+tab).style.display = 'block';
    document.querySelectorAll('#settingsTabs a').forEach(a => {
        a.className = 'ins-btn ins-btn-outline';
    });
    el.className = 'ins-btn ins-btn-primary';
}
</script>
</body></html>
