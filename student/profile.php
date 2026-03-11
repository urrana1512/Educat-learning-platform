<?php
include 'header.php';
include 'sidebar.php';

$update_msg = "";

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    
    // Handle Profile Photo Upload
    if (!empty($_FILES['profile_photo']['name'])) {
        $photo_name = time() . '_' . $_FILES['profile_photo']['name'];
        $photo_tmp = $_FILES['profile_photo']['tmp_name'];
        $photo_path = "client/profile_photo/" . $photo_name;
        
        if (move_uploaded_file($photo_tmp, "../" . $photo_path)) {
            mysqli_query($conn, "UPDATE user_master SET user_profile_photo = '$photo_path' WHERE user_id = $user_id");
        }
    }
    
    $update_query = "UPDATE user_master SET user_name = '$name', user_email = '$email', user_contact_number = '$contact' WHERE user_id = $user_id";
    if (mysqli_query($conn, $update_query)) {
        $update_msg = "<div class='alert alert-success'>Profile updated successfully!</div>";
        // Refresh $userdata
        $result = mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id");
        $userdata = mysqli_fetch_assoc($result);
    } else {
        $update_msg = "<div class='alert alert-danger'>Error updating profile: " . mysqli_error($conn) . "</div>";
    }
}
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

    .profile-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .profile-img-header {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 30px;
    }

    .profile-img-header img {
        width: 100%;
        height: 100%;
        border-radius: 30px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .edit-photo-btn {
        position: absolute;
        bottom: -5px;
        right: -5px;
        background: var(--primary-color);
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid white;
        transition: all 0.2s;
    }

    .edit-photo-btn:hover {
        background: var(--primary-hover);
        transform: scale(1.1);
    }

    .form-label {
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid var(--border-color);
        background: #f8fafc;
        font-size: 0.95rem;
    }

    .form-control:focus {
        background: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .save-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.2s;
    }

    .save-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="page-header mb-5 text-center">
            <h2 class="fw-bold">My Profile</h2>
            <p class="text-muted">Manage your personal information and account settings.</p>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <?php echo $update_msg; ?>
                <div class="profile-card">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="profile-img-header">
                            <img src="../<?php echo $userdata['user_profile_photo']; ?>" alt="Profile" id="profileDisplay">
                            <label for="photoInput" class="edit-photo-btn">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file" name="profile_photo" id="photoInput" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo $userdata['user_name']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $userdata['user_email']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Contact Number</label>
                                <input type="text" name="contact" class="form-control" value="<?php echo $userdata['user_contact_number']; ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="Student" disabled>
                            </div>
                            <div class="col-12 text-center mt-5">
                                <button type="submit" name="update_profile" class="save-btn px-5">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileDisplay').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php include 'footer.php'; ?>
