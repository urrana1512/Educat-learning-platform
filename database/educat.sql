-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2024 at 08:47 AM
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
  `course_category_owner` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_category_master`
--

INSERT INTO `course_category_master` (`course_category_id`, `course_category_name`, `course_category_owner`) VALUES
(1, 'Java', 2),
(2, 'Excel', 2),
(4, 'JavaScript', 2),
(5, 'Data Science', 2),
(7, 'PHP', 4),
(8, 'C Language', 4),
(9, 'Python', 4),
(10, 'Physics ', 4),
(12, 'Chemistry', 4),
(22, 'Social Media', 4),
(24, 'Design ', 4),
(26, 'Programming', 4);

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
  `course_purchases` int(200) NOT NULL,
  `course_lanuched_on` varchar(200) NOT NULL,
  `course_last_updated_on` varchar(200) NOT NULL,
  `course_completion_number` int(200) NOT NULL,
  `course_rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_master`
--

INSERT INTO `course_master` (`course_id`, `course_name`, `course_image`, `course_instructor`, `course_description`, `course_language`, `course_price`, `course_discount`, `course_category`, `course_chapters`, `course_demo_lecture`, `course_videos`, `course_approved_by_admin`, `course_purchases`, `course_lanuched_on`, `course_last_updated_on`, `course_completion_number`, `course_rating`) VALUES
(6, 'React', 'course/uploads/course_featured_image/663857a04f335_download.png', 4, 'React can be used to build web, mobile, and desktop applications, making it a versatile framework for cross-platform development. React Native, a framework based on React, is specifically designed for mobile app development, while React Desktop allows you', 2, 999, 10, '4', 0, 'course/uploads/course_featured_video/663857a052014_React JS Explained In 10 Minutes.mp4', 0, 0, 0, '06-05-2024', '06-05-2024', 0, 0),
(7, 'Java', 'course/uploads/course_featured_image/66385882a24d2_download.png', 4, 'Java is used for developing desktop applications, system software, server applications, and software tools. It is platform-independent, robust, and object-oriented, which simplifies the development and maintenance of complex applications.', 1, 199, 10, '1', 0, 'course/uploads/course_featured_video/66385882a7694_Learn Java In 5 Minutes !!.mp4', 0, 0, 0, '06-05-2024', '06-05-2024', 0, 0);

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
(1, 5, 4, 1000, 1000, 0, '05-05-2024', 1, 0, 'VVFYCMKNKLJ34K', 0, 2),
(2, 5, 5, 1000, 1000, 0, '05-05-2024', 1, 0, 'Z27BDJ7X59D6JI', 0, 0),
(3, 7, 5, 179, 199, 10, '06-05-2024', 0, 0, '4EZAM8AY1U0ARD', 0, 0),
(4, 6, 10, 899, 999, 10, '28-09-2024', 0, 0, '3EEL974KJFA60T', 0, 0);

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
(5, 5, 5, '06-05-2024');

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
  `isVerified` int(10) NOT NULL,
  `isActive` int(10) NOT NULL,
  `registration_date` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_master`
--

INSERT INTO `user_master` (`user_id`, `user_name`, `user_email`, `user_contact_number`, `user_password`, `user_profile_photo`, `role`, `isVerified`, `isActive`, `registration_date`) VALUES
(4, 'Vansh Mistry', 'vanshmistry2001@gmail.com', 7096197750, '$2y$10$jLXmv/h5MOHNkloEHigPe.tfe4qu/XAyYb4qqtPu3eV/aqra2uSqa', 'client/profile_photo/66f6e26b38db7_IMG_20230104_190231 (1).jpg', 1, 0, 0, '03-05-2024'),
(5, 'Samarth Jayswal', 'samarth9999@gmail.com', 0, '$2y$10$rUMZfO3LDdnbycRCuEH2se7KHbg480fUEUDHjmEwRwFVHYDNPfuNa', 'assets/img/EduCat (4).png', 3, 1, 0, '03-05-2024'),
(6, 'Aryan Patel', 'aryan1212@gmail.com', 0, '$2y$10$3kifTLJvnd3T1Yv16yOA1eI559wD7/gJuG27HbxErKuXm7FoS/gOe', 'assets/img/EduCat (4).png', 2, 1, 0, '03-05-2024'),
(7, 'aryan patel', 'patelaryan5636@gmail.com', 0, '$2y$10$11yLBQ7cyahMMrBI2coIN.jGR9n9ZHm/.lGgbol/t1T9HtcaXg8MO', 'assets/img/EduCat (4).png', 3, 1, 0, '06-05-2024'),
(8, 'Pulkit Kapadia', 'pulkit.kapadia08@gmail.com', 0, '$2y$10$gIpsx.dRlWSFBkOSFKv/1e5/UZJa9Y85FsCCDXuFBlAiSorN4UrIG', 'assets/img/EduCat (4).png', 3, 1, 0, '06-05-2024'),
(11, 'arayn', 'sachaniaryan675@gmail.com', 0, '$2y$10$e/tLqYXNrs3DsUGdb0LJl.R3g.aH0Nz9Nod3tLbVWcKIXn3aYsr1y', 'assets/img/EduCat (4).png', 3, 1, 0, '20-10-2024');

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
(3, '', '3459ab8467e4e3fd34cf25ccd3584b51', '2024-05-03 11:31:53', 0),
(4, 'sivah22334@agafx.com', '9d55080871d4b14a11bb620b5be88bff', '2024-05-03 11:32:28', 1),
(5, 'vanshmistry2001@gmail.com', 'd6347c7f884e3a0eb5181d4428f86795', '2024-05-04 11:15:20', 1),
(6, 'vanshmistry2001@gmail.com', '8248', '2024-05-04 11:17:41', 1),
(7, 'vanshmistry2001@gmail.com', '7924', '2024-05-04 11:22:40', 1),
(8, 'pulkit.kapadia08@gmail.com', '701716255365b09b9a00ea18ad38b5ff', '2024-05-06 05:45:34', 0),
(9, 'pulkit.kapadia08@gmail.com', 'de3f7d4b3d7c560552f81b6757605abf', '2024-05-06 05:46:10', 0),
(10, 'patelaryan5636@gmail.com', '0341', '2024-05-06 05:47:36', 0),
(11, 'sachaniaryan675@gmail.com', 'd546d664a5119fbe9666467a256fb1a2', '2024-09-28 12:49:28', 0),
(12, 'sachaniaryan675@gmail.com', '2959b6cd2b93dc22e922c82782b179b0', '2024-10-20 06:37:25', 0),
(13, 'sachaniaryan675@gmail.com', 'e16577e604b84ccb41199a4d63d6ac5f', '2024-10-20 06:40:18', 0);

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
  MODIFY `course_chapter_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `course_master`
--
ALTER TABLE `course_master`
  MODIFY `course_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_rating`
--
ALTER TABLE `course_rating`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `feedback_master`
--
ALTER TABLE `feedback_master`
  MODIFY `feedback_id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `instructor_create_answers`
--
ALTER TABLE `instructor_create_answers`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages_master`
--
ALTER TABLE `languages_master`
  MODIFY `language_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `purchased_course_master`
--
ALTER TABLE `purchased_course_master`
  MODIFY `pcm_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `user_cart_master`
--
ALTER TABLE `user_cart_master`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_fav_master`
--
ALTER TABLE `user_fav_master`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_master`
--
ALTER TABLE `user_master`
  MODIFY `user_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `verification`
--
ALTER TABLE `verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `videos_master`
--
ALTER TABLE `videos_master`
  MODIFY `video_id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
