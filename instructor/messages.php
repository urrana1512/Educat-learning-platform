<?php
    require '../includes/scripts/connection.php';
    session_start();
    if(isset($_SESSION['educat_logedin_user_id']) && (trim($_SESSION['educat_logedin_user_id']) !== '')){
        $user_id  = $_SESSION['educat_logedin_user_id'];
        $userdata = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $user_id"));
        if($userdata["role"] != 2) header("Location: ../404.php");
    } else { header("Location: ../sign-in.php"); }

    $active_chat = isset($_GET['userid']) ? intval($_GET['userid']) : 0;
    
    // Filtering
    $search_contact = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
    $filter_course  = isset($_GET['course']) ? intval($_GET['course']) : 0;

    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message']) && $active_chat > 0){
        $msg = mysqli_real_escape_string($conn, $_POST['message_text']);
        if(!empty($msg)){
            mysqli_query($conn, "INSERT INTO student_messages (sender_id, receiver_id, message) VALUES ($user_id, $active_chat, '$msg')");
            
            // Notify student
            $instructor_name = $userdata['user_name'];
            $notif_title = "New Reply received";
            $notif_msg = "$instructor_name replied to your message.";
            mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($active_chat, '$notif_title', '$notif_msg', 'msg')");
            
            header("Location: messages.php?userid=$active_chat"); exit;
        }
    }

    // Get instructor courses for filter
    $courses_res = mysqli_query($conn, "SELECT course_id, course_name FROM course_master WHERE course_instructor = $user_id ORDER BY course_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages — EduCat Instructor</title>
    <link rel="shortcut icon" href="../assets/img/EduCat (4)_rm.png">
    <link rel="stylesheet" href="../dashboard/assets/plugins/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../instructor/assets/css/instructor.css">
    <style>
        .ins-chat-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            height: calc(100vh - var(--header-h) - 40px);
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        
        /* Sidebar */
        .ins-chat-list-panel {
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            background: #fff;
        }
        .ins-chat-list-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-light);
        }
        .ins-chat-search-wrap {
            position: relative;
            margin-top: 12px;
        }
        .ins-chat-search-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.85rem;
        }
        .ins-chat-search-input {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1.5px solid var(--border-light);
            border-radius: var(--radius-sm);
            font-size: 0.825rem;
            outline: none;
            transition: var(--transition);
        }
        .ins-chat-search-input:focus { border-color: var(--primary); }

        .ins-chat-contacts-scroll {
            flex: 1;
            overflow-y: auto;
        }
        .ins-chat-contacts-scroll::-webkit-scrollbar { width: 5px; }
        .ins-chat-contacts-scroll::-webkit-scrollbar-thumb { background: var(--border-light); border-radius: 10px; }

        .contact-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-light);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
        }
        .contact-card:hover { background: var(--bg); }
        .contact-card.active { background: var(--primary-light); border-left: 3px solid var(--primary); }
        .contact-card__avatar { position: relative; flex-shrink: 0; }
        .contact-card__info { min-width: 0; flex: 1; }
        .contact-card__name { font-weight: 700; font-size: 0.9rem; color: var(--text); margin-bottom: 2px; display: flex; justify-content: space-between; }
        .contact-card__time { font-size: 0.7rem; color: var(--text-light); font-weight: 400; }
        .contact-card__sub { font-size: 0.72rem; color: var(--primary); font-weight: 600; margin-bottom: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .contact-card__msg { font-size: 0.78rem; color: var(--text-muted); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        
        .unread-badge {
            background: var(--primary);
            color: white;
            font-size: 0.65rem;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Chat Window */
        .ins-chat-main-panel {
            display: flex;
            flex-direction: column;
            background: #F8FAFC;
        }
        .ins-chat-header {
            padding: 14px 24px;
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .ins-chat-body {
            flex: 1;
            padding: 24px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .ins-chat-body::-webkit-scrollbar { width: 6px; }
        .ins-chat-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }

        .msg-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 14px;
            font-size: 0.9rem;
            line-height: 1.5;
            position: relative;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .msg-bubble--sent {
            align-self: flex-end;
            background: var(--primary);
            color: white;
            border-bottom-right-radius: 2px;
        }
        .msg-bubble--received {
            align-self: flex-start;
            background: white;
            color: var(--text);
            border-bottom-left-radius: 2px;
            border: 1px solid var(--border-light);
        }
        .msg-time {
            font-size: 0.65rem;
            margin-top: 4px;
            opacity: 0.7;
            display: block;
            text-align: right;
        }
        .msg-status { font-size: 0.65rem; margin-left: 4px; }

        .ins-chat-footer {
            padding: 16px 24px;
            background: #fff;
            border-top: 1px solid var(--border);
        }
        .ins-chat-input-box {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--bg);
            padding: 8px 16px;
            border-radius: 30px;
            border: 1.5px solid var(--border-light);
            transition: var(--transition);
        }
        .ins-chat-input-box:focus-within { border-color: var(--primary); background: #fff; box-shadow: var(--shadow-sm); }
        .ins-chat-input-box input {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            font-size: 0.9rem;
            color: var(--text);
            padding: 6px 0;
        }
        .ins-chat-action-btn {
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 1.1rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .ins-chat-action-btn:hover { color: var(--primary); }
        .ins-chat-send-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }
        .ins-chat-send-btn:hover { transform: scale(1.05); background: var(--primary-dark); }

        @media (max-width: 768px) {
            .ins-chat-container { grid-template-columns: 1fr; height: auto; min-height: 600px; }
            .ins-chat-list-panel { <?php echo ($active_chat > 0) ? 'display:none;' : ''; ?> }
            .ins-chat-main-panel { <?php echo ($active_chat == 0) ? 'display:none;' : ''; ?> }
        }
    </style>
</head>
<body class="instructor-body">
<div class="ins-wrapper">
    <?php include("includes/sidebar.php"); ?>
    <div class="ins-main">
        <?php include("includes/header.php"); ?>
        <div class="ins-content">

            <div class="ins-chat-container">
                <!-- Left Sidebar: Conversations -->
                <div class="ins-chat-list-panel">
                    <div class="ins-chat-list-header">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <h4 style="margin:0;font-size:1.1rem;font-weight:700;">Messages</h4>
                            <span class="ins-badge ins-badge-primary">Dynamic</span>
                        </div>
                        
                        <form method="GET" action="">
                            <div class="ins-chat-search-wrap">
                                <i class="fas fa-search"></i>
                                <input type="text" name="search" class="ins-chat-search-input" placeholder="Search students..." value="<?php echo htmlspecialchars($search_contact);?>">
                            </div>
                            <select name="course" class="ins-form-control mt-8" style="font-size:0.75rem;padding:6px 10px;" onchange="this.form.submit()">
                                <option value="0">All Enrolled Students</option>
                                <?php mysqli_data_seek($courses_res, 0); while($cr = mysqli_fetch_assoc($courses_res)): ?>
                                <option value="<?php echo $cr['course_id'];?>" <?php if($filter_course == $cr['course_id']) echo 'selected';?>><?php echo htmlspecialchars($cr['course_name']);?></option>
                                <?php endwhile; ?>
                            </select>
                        </form>
                    </div>

                    <div class="ins-chat-contacts-scroll">
                    <?php
                        // Enhanced contacts query to include course info and unread count
                        $contacts_sql = "SELECT u.user_id, u.user_name, u.user_profile_photo,
                                               (SELECT m2.message FROM student_messages m2 WHERE (m2.sender_id = u.user_id AND m2.receiver_id = $user_id) OR (m2.sender_id = $user_id AND m2.receiver_id = u.user_id) ORDER BY m2.created_at DESC LIMIT 1) as last_msg,
                                               (SELECT m2.created_at FROM student_messages m2 WHERE (m2.sender_id = u.user_id AND m2.receiver_id = $user_id) OR (m2.sender_id = $user_id AND m2.receiver_id = u.user_id) ORDER BY m2.created_at DESC LIMIT 1) as last_time,
                                               (SELECT COUNT(*) FROM student_messages m3 WHERE m3.sender_id = u.user_id AND m3.receiver_id = $user_id AND m3.is_read = 0) as unread_count,
                                               (SELECT c.course_name FROM purchased_course_master pcm JOIN course_master c ON pcm.course_id = c.course_id WHERE pcm.user_id = u.user_id AND c.course_instructor = $user_id LIMIT 1) as course_name
                                         FROM user_master u
                                         WHERE u.user_id IN (
                                             SELECT DISTINCT CASE WHEN sender_id = $user_id THEN receiver_id ELSE sender_id END
                                             FROM student_messages 
                                             WHERE sender_id = $user_id OR receiver_id = $user_id
                                         ) OR u.user_id IN (
                                             SELECT user_id FROM purchased_course_master pcm JOIN course_master c ON pcm.course_id = c.course_id WHERE c.course_instructor = $user_id
                                         )";
                        
                        if(!empty($search_contact)) $contacts_sql .= " AND u.user_name LIKE '%$search_contact%'";
                        if($filter_course > 0) {
                            $contacts_sql = "SELECT u.user_id, u.user_name, u.user_profile_photo,
                                               (SELECT m2.message FROM student_messages m2 WHERE (m2.sender_id = u.user_id AND m2.receiver_id = $user_id) OR (m2.sender_id = $user_id AND m2.receiver_id = u.user_id) ORDER BY m2.created_at DESC LIMIT 1) as last_msg,
                                               (SELECT m2.created_at FROM student_messages m2 WHERE (m2.sender_id = u.user_id AND m2.receiver_id = $user_id) OR (m2.sender_id = $user_id AND m2.receiver_id = u.user_id) ORDER BY m2.created_at DESC LIMIT 1) as last_time,
                                               (SELECT COUNT(*) FROM student_messages m3 WHERE m3.sender_id = u.user_id AND m3.receiver_id = $user_id AND m3.is_read = 0) as unread_count,
                                               c.course_name
                                         FROM purchased_course_master pcm
                                         JOIN user_master u ON pcm.user_id = u.user_id
                                         JOIN course_master c ON pcm.course_id = c.course_id
                                         WHERE c.course_instructor = $user_id AND pcm.course_id = $filter_course";
                            if(!empty($search_contact)) $contacts_sql .= " AND u.user_name LIKE '%$search_contact%'";
                        }
                        
                        $contacts_sql .= " ORDER BY last_time DESC, u.user_name ASC";
                        $contacts_res = mysqli_query($conn, $contacts_sql);

                        if($contacts_res && mysqli_num_rows($contacts_res) > 0){
                            while($c = mysqli_fetch_assoc($contacts_res)):
                                $time_str = $c['last_time'] ? date('H:i', strtotime($c['last_time'])) : '';
                                if($c['last_time'] && date('Y-m-d') != date('Y-m-d', strtotime($c['last_time']))) $time_str = date('M d', strtotime($c['last_time']));
                    ?>
                        <a href="messages.php?userid=<?php echo $c['user_id']; ?>" class="contact-card <?php echo ($c['user_id']==$active_chat)?'active':''; ?>">
                            <div class="contact-card__avatar">
                                <img src="../<?php echo $c['user_profile_photo']; ?>" class="ins-avatar ins-avatar-md" onerror="this.src='../assets/img/EduCat (4).png'">
                                <?php if($c['unread_count'] > 0 && $c['user_id'] != $active_chat): ?>
                                    <div style="position:absolute;top:-2px;right:-2px;" class="unread-badge"><?php echo $c['unread_count'];?></div>
                                <?php endif; ?>
                            </div>
                            <div class="contact-card__info">
                                <div class="contact-card__name">
                                    <span><?php echo htmlspecialchars($c['user_name']); ?></span>
                                    <span class="contact-card__time"><?php echo $time_str;?></span>
                                </div>
                                <div class="contact-card__sub"><?php echo htmlspecialchars($c['course_name'] ?? 'Student');?></div>
                                <div class="contact-card__msg"><?php echo htmlspecialchars($c['last_msg'] ?? 'No messages yet');?></div>
                            </div>
                        </a>
                    <?php endwhile; } else { ?>
                        <div class="ins-empty" style="padding:40px 20px;">
                            <i class="far fa-user-circle"></i>
                            <p style="font-size:0.8rem;">No students found</p>
                        </div>
                    <?php } ?>
                    </div>
                </div>

                <!-- Right Side: Chat Window -->
                <div class="ins-chat-main-panel">
                <?php if($active_chat > 0):
                    $u_s = mysqli_query($conn, "SELECT u.*, (SELECT c.course_name FROM purchased_course_master pcm JOIN course_master c ON pcm.course_id = c.course_id WHERE pcm.user_id = u.user_id AND c.course_instructor = $user_id LIMIT 1) as course_name FROM user_master u WHERE user_id = $active_chat");
                    $chat_user = mysqli_fetch_assoc($u_s);
                    mysqli_query($conn, "UPDATE student_messages SET is_read = 1 WHERE sender_id = $active_chat AND receiver_id = $user_id");
                ?>
                    <!-- Chat Header -->
                    <div class="ins-chat-header">
                        <div style="display:flex;align-items:center;gap:14px;">
                            <a href="messages.php" class="ins-chat-action-btn d-md-none" style="margin-right:8px;"><i class="fas fa-arrow-left"></i></a>
                            <img src="../<?php echo $chat_user['user_profile_photo']; ?>" class="ins-avatar ins-avatar-md" onerror="this.src='../assets/img/EduCat (4).png'">
                            <div>
                                <p style="margin:0;font-weight:700;font-size:1rem;color:var(--text);"><?php echo htmlspecialchars($chat_user['user_name']); ?></p>
                                <p style="margin:0;font-size:0.75rem;color:var(--primary);font-weight:600;"><?php echo htmlspecialchars($chat_user['course_name'] ?? 'Student'); ?></p>
                            </div>
                        </div>
                        <div style="display:flex;gap:15px;">
                            <button class="ins-chat-action-btn" title="Search"><i class="fas fa-search"></i></button>
                            <button class="ins-chat-action-btn" title="More Options"><i class="fas fa-ellipsis-v"></i></button>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="ins-chat-body" id="chatBox">
                        <?php
                        $msg_sql = "SELECT * FROM student_messages WHERE (sender_id=$user_id AND receiver_id=$active_chat) OR (sender_id=$active_chat AND receiver_id=$user_id) ORDER BY created_at ASC";
                        $msg_res = mysqli_query($conn, $msg_sql);
                        if($msg_res && mysqli_num_rows($msg_res) > 0){
                            $last_date = '';
                            while($m = mysqli_fetch_assoc($msg_res)){
                                $sent = $m['sender_id'] == $user_id;
                                $curr_date = date('Y-m-d', strtotime($m['created_at']));
                                if($curr_date != $last_date){
                                    $date_label = (date('Y-m-d') == $curr_date) ? 'Today' : ((date('Y-m-d', strtotime('-1 day')) == $curr_date) ? 'Yesterday' : date('M d, Y', strtotime($m['created_at'])));
                                    echo '<div style="text-align:center;margin:10px 0;"><span style="font-size:0.65rem;font-weight:700;background:rgba(0,0,0,0.05);padding:3px 12px;border-radius:20px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.5px;">'.$date_label.'</span></div>';
                                    $last_date = $curr_date;
                                }
                        ?>
                        <div class="msg-bubble <?php echo $sent ? 'msg-bubble--sent' : 'msg-bubble--received'; ?>">
                            <?php echo htmlspecialchars($m['message']); ?>
                            <span class="msg-time">
                                <?php echo date('H:i', strtotime($m['created_at'])); ?>
                                <?php if($sent): ?>
                                    <i class="fas fa-check-double msg-status" style="<?php echo $m['is_read'] ? 'color:#fff;' : 'opacity:0.5;'; ?>"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                        <?php } } else { ?>
                            <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;color:var(--text-muted);opacity:0.6;">
                                <i class="far fa-hand-wave" style="font-size:2.5rem;margin-bottom:12px;"></i>
                                <p style="font-size:0.9rem;font-weight:600;">Say hello to start the conversation!</p>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Input -->
                    <div class="ins-chat-footer">
                        <form method="POST" action="" style="display:flex;gap:12px;align-items:center;">
                            <div class="ins-chat-input-box">
                                <button type="button" class="ins-chat-action-btn" title="Attach File"><i class="fas fa-paperclip"></i></button>
                                <input type="text" name="message_text" placeholder="Type your message here..." required autocomplete="off">
                                <button type="button" class="ins-chat-action-btn" title="Emoji"><i class="far fa-smile"></i></button>
                            </div>
                            <button type="submit" name="send_message" class="ins-chat-send-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>

                <?php else: ?>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;background:#fff;text-align:center;padding:40px;">
                        <div style="width:120px;height:120px;border-radius:50%;background:var(--primary-light);display:flex;align-items:center;justify-content:center;margin-bottom:24px;">
                            <i class="far fa-comments" style="font-size:3.5rem;color:var(--primary);"></i>
                        </div>
                        <h3 style="font-weight:800;margin-bottom:12px;">Your Messages</h3>
                        <p style="color:var(--text-muted);max-width:300px;line-height:1.6;font-size:0.95rem;">Select a student from the sidebar to view your conversation or start a new chat.</p>
                        <div style="margin-top:32px;display:flex;gap:12px;">
                            <div style="padding:12px 20px;background:var(--bg);border-radius:var(--radius);border:1px solid var(--border-light);">
                                <div style="font-weight:800;font-size:1.2rem;color:var(--primary);">24</div>
                                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;font-weight:700;">Total Students</div>
                            </div>
                            <div style="padding:12px 20px;background:var(--bg);border-radius:var(--radius);border:1px solid var(--border-light);">
                                <div style="font-weight:800;font-size:1.2rem;color:var(--secondary);">12</div>
                                <div style="font-size:0.7rem;color:var(--text-muted);text-transform:uppercase;font-weight:700;">Active Chats</div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </div>

            </div><!-- /ins-chat-container -->

        </div>
    </div>
</div>

<script>
    const chatBox = document.getElementById('chatBox');
    if(chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
        // Animation for bubbles
        const bubbles = chatBox.querySelectorAll('.msg-bubble');
        bubbles.forEach((bubble, index) => {
            bubble.style.opacity = '0';
            bubble.style.transform = 'translateY(10px)';
            setTimeout(() => {
                bubble.style.transition = 'all 0.3s ease';
                bubble.style.opacity = '1';
                bubble.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }
</script>
<?php include("includes/footer.php"); ?>
</body>
</html>
