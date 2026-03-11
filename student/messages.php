<?php
include 'header.php';
include 'sidebar.php';

// Fetch instructors the student has courses with (to start conversations)
$instructors_query = "SELECT DISTINCT u.user_id, u.user_name, u.user_profile_photo 
                      FROM purchased_course_master pc 
                      JOIN course_master c ON pc.course_id = c.course_id 
                      JOIN user_master u ON c.course_instructor = u.user_id 
                      WHERE pc.user_id = $user_id";
$instructors_res = mysqli_query($conn, $instructors_query);

// Current active conversation
$active_instructor_id = isset($_GET['instructor_id']) ? $_GET['instructor_id'] : null;

// Fetch messages if an instructor is selected
$messages = [];
if ($active_instructor_id) {
    $msg_query = "SELECT * FROM student_messages 
                  WHERE (sender_id = $user_id AND receiver_id = $active_instructor_id) 
                  OR (sender_id = $active_instructor_id AND receiver_id = $user_id) 
                  ORDER BY created_at ASC";
    $msg_res = mysqli_query($conn, $msg_query);
    while ($m = mysqli_fetch_assoc($msg_res)) {
        $messages[] = $m;
    }
    
    // Get active instructor data
    $active_ins_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM user_master WHERE user_id = $active_instructor_id"));
}

// Handle message submission
if (isset($_POST['send_msg'])) {
    $receiver_id = $_POST['receiver_id'];
    $message_text = mysqli_real_escape_string($conn, $_POST['message']);
    
    mysqli_query($conn, "INSERT INTO student_messages (sender_id, receiver_id, message) VALUES ($user_id, $receiver_id, '$message_text')");
    
    // Notify instructor
    $student_name = $userdata['user_name'];
    $notif_title = "New Message received";
    $notif_msg = "$student_name sent you a new message.";
    mysqli_query($conn, "INSERT INTO notifications (user_id, title, message, type) VALUES ($receiver_id, '$notif_title', '$notif_msg', 'msg')");
    
    header("Location: messages.php?instructor_id=$receiver_id");
    exit();
}
?>

<style>
    .main-content {
        margin-left: 260px;
        padding: 70px 0 0; /* No padding for chat feel */
        height: 100vh;
        background: #f8fafc;
    }

    .chat-container {
        display: flex;
        height: calc(100vh - 70px);
    }

    .chat-sidebar {
        width: 350px;
        background: white;
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
    }

    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f1f5f9;
        position: relative;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .instructor-list {
        flex: 1;
        overflow-y: auto;
    }

    .instructor-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 20px;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: var(--text-main);
        border-bottom: 1px solid #f8fafc;
    }

    .instructor-item:hover {
        background: #f8fafc;
    }

    .instructor-item.active {
        background: #eef2ff;
        border-left: 4px solid var(--primary-color);
    }

    .instructor-item img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }

    .instructor-info h6 {
        margin: 0;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .instructor-info p {
        margin: 0;
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    /* Chat Messages */
    .chat-header {
        padding: 15px 25px;
        background: white;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 15px;
        z-index: 10;
    }

    .messages-area {
        flex: 1;
        padding: 25px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .message-bubble {
        max-width: 70%;
        padding: 12px 18px;
        border-radius: 18px;
        font-size: 0.9rem;
        position: relative;
    }

    .message-bubble.sent {
        align-self: flex-end;
        background: var(--primary-color);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-bubble.received {
        align-self: flex-start;
        background: white;
        color: var(--text-main);
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }

    .message-time {
        font-size: 0.7rem;
        opacity: 0.7;
        margin-top: 5px;
        display: block;
        text-align: right;
    }

    .chat-input-area {
        padding: 20px 25px;
        background: white;
        border-top: 1px solid var(--border-color);
    }

    .chat-form {
        display: flex;
        gap: 15px;
        background: #f1f5f9;
        padding: 8px 15px;
        border-radius: 15px;
    }

    .chat-form input {
        flex: 1;
        background: transparent;
        border: none;
        outline: none;
        padding: 8px;
        font-size: 0.9rem;
    }

    .empty-chat {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--text-muted);
    }
</style>

<main class="main-content">
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h5 class="fw-bold mb-0">Messages</h5>
            </div>
            <div class="instructor-list">
                <?php if (mysqli_num_rows($instructors_res) > 0): ?>
                    <?php while ($ins = mysqli_fetch_assoc($instructors_res)): ?>
                        <a href="?instructor_id=<?php echo $ins['user_id']; ?>" class="instructor-item <?php echo $active_instructor_id == $ins['user_id'] ? 'active' : ''; ?>">
                            <img src="../<?php echo $ins['user_profile_photo']; ?>" alt="Instructor">
                            <div class="instructor-info">
                                <h6><?php echo $ins['user_name']; ?></h6>
                                <p>Instructor</p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted text-center mt-5 p-3">You don't have any instructors yet.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Chat -->
        <div class="chat-main">
            <?php if ($active_instructor_id): ?>
                <div class="chat-header">
                    <img src="../<?php echo $active_ins_data['user_profile_photo']; ?>" alt="Ins" style="width:40px; height:40px; border-radius:50%;">
                    <div>
                        <h6 class="mb-0 fw-bold"><?php echo $active_ins_data['user_name']; ?></h6>
                        <small class="text-success"><i class="fa-solid fa-circle" style="font-size:0.6rem;"></i> Active</small>
                    </div>
                </div>

                <div class="messages-area" id="msgArea">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $m): 
                            $is_sent = ($m['sender_id'] == $user_id);
                        ?>
                            <div class="message-bubble <?php echo $is_sent ? 'sent' : 'received'; ?>">
                                <?php echo $m['message']; ?>
                                <span class="message-time"><?php echo date('H:i', strtotime($m['created_at'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-chat">
                            <i class="fa-regular fa-comments fs-1 mb-3"></i>
                            <p>No messages yet. Start the conversation!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="chat-input-area">
                    <form action="" method="POST" class="chat-form">
                        <input type="hidden" name="receiver_id" value="<?php echo $active_instructor_id; ?>">
                        <button type="button" class="btn text-muted"><i class="fa-solid fa-paperclip"></i></button>
                        <input type="text" name="message" placeholder="Type your message here..." required autocomplete="off">
                        <button type="submit" name="send_msg" class="btn btn-primary px-4 rounded-pill">Send <i class="fa-solid fa-paper-plane ms-1"></i></button>
                    </form>
                </div>
            <?php else: ?>
                <div class="empty-chat">
                    <i class="fa-regular fa-message fs-1 mb-3" style="font-size: 5rem !important;"></i>
                    <h4>Your Inbox</h4>
                    <p>Select an instructor from the left to start chatting.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    // Scroll to bottom of messages
    const msgArea = document.getElementById('msgArea');
    if (msgArea) {
        msgArea.scrollTop = msgArea.scrollHeight;
    }
</script>

<?php include 'footer.php'; ?>
