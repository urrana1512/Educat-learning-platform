<?php
require 'includes/scripts/connection.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `notifications_master` (
        `notification_id` int(200) NOT NULL AUTO_INCREMENT,
        `user_id` int(200) NOT NULL,
        `message` varchar(255) NOT NULL,
        `type` varchar(200) NOT NULL,
        `is_read` int(10) NOT NULL DEFAULT 0,
        `created_at` varchar(200) NOT NULL,
        PRIMARY KEY (`notification_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    "CREATE TABLE IF NOT EXISTS `messages_master` (
        `message_id` int(200) NOT NULL AUTO_INCREMENT,
        `sender_id` int(200) NOT NULL,
        `receiver_id` int(200) NOT NULL,
        `message_text` longtext NOT NULL,
        `course_id` int(200) NOT NULL,
        `created_at` varchar(200) NOT NULL,
        PRIMARY KEY (`message_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
];

foreach ($queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "Table created successfully\n";
    } else {
        echo "Error creating table: " . mysqli_error($conn) . "\n";
    }
}
?>
