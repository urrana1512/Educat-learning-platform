-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 06:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `educat`
--

-- --------------------------------------------------------

--
-- Table structure for table `certificate_master`
--

CREATE TABLE `certificate_master` (
  `certificate_id` int(200) NOT NULL,
  `certificate_course` int(200) NOT NULL,
  `certificate_student` int(200) NOT NULL,
  `certificate_instructor` int(200) NOT NULL,
  `certificate_score` int(200) NOT NULL,
  `certificate_date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_us`
--

CREATE TABLE `contact_us` (
  `contact_id` int(200) NOT NULL,
  `contact_user_name` varchar(200) NOT NULL,
  `contact_user_email` varchar(200) NOT NULL,
  `contact_user_message` varchar(200) NOT NULL,
  `contact_date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_category_master`
--

CREATE TABLE `course_category_master` (
  `course_category_id` int(200) NOT NULL,
  `course_category_name` varchar(200) NOT NULL,
  `category_description` text DEFAULT NULL,
  `category_status` tinyint(1) DEFAULT 1,
  `course_category_owner` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_category_master`
--

INSERT INTO `course_category_master` (`course_category_id`, `course_category_name`, `category_description`, `category_status`, `course_category_owner`) VALUES
(1, 'Java', NULL, 1, 2),
(2, 'Excel', NULL, 1, 2),
(4, 'JavaScript', NULL, 1, 2),
(5, 'Data Science', NULL, 1, 2),
(7, 'PHP', NULL, 1, 4),
(8, 'C Language', NULL, 1, 4),
(9, 'Python', NULL, 1, 4),
(22, 'Social Media', NULL, 1, 4),
(24, 'Design ', NULL, 1, 4),
(26, 'Programming', NULL, 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `course_chapter_list`
--

CREATE TABLE `course_chapter_list` (
  `course_chapter_id` int(200) NOT NULL,
  `course_chapter_name` longtext NOT NULL,
  `course_chapter_description` varchar(255) NOT NULL,
  `course_id` int(200) NOT NULL,
  `chapter_total_videos` int(200) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_chapter_list`
--

INSERT INTO `course_chapter_list` (`course_chapter_id`, `course_chapter_name`, `course_chapter_description`, `course_id`, `chapter_total_videos`) VALUES
(12, 'Introduction to PHP', 'Understand the basics of PHP and how it works with web servers. Learn how PHP is used to create dynamic websites and server-side applications.\r\n\r\nTopics:\r\n\r\nWhat is PHP\r\nHistory of PHP\r\nInstalling XAMPP / WAMP\r\nPHP file structure\r\nFirst PHP program (Hello', 9, 1),
(13, 'PHP Syntax and Variables', 'Learn the basic syntax of PHP programming and how to store and manipulate data using variables.\r\n\r\nTopics:\r\n\r\nPHP syntax rules\r\nVariables in PHP\r\nData types\r\nEcho and print statements\r\nComments in PHP', 9, 1),
(14, 'PHP Operators', 'Understand how operators work in PHP to perform mathematical, logical, and comparison operations.\r\n\r\nTopics:\r\n\r\nArithmetic operators\r\nAssignment operators\r\nComparison operators\r\nLogical operators\r\nIncrement and decrement operators', 9, 1),
(15, 'Conditional Statements', 'Learn how to control program flow using conditions and decision-making statements.\r\n\r\nTopics:\r\n\r\nif statement\r\nif-else statement\r\nif-elseif-else\r\nswitch statement\r\nNested conditions', 9, 0),
(16, 'Loops in PHP', 'Loops allow you to repeat a block of code multiple times. This section covers all looping techniques in PHP.\r\n\r\nTopics:\r\n\r\nwhile loop\r\ndo-while loop\r\nfor loop\r\nforeach loop\r\nLoop control statements (break, continue)', 9, 0),
(17, 'Introduction to Social Media Marketing', 'Learn the basics of social media marketing and understand how businesses use social platforms to promote their products and services online.', 10, 1),
(18, 'Understanding Major Social Media Platforms', 'Explore popular platforms like Instagram, Facebook, YouTube, and LinkedIn and learn how each platform works for marketing and audience engagement.', 10, 0),
(19, 'Creating a Social Media Strategy', 'Learn how to plan an effective social media strategy including goal setting, target audience identification, and content planning.', 10, 0),
(20, 'Content Creation for Social Media', 'Understand how to create engaging posts, reels, images, and videos that attract users and increase interaction on social media.', 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `course_master`
--

CREATE TABLE `course_master` (
  `course_id` int(200) NOT NULL,
  `course_name` varchar(200) NOT NULL,
  `course_image` varchar(200) NOT NULL DEFAULT 'assets/img/notfound.png',
  `course_instructor` int(200) NOT NULL,
  `course_description` varchar(255) NOT NULL,
  `course_language` int(200) NOT NULL,
  `course_price` int(200) NOT NULL,
  `course_discount` int(255) NOT NULL DEFAULT 0,
  `course_category` varchar(200) NOT NULL,
  `course_chapters` int(200) NOT NULL DEFAULT 0,
  `course_demo_lecture` varchar(255) NOT NULL,
  `course_videos` int(200) NOT NULL,
  `course_approved_by_admin` int(200) NOT NULL,
  `course_status` tinyint(1) DEFAULT 1,
  `course_purchases` int(200) NOT NULL,
  `course_lanuched_on` varchar(200) NOT NULL,
  `course_last_updated_on` varchar(200) NOT NULL,
  `course_completion_number` int(200) NOT NULL,
  `course_rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_master`
--

INSERT INTO `course_master` (`course_id`, `course_name`, `course_image`, `course_instructor`, `course_description`, `course_language`, `course_price`, `course_discount`, `course_category`, `course_chapters`, `course_demo_lecture`, `course_videos`, `course_approved_by_admin`, `course_status`, `course_purchases`, `course_lanuched_on`, `course_last_updated_on`, `course_completion_number`, `course_rating`) VALUES
(8, 'Machine Learning', 'course/uploads/course_featured_image/69b0332322993_ML.jpg', 15, 'Machine learning (ML) is a subset of artificial intelligence focused on building systems that learn and improve from experience without being explicitly programmed. By identifying patterns and correlations in large datasets, these systems can perform task', 3, 1200, 15, '9', 0, 'course/uploads/course_featured_video/69b0332325e05_6637122fbeb0f_educat video.mp4', 0, 1, 1, 0, '10-03-2026', '10-03-2026', 0, 0),
(9, 'PHP', 'course/uploads/course_featured_image/69b06252184bf_php.png', 15, 'PHP is a popular server-side scripting language used to develop dynamic websites and web applications. It is widely used with MySQL databases.', 2, 2199, 10, '7', 5, 'course/uploads/course_featured_video/69b062521c9ae_Vansh Mistry_Chapter 3 video 1_663715d163bea_educat video.mp4', 3, 1, 1, 0, '10-03-2026', '11-03-2026', 0, 0),
(10, 'Social Media Marketing', 'course/uploads/course_featured_image/69b16baa45138_social media marketing.jpg', 15, 'Learn how to promote brands and products using social media platforms. Covers audience targeting, campaign creation, engagement strategies, and performance tracking.', 1, 2499, 20, '22', 4, '', 1, 1, 1, 0, '11-03-2026', '11-03-2026', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `course_rating`
--

CREATE TABLE `course_rating` (
  `id` int(255) NOT NULL,
  `rating_1` int(200) NOT NULL,
  `rating_2` int(200) NOT NULL,
  `rating_3` int(200) NOT NULL,
  `rating_4` int(200) NOT NULL,
  `rating_5` int(200) NOT NULL,
  `number_of_total_people` int(200) NOT NULL,
  `course` int(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_rating`
--

INSERT INTO `course_rating` (`id`, `rating_1`, `rating_2`, `rating_3`, `rating_4`, `rating_5`, `number_of_total_people`, `course`) VALUES
(4, 9, 4, 0, 0, 0, 13, 5);

-- --------------------------------------------------------

--
-- Table structure for table `course_review_master`
--

CREATE TABLE `course_review_master` (
  `review_id` int(200) NOT NULL,
  `reviewer_id` int(200) NOT NULL,
  `course_id` int(200) NOT NULL,
  `review_text` text NOT NULL,
  `review_stars` decimal(3,1) NOT NULL,
  `review_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_review_master`
--

INSERT INTO `course_review_master` (`review_id`, `reviewer_id`, `course_id`, `review_text`, `review_stars`, `review_date`) VALUES
(1, 19, 9, 'nice course', 5.0, '2026-03-11 19:15:13'),
(2, 19, 9, 'nice course', 5.0, '2026-03-11 19:15:21'),
(3, 19, 10, 'Good', 4.0, '2026-03-11 19:30:32');

-- --------------------------------------------------------

--
-- Table structure for table `feedback_master`
--

CREATE TABLE `feedback_master` (
  `feedback_id` int(200) NOT NULL,
  `feedback_by` int(200) NOT NULL,
  `feedback_on` int(200) NOT NULL,
  `feedback_message` varchar(200) NOT NULL,
  `feedback_date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `instructor_create_answers`
--

CREATE TABLE `instructor_create_answers` (
  `id` int(255) NOT NULL,
  `instructor_id` int(255) NOT NULL,
  `answer_one` int(5) NOT NULL,
  `answer_two` int(5) NOT NULL,
  `answer_three` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor_create_answers`
--

INSERT INTO `instructor_create_answers` (`id`, `instructor_id`, `answer_one`, `answer_two`, `answer_three`) VALUES
(1, 15, 4, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `languages_master`
--

CREATE TABLE `languages_master` (
  `language_id` int(200) NOT NULL,
  `language_name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `languages_master`
--

INSERT INTO `languages_master` (`language_id`, `language_name`) VALUES
(1, 'English'),
(2, 'Hindi'),
(3, 'Gujarati'),
(4, 'Sanskrit'),
(5, 'Kannada'),
(6, 'Sindhi'),
(7, 'Bengali'),
(8, 'Bodo'),
(9, 'Dogri'),
(10, 'Kashmiri'),
(11, 'Konkani'),
(12, 'Maithili'),
(13, 'Malayalam'),
(14, 'Manipuri'),
(15, 'Marathi'),
(16, 'Nepali'),
(17, 'Odhia'),
(18, 'Punjabi'),
(19, 'Santali'),
(20, 'Tamil'),
(21, 'Telugu'),
(22, 'Urdu');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_progress`
--

CREATE TABLE `lesson_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lesson_progress`
--

INSERT INTO `lesson_progress` (`id`, `user_id`, `course_id`, `lesson_id`, `is_completed`, `completed_at`) VALUES
(1, 19, 9, 10, 1, '2026-03-11 13:04:49'),
(2, 19, 9, 12, 1, '2026-03-11 13:04:52'),
(3, 19, 9, 11, 1, '2026-03-11 13:04:56'),
(4, 19, 10, 13, 1, '2026-03-11 13:57:42');

-- --------------------------------------------------------

--
-- Table structure for table `lesson_resources`
--

CREATE TABLE `lesson_resources` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages_master`
--

CREATE TABLE `messages_master` (
  `message_id` int(200) NOT NULL,
  `sender_id` int(200) NOT NULL,
  `receiver_id` int(200) NOT NULL,
  `message_text` longtext NOT NULL,
  `course_id` int(200) NOT NULL,
  `created_at` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 19, 'New Reply received', 'Udit Rana replied to your message.', 'msg', 1, '2026-03-11 13:26:00');

-- --------------------------------------------------------

--
-- Table structure for table `notifications_master`
--

CREATE TABLE `notifications_master` (
  `notification_id` int(200) NOT NULL,
  `user_id` int(200) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(200) NOT NULL,
  `is_read` int(10) NOT NULL DEFAULT 0,
  `created_at` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `platform_settings`
--

CREATE TABLE `platform_settings` (
  `id` int(11) NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `site_logo` varchar(255) DEFAULT NULL,
  `admin_email` varchar(255) DEFAULT NULL,
  `facebook_url` varchar(255) DEFAULT NULL,
  `twitter_url` varchar(255) DEFAULT NULL,
  `linkedin_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `platform_settings`
--

INSERT INTO `platform_settings` (`id`, `site_name`, `site_logo`, `admin_email`, `facebook_url`, `twitter_url`, `linkedin_url`) VALUES
(1, 'EduCat', 'assets/img/EduCat (3).png', 'admin@gmail.com', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `purchased_course_master`
--

CREATE TABLE `purchased_course_master` (
  `pcm_id` int(200) NOT NULL,
  `course_id` int(200) NOT NULL,
  `user_id` int(200) NOT NULL,
  `payment_amount` int(255) NOT NULL,
  `original_price` int(255) NOT NULL,
  `discount_given` int(255) NOT NULL,
  `purchase_date` varchar(200) NOT NULL,
  `course_favorite` int(10) NOT NULL DEFAULT 0,
  `course_progress` int(255) NOT NULL DEFAULT 0,
  `transaction_id` varchar(200) NOT NULL,
  `course_completed` int(255) NOT NULL,
  `course_rating` int(255) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchased_course_master`
--

INSERT INTO `purchased_course_master` (`pcm_id`, `course_id`, `user_id`, `payment_amount`, `original_price`, `discount_given`, `purchase_date`, `course_favorite`, `course_progress`, `transaction_id`, `course_completed`, `course_rating`) VALUES
(6, 9, 19, 1979, 2199, 10, '11-03-2026', 0, 100, 'T04T01885HE6QF', 1, 5),
(7, 8, 19, 1020, 1200, 15, '11-03-2026', 0, 0, 'QRK1QY0DBOU3G6', 0, 0),
(8, 10, 19, 1999, 2499, 20, '11-03-2026', 0, 100, 'ETZBSKD2BUF8HO', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_master`
--

CREATE TABLE `quiz_master` (
  `quiz_id` int(200) NOT NULL,
  `quiz_for_course` int(200) NOT NULL,
  `quiz_name` varchar(255) NOT NULL,
  `quiz_total_marks` int(200) NOT NULL,
  `quiz_added_by` int(200) NOT NULL,
  `quiz_total_questions` int(200) NOT NULL,
  `quiz_questions_marked` int(255) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_master`
--

INSERT INTO `quiz_master` (`quiz_id`, `quiz_for_course`, `quiz_name`, `quiz_total_marks`, `quiz_added_by`, `quiz_total_questions`, `quiz_questions_marked`) VALUES
(4, 5, 'JS Test', 2, 4, 10, 0),
(5, 5, 'JS Test', 2, 4, 10, 0),
(6, 5, 'JS Test', 2, 4, 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_question_master`
--

CREATE TABLE `quiz_question_master` (
  `quiz_question_id` int(200) NOT NULL,
  `question` varchar(200) NOT NULL,
  `option_one` varchar(200) NOT NULL,
  `option_two` varchar(200) NOT NULL,
  `option_three` varchar(200) NOT NULL,
  `option_four` varchar(200) NOT NULL,
  `quiz_true_option` int(4) NOT NULL,
  `quiz_id` int(200) NOT NULL,
  `quiz_question_marks` int(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_messages`
--

CREATE TABLE `student_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_messages`
--

INSERT INTO `student_messages` (`id`, `sender_id`, `receiver_id`, `message`, `attachment`, `created_at`, `is_read`) VALUES
(1, 16, 15, 'Heyy Sir, Please Explain in Details..!', NULL, '2026-03-11 12:15:37', 0),
(2, 19, 15, 'Heyy Sir, Please Explain in Details..!', NULL, '2026-03-11 12:47:15', 1),
(3, 15, 19, 'Okay dear, I will do it.', NULL, '2026-03-11 13:26:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_notes`
--

CREATE TABLE `student_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `note_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_notes`
--

INSERT INTO `student_notes` (`id`, `user_id`, `lesson_id`, `note_content`, `created_at`) VALUES
(1, 19, 12, '', '2026-03-11 13:22:30');

-- --------------------------------------------------------

--
-- Table structure for table `user_cart_master`
--

CREATE TABLE `user_cart_master` (
  `id` int(200) NOT NULL,
  `course_id` int(200) NOT NULL,
  `user_id` int(200) NOT NULL,
  `course_price` int(200) NOT NULL,
  `date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_fav_master`
--

CREATE TABLE `user_fav_master` (
  `id` int(255) NOT NULL,
  `course_id` int(255) NOT NULL,
  `user_id` int(255) NOT NULL,
  `date` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_fav_master`
--

INSERT INTO `user_fav_master` (`id`, `course_id`, `user_id`, `date`) VALUES
(4, 5, 4, '05-05-2024'),
(5, 5, 5, '06-05-2024'),
(7, 9, 16, '11-03-2026');

-- --------------------------------------------------------

--
-- Table structure for table `user_master`
--

CREATE TABLE `user_master` (
  `user_id` int(200) NOT NULL,
  `user_name` varchar(200) NOT NULL,
  `user_email` varchar(200) NOT NULL,
  `user_contact_number` bigint(200) NOT NULL,
  `user_password` varchar(200) NOT NULL,
  `user_profile_photo` varchar(200) NOT NULL DEFAULT 'assets/img/EduCat (4).png',
  `role` int(10) NOT NULL,
  `user_status` tinyint(1) DEFAULT 1,
  `isVerified` int(10) NOT NULL,
  `isActive` int(10) NOT NULL,
  `registration_date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`user_id`, `user_name`, `user_email`, `user_contact_number`, `user_password`, `user_profile_photo`, `role`, `user_status`, `isVerified`, `isActive`, `registration_date`) VALUES
(15, 'Udit Rana', 'urrana1512@gmail.com', 7990050104, '$2y$10$9wn1Xs.JUFJjP4MVrJ0baurq9095MdNse9mmE//M7trGdqGoCULH.', 'assets/img/EduCat (4).png', 2, 1, 1, 0, '29-12-2025'),
(18, 'Admin', 'admin@gmail.com', 0, '$2y$10$ZQ2FGZhOEB7QaYqiJadXqe9.7c3vJXRtCDjXiDqz4BzdVD.3.6r9K', 'assets/img/EduCat (4).png', 1, 1, 1, 0, '11-03-2026'),
(19, 'Dhruvi Patel', 'dhruvi1237@gmail.com', 0, '$2y$10$rv/2U3LFfMKAan45aKfUSO9E9fUjnCGe2JuU9McTZTCBovRwCCgLa', 'assets/img/EduCat (4).png', 3, 1, 1, 0, '11-03-2026');

-- --------------------------------------------------------

--
-- Table structure for table `verification`
--

CREATE TABLE `verification` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verification`
--

INSERT INTO `verification` (`id`, `user_email`, `token`, `created_at`, `is_verified`) VALUES
(1, 'vanshmistry2001@gmail.com', '7d118ed3e79d2376a84a1750778b8bcf', '2024-05-03 10:55:58', 1),
(2, 'sivah22334@agafx.com', '4b11507be5e45e46679283473a290379', '2024-05-03 11:30:36', 1),
(4, 'sivah22334@agafx.com', '9d55080871d4b14a11bb620b5be88bff', '2024-05-03 11:32:28', 1),
(5, 'vanshmistry2001@gmail.com', 'd6347c7f884e3a0eb5181d4428f86795', '2024-05-04 11:15:20', 1),
(6, 'vanshmistry2001@gmail.com', '8248', '2024-05-04 11:17:41', 1),
(7, 'vanshmistry2001@gmail.com', '7924', '2024-05-04 11:22:40', 1),
(8, 'pulkit.kapadia08@gmail.com', '701716255365b09b9a00ea18ad38b5ff', '2024-05-06 05:45:34', 0),
(9, 'pulkit.kapadia08@gmail.com', 'de3f7d4b3d7c560552f81b6757605abf', '2024-05-06 05:46:10', 0),
(10, 'patelaryan5636@gmail.com', '0341', '2024-05-06 05:47:36', 0),
(11, 'sachaniaryan675@gmail.com', 'd546d664a5119fbe9666467a256fb1a2', '2024-09-28 12:49:28', 0),
(12, 'sachaniaryan675@gmail.com', '2959b6cd2b93dc22e922c82782b179b0', '2024-10-20 06:37:25', 0),
(13, 'sachaniaryan675@gmail.com', 'e16577e604b84ccb41199a4d63d6ac5f', '2024-10-20 06:40:18', 0),
(14, 'urrana1512@gmail.com', '0e21cc987f7879e28c6c4ca7bf1ce395', '2025-12-26 17:23:33', 0),
(15, 'urrana1512@gmail.com', '2bc3106268b327ffbd308477c39d9620', '2025-12-26 17:23:37', 0),
(16, 'urrana1512@gmail.com', 'cb2ab70e30c9ecd0ad1c86117c20dc9e', '2025-12-26 17:23:58', 0),
(17, 'urrana1512@gmail.com', 'da60820e1e1f3ab55f593f748bad36c7', '2025-12-26 17:26:27', 0),
(18, 'abc@gmail.com', 'f0dca2584b3f3f1f6bc7970f32d1b0d7', '2025-12-29 11:19:16', 0),
(19, 'abc@gmail.com', '294d98b5f9aec8aac1fff56deb715fc4', '2025-12-29 11:20:26', 0),
(20, 'abc@gmail.com', '40a6eea99c1cac157b6a0b4d2288ea25', '2025-12-29 11:21:00', 0),
(21, 'abc@gmail.com', '3c9dd24dc00aeabbb8566c1f4d1ac6d2', '2025-12-29 11:21:04', 0),
(22, 'admin@gmail.com', '65d2f32e0cbecd1910bf660ee0feb633', '2025-12-29 11:56:23', 0),
(23, 'urrana1512@gmail.com', '05ee55c86f47888bcd8d20eba5633266', '2025-12-29 11:57:30', 0),
(24, 'dhruv@gmail.com', '6b88b65ceb699e7ec307601f74991930', '2026-03-10 15:11:36', 0),
(25, 'dhruv@gmail.com', '755871118981efe9bf366cc15219758f', '2026-03-10 15:11:48', 0),
(26, 'dhruv@gmail.com', '332e5cc7972e3d2e5efb122e13a5dda0', '2026-03-10 15:12:45', 0),
(27, 'urrana1512@gmail.com', '2020', '2026-03-10 15:17:34', 0),
(28, 'urrana1512@gmail.com', '8700', '2026-03-10 15:17:53', 0),
(29, 'urrana1512@gmail.com', '0104', '2026-03-10 15:23:37', 0),
(30, 'urrana1512@gmail.com', '6092', '2026-03-10 15:24:47', 0),
(31, 'urrana1512@gmail.com', '9948', '2026-03-10 15:26:21', 0),
(32, 'urrana1512@gmail.com', '883d244181905bb8354c085d3b758b22', '2026-03-10 15:33:29', 0),
(33, 'urrana1512@gmail.com', 'd8a45ce46cb06692506b48a513b8f640', '2026-03-10 15:33:34', 0),
(34, 'urrana1512@gmail.com', '5dba4267161327154dfb95f40c499b01', '2026-03-10 15:33:47', 0),
(35, 'urrana1512@gmail.com', '23d270abb168e13f8a604f8b7dbdc0ce', '2026-03-10 15:33:52', 0),
(36, 'urrana1512@gmail.com', 'b89f2f1857887ecdb8d36901faf80c44', '2026-03-10 15:34:08', 0);

-- --------------------------------------------------------

--
-- Table structure for table `videos_master`
--

CREATE TABLE `videos_master` (
  `video_id` int(200) NOT NULL,
  `video_name` varchar(200) NOT NULL,
  `video_path` varchar(200) NOT NULL,
  `video_plays` int(200) NOT NULL,
  `video_of_chapter` int(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos_master`
--

INSERT INTO `videos_master` (`video_id`, `video_name`, `video_path`, `video_plays`, `video_of_chapter`) VALUES
(10, 'Operators in Php', 'course/uploads/course_videos/69b16c0c38ce1_Operators_in_Php_PHP_Tutorial_9_144P.mp4', 0, 14),
(11, 'Php Variables', 'course/uploads/course_videos/69b16c42dd373_Php_Variables_PHP_Tutorial_5_144P.mp4', 0, 13),
(12, 'Installing XAMPP, VS Code + Environment Setup', 'course/uploads/course_videos/69b16bf6a1eb8_Installing_XAMPP_VS_Code_Environment_Setup_PHP_Tutorial_1_240P.mp4', 0, 12),
(13, 'Introduction to Social Media Marketing', 'course/uploads/course_videos/69b173d71c6f1_Presentation_on_the_Importance_of_social_media_480P.mp4', 0, 17);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `certificate_master`
--
ALTER TABLE `certificate_master`
  ADD PRIMARY KEY (`certificate_id`);

--
-- Indexes for table `contact_us`
--
ALTER TABLE `contact_us`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `course_category_master`
--
ALTER TABLE `course_category_master`
  ADD PRIMARY KEY (`course_category_id`);

--
-- Indexes for table `course_chapter_list`
--
ALTER TABLE `course_chapter_list`
  ADD PRIMARY KEY (`course_chapter_id`);

--
-- Indexes for table `course_master`
--
ALTER TABLE `course_master`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `course_rating`
--
ALTER TABLE `course_rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_review_master`
--
ALTER TABLE `course_review_master`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `feedback_master`
--
ALTER TABLE `feedback_master`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `instructor_create_answers`
--
ALTER TABLE `instructor_create_answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages_master`
--
ALTER TABLE `languages_master`
  ADD PRIMARY KEY (`language_id`);

--
-- Indexes for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lesson_resources`
--
ALTER TABLE `lesson_resources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages_master`
--
ALTER TABLE `messages_master`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications_master`
--
ALTER TABLE `notifications_master`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `platform_settings`
--
ALTER TABLE `platform_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchased_course_master`
--
ALTER TABLE `purchased_course_master`
  ADD PRIMARY KEY (`pcm_id`);

--
-- Indexes for table `quiz_master`
--
ALTER TABLE `quiz_master`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Indexes for table `quiz_question_master`
--
ALTER TABLE `quiz_question_master`
  ADD PRIMARY KEY (`quiz_question_id`);

--
-- Indexes for table `student_messages`
--
ALTER TABLE `student_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_notes`
--
ALTER TABLE `student_notes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_cart_master`
--
ALTER TABLE `user_cart_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_fav_master`
--
ALTER TABLE `user_fav_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_master`
--
ALTER TABLE `user_master`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- Indexes for table `verification`
--
ALTER TABLE `verification`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_token` (`user_email`,`token`);

--
-- Indexes for table `videos_master`
--
ALTER TABLE `videos_master`
  ADD PRIMARY KEY (`video_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `certificate_master`
--
ALTER TABLE `certificate_master`
  MODIFY `certificate_id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_us`
--
ALTER TABLE `contact_us`
  MODIFY `contact_id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_category_master`
--
ALTER TABLE `course_category_master`
  MODIFY `course_category_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `course_chapter_list`
--
ALTER TABLE `course_chapter_list`
  MODIFY `course_chapter_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `course_master`
--
ALTER TABLE `course_master`
  MODIFY `course_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `course_rating`
--
ALTER TABLE `course_rating`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `course_review_master`
--
ALTER TABLE `course_review_master`
  MODIFY `review_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback_master`
--
ALTER TABLE `feedback_master`
  MODIFY `feedback_id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instructor_create_answers`
--
ALTER TABLE `instructor_create_answers`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `languages_master`
--
ALTER TABLE `languages_master`
  MODIFY `language_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lesson_progress`
--
ALTER TABLE `lesson_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lesson_resources`
--
ALTER TABLE `lesson_resources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages_master`
--
ALTER TABLE `messages_master`
  MODIFY `message_id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `notifications_master`
--
ALTER TABLE `notifications_master`
  MODIFY `notification_id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchased_course_master`
--
ALTER TABLE `purchased_course_master`
  MODIFY `pcm_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `quiz_master`
--
ALTER TABLE `quiz_master`
  MODIFY `quiz_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quiz_question_master`
--
ALTER TABLE `quiz_question_master`
  MODIFY `quiz_question_id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_messages`
--
ALTER TABLE `student_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_notes`
--
ALTER TABLE `student_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_cart_master`
--
ALTER TABLE `user_cart_master`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_fav_master`
--
ALTER TABLE `user_fav_master`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `user_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `videos_master`
--
ALTER TABLE `videos_master`
  MODIFY `video_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
