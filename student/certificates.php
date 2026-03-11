<?php
include 'header.php';
include 'sidebar.php';

// Fetch all certificates for the student
$certs_query = "SELECT cm.*, c.course_name, c.course_image, u.user_name as instructor_name 
                FROM certificate_master cm 
                JOIN course_master c ON cm.certificate_course = c.course_id 
                JOIN user_master u ON cm.certificate_instructor = u.user_id 
                WHERE cm.certificate_student = $user_id 
                ORDER BY cm.certificate_date DESC";
$certs_res = mysqli_query($conn, $certs_query);
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

    .cert-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 25px;
        transition: all 0.3s;
        display: flex;
        padding: 20px;
        gap: 25px;
        align-items: center;
    }

    .cert-card:hover {
        transform: scale(1.02);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        border-color: var(--primary-color);
    }

    .cert-preview {
        width: 160px;
        height: 110px;
        background: #f1f5f9;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }

    .cert-preview i {
        font-size: 2.5rem;
        color: #94a3b8;
        z-index: 1;
    }

    .cert-preview::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
    }

    .cert-info {
        flex: 1;
    }

    .cert-info h5 {
        font-weight: 700;
        margin-bottom: 5px;
    }

    .cert-info p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 0;
    }

    .cert-meta {
        display: flex;
        gap: 20px;
        margin-top: 10px;
    }

    .meta-item {
        font-size: 0.75rem;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .cert-actions {
        display: flex;
        gap: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 24px;
        border: 1px dashed var(--border-color);
    }

    .empty-state i {
        font-size: 4rem;
        color: #e2e8f0;
        margin-bottom: 20px;
    }
</style>

<main class="main-content">
    <div class="container-fluid">
        <div class="page-header mb-5">
            <h2 class="fw-bold">My Certificates</h2>
            <p class="text-muted">Showcase your achievements and professional credentials.</p>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <?php if (mysqli_num_rows($certs_res) > 0): ?>
                    <?php while ($cert = mysqli_fetch_assoc($certs_res)): ?>
                        <div class="cert-card">
                            <div class="cert-preview">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div class="cert-info">
                                <h5 class="text-main"><?php echo $cert['course_name']; ?></h5>
                                <p>Issued by <b><?php echo $cert['instructor_name']; ?></b></p>
                                <div class="cert-meta">
                                    <span class="meta-item"><i class="fa-regular fa-calendar"></i> <?php echo $cert['certificate_date']; ?></span>
                                    <span class="meta-item"><i class="fa-solid fa-hashtag"></i> ID: EDU-<?php echo str_pad($cert['certificate_id'], 6, '0', STR_PAD_LEFT); ?></span>
                                    <span class="meta-item text-primary fw-bold"><i class="fa-solid fa-chart-simple"></i> Score: <?php echo $cert['certificate_score']; ?>%</span>
                                </div>
                            </div>
                            <div class="cert-actions">
                                <a href="../certificate.php?id=<?php echo $cert['certificate_course']; ?>" target="_blank" class="btn btn-primary rounded-pill px-4" style="font-weight: 600;">View Certificate</a>
                                <!-- <button class="btn btn-outline-secondary rounded-pill px-3"><i class="fa-solid fa-share-nodes"></i></button> -->
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-certificate"></i>
                        <h3>No Certificates Yet</h3>
                        <p class="text-muted mx-auto" style="max-width: 400px;">Complete courses and pass the final assessments to earn your professional certificates.</p>
                        <a href="my-courses.php" class="btn btn-primary mt-3 px-4 rounded-pill">View My Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>
