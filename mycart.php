<?php
    require 'includes/scripts/connection.php';  
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim ($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id = $_SESSION['educat_logedin_user_id'];
        $query = "SELECT * FROM user_master WHERE user_id = $user_id";
        $result = mysqli_query($conn, $query);
        $userdata = mysqli_fetch_assoc($result);
        $user_role = $userdata["role"];
    } else {
        header("Location: sign-in.php"); exit;
    }

    // Traditional "Add to Cart" logic preserved for compatibility
    if(isset($_GET["id"])){
        $id = intval($_GET["id"]);
        $currentDate = date('d-m-Y');
        
        $sql1 = "SELECT course_id, user_id FROM user_cart_master WHERE course_id = $id AND user_id = $user_id";
        $result1 = mysqli_query($conn, $sql1);
        if(mysqli_num_rows($result1) > 0){
            $_SESSION["educat_error_message"] = "Error: Course already in cart.";
            header("Location: course-overview.php?id=" . $id); exit;
        } else {
            $sql = "SELECT course_price FROM `course_master` WHERE `course_id` = $id";
            $result = mysqli_query($conn,$sql);
            $rowdata = mysqli_fetch_assoc($result);
            $price = $rowdata['course_price'];
    
            $sqlToInsertInCart = "INSERT INTO `user_cart_master`(`course_id`, `user_id`, `course_price`, `date`) VALUES ('$id','$user_id','$price','$currentDate')";
            if(mysqli_query($conn, $sqlToInsertInCart)){
                $_SESSION["educat_success_message"] = "Course added to cart.";
                header("Location: mycart.php"); exit;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart | EduCat (Demo Mode)</title>
    <link rel="shortcut icon" type="image/x-icon" href="./assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-remove-btn {
            color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 10px;
            width: fit-content;
        }
        .cart-remove-btn:hover { background: #ef4444; color: white; }
        
        /* Demo Helper Styles */
        .demo-notice {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: #1E40AF;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <?php include("includes/components/header.php"); ?>

    <div class="cart-header">
        <div class="container">
            <h1 style="margin:0; font-weight:800; font-size:2.5rem; letter-spacing:-1px;">My Shopping Cart</h1>
            <p style="color:var(--text-muted); margin-top:5px;">Review your items and complete enrollment.</p>
        </div>
    </div>

    <div class="container">
        <!-- Demo Helper - Only for this project demonstration -->

        <div class="cart-container">
            <!-- Items List -->
            <div class="cart-items">
                <?php
                    $total_price = 0;
                    $item_count = 0;
                    $sql = "SELECT c.*, u.user_name as instructor_name, cart.cart_id 
                            FROM user_cart_master cart
                            JOIN course_master c ON cart.course_id = c.course_id
                            JOIN user_master u ON c.course_instructor = u.user_id
                            WHERE cart.user_id = $user_id";
                    $result = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($result) > 0){
                        while($row = mysqli_fetch_assoc($result)){
                            $price = floatval($row["course_price"]);
                            $total_price += $price;
                            $item_count++;
                ?>
                    <div class="cart-card">
                        <img src="<?php echo $row['course_image']; ?>" class="cart-card__img" onerror="this.src='assets/img/EduCat (4).png'">
                        <div class="cart-card__info">
                            <h2 class="cart-card__title"><?php echo htmlspecialchars($row['course_name']); ?></h2>
                            <p class="cart-card__instructor">By <strong><?php echo htmlspecialchars($row['instructor_name']); ?></strong></p>
                            <div style="display:flex; align-items:center; gap:5px; color:#F59E0B; font-size:0.85rem; margin-bottom:10px;">
                                <?php for($i=0; $i<5; $i++) echo $i < $row['course_rating'] ? '★' : '☆'; ?>
                                <span style="color:var(--text-muted);">(<?php echo $row['course_rating']; ?>)</span>
                            </div>
                            <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                                <span class="cart-card__price">₹<?php echo number_format($price, 2); ?></span>
                                <a href="includes/scripts/remove_from_cart.php?id=<?php echo $row['cart_id']; ?>" class="cart-remove-btn">
                                    <i class="fas fa-trash-alt"></i> Remove
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } } else { ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h2>Your cart is empty</h2>
                        <p style="color:var(--text-muted); margin-bottom:2rem;">Explore our high-quality courses and start learning today!</p>
                        <a href="index.php" class="buttons" style="display:inline-block; padding:12px 30px;">Browse Courses</a>
                    </div>
                <?php } ?>
            </div>

            <!-- Summary Panel -->
            <?php if($item_count > 0): ?>
            <div class="cart-summary">
                <h3 class="cart-summary__title">Order Summary</h3>
                <div class="cart-summary__row">
                    <span>Subtotal (<?php echo $item_count; ?> items)</span>
                    <span>₹<?php echo number_format($total_price, 2); ?></span>
                </div>
                <div class="cart-summary__row">
                    <span>Processing Fees</span>
                    <span style="color:var(--accent);">FREE</span>
                </div>
                <div class="cart-summary__total">
                    <div style="font-size:0.8rem; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Grand Total:</div>
                    ₹<?php echo number_format($total_price, 2); ?>
                </div>
                
                <!-- Main Checkout Button -->
                <button id="rzp-button1" class="checkout-btn">
                    <i class="fas fa-shield-alt"></i> Secure Payment
                </button>

                
                <div style="margin-top:20px; text-align:center;">
                    <img src="https://i.ibb.co/68v8z9t/Group-162.png" style="width:100%; max-width:200px; opacity:0.6;" alt="Payment Methods">
                    <p style="font-size:0.75rem; color:var(--text-muted); margin-top:10px;">
                        <i class="fas fa-lock"></i> Secured with 256-bit SSL Encryption
                    </p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("includes/components/footer.php"); ?>

    <?php if($item_count > 0): ?>
    <script src="assets/js/payment_sim.js"></script>
    <script>
        document.getElementById('rzp-button1').onclick = function (e) {
            e.preventDefault();
            
            RazorpaySim.init({
                "amount": "<?php echo intval($total_price * 100); ?>",
                "currency": "INR",
                "name": "EduCat",
                "image": "https://i.ibb.co/QK5xyg0/Edu-Cat-4.png",
                "handler": function (response) {
                    window.location.href = "includes/scripts/purchase_cart.php";
                },
                "modal": {
                    "ondismiss": function(){
                        console.log("Payment cancelled");
                    }
                }
            });
            
            RazorpaySim.open();
        }
    </script>
    <?php endif; ?>
</body>
</html>
