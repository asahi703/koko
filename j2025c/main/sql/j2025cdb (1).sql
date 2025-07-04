-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 2025 年 7 月 04 日 14:03
-- サーバのバージョン： 8.0.41
-- PHP のバージョン: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `j2025cdb`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `administers`
--

CREATE TABLE `administers` (
  `administer_id` int NOT NULL,
  `administer_name` varchar(255) NOT NULL,
  `administer_mailaddress` varchar(255) NOT NULL,
  `administer_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- テーブルのデータのダンプ `administers`
--

INSERT INTO `administers` (`administer_id`, `administer_name`, `administer_mailaddress`, `administer_password`) VALUES
(1, 'admin', 'admin@.com', 'admin');

-- --------------------------------------------------------

--
-- テーブルの構造 `attendances`
--

CREATE TABLE `attendances` (
  `attendance_id` int NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_status` enum('attend','late','absent') NOT NULL DEFAULT 'attend',
  `attendance_class` int NOT NULL,
  `attendance_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `chats`
--

CREATE TABLE `chats` (
  `chat_id` int NOT NULL,
  `chat_text` text NOT NULL,
  `chat_file` longblob,
  `sent_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `from_chat` int DEFAULT NULL,
  `to_chat` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `chat_recipients`
--

CREATE TABLE `chat_recipients` (
  `chat_id` int NOT NULL,
  `user_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `classes`
--

CREATE TABLE `classes` (
  `class_id` int NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `class_community` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `class_users`
--

CREATE TABLE `class_users` (
  `user_id` int NOT NULL,
  `class_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `comments`
--

CREATE TABLE `comments` (
  `comment_id` int NOT NULL,
  `comment_text` text NOT NULL,
  `comment_file` longblob,
  `comment_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `comment_scheduled_at` datetime DEFAULT NULL,
  `comment_is_sent` tinyint(1) NOT NULL DEFAULT '0',
  `comment_class` int NOT NULL,
  `comment_user` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `communities`
--

CREATE TABLE `communities` (
  `community_id` int NOT NULL,
  `community_name` varchar(255) NOT NULL,
  `community_description` varchar(255) NOT NULL,
  `community_owner` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `community_users`
--

CREATE TABLE `community_users` (
  `user_id` int NOT NULL,
  `community_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `events`
--

CREATE TABLE `events` (
  `event_id` int NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_description` varchar(255) DEFAULT NULL,
  `event_start_time` date NOT NULL,
  `event_end_time` date NOT NULL,
  `event_class` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `fruits`
--

CREATE TABLE `fruits` (
  `fruits_id` int NOT NULL,
  `fruits_name` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `fruits`
--

INSERT INTO `fruits` (`fruits_id`, `fruits_name`) VALUES
(1, 'りんご'),
(2, 'みかん'),
(3, 'バナナ'),
(4, 'もも'),
(5, 'メロン');

-- --------------------------------------------------------

--
-- テーブルの構造 `fruits_match`
--

CREATE TABLE `fruits_match` (
  `member_id` int NOT NULL DEFAULT '0',
  `fruits_id` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- テーブルの構造 `member`
--

CREATE TABLE `member` (
  `member_id` int NOT NULL,
  `member_name` varchar(100) NOT NULL DEFAULT '',
  `member_prefecture_id` int NOT NULL DEFAULT '0',
  `member_address` text,
  `member_minor` int NOT NULL DEFAULT '0',
  `par_name` varchar(100) NOT NULL DEFAULT '',
  `par_prefecture_id` int NOT NULL DEFAULT '0',
  `par_address` text,
  `member_comment` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- テーブルの構造 `prefecture`
--

CREATE TABLE `prefecture` (
  `prefecture_id` int NOT NULL,
  `prefecture_name` varchar(20) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- テーブルのデータのダンプ `prefecture`
--

INSERT INTO `prefecture` (`prefecture_id`, `prefecture_name`) VALUES
(1, '北海道'),
(2, '青森県'),
(3, '岩手県'),
(4, '宮城県'),
(5, '秋田県'),
(6, '山形県'),
(7, '福島県'),
(8, '茨城県'),
(9, '栃木県'),
(10, '群馬県'),
(11, '埼玉県'),
(12, '千葉県'),
(13, '東京都'),
(14, '神奈川県'),
(15, '新潟県'),
(16, '富山県'),
(17, '石川県'),
(18, '福井県'),
(19, '山梨県'),
(20, '長野県'),
(21, '岐阜県'),
(22, '静岡県'),
(23, '愛知県'),
(24, '三重県'),
(25, '滋賀県'),
(26, '京都府'),
(27, '大阪府'),
(28, '兵庫県'),
(29, '奈良県'),
(30, '和歌山県'),
(31, '鳥取県'),
(32, '島根県'),
(33, '岡山県'),
(34, '広島県'),
(35, '山口県'),
(36, '徳島県'),
(37, '香川県'),
(38, '愛媛県'),
(39, '高知県'),
(40, '福岡県'),
(41, '佐賀県'),
(42, '長崎県'),
(43, '熊本県'),
(44, '大分県'),
(45, '宮崎県'),
(46, '鹿児島県'),
(47, '沖縄県');

-- --------------------------------------------------------

--
-- テーブルの構造 `questions`
--

CREATE TABLE `questions` (
  `question_id` int NOT NULL,
  `from_user` int DEFAULT NULL,
  `to_user` int DEFAULT NULL,
  `question_title` varchar(255) NOT NULL,
  `question_text` text NOT NULL,
  `anser_text` text,
  `is_anser` tinyint(1) NOT NULL DEFAULT '0',
  `asked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `answered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `temprates`
--

CREATE TABLE `temprates` (
  `temprate_id` int NOT NULL,
  `temprate_title` varchar(255) NOT NULL,
  `temprate_text` text NOT NULL,
  `temprate_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `user_mailaddress` varchar(255) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_icon` blob,
  `user_is_teacher` tinyint(1) NOT NULL DEFAULT '0',
  `user_text` text,
  `user_login` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_mailaddress`, `user_password`, `user_icon`, `user_is_teacher`, `user_text`, `user_login`) VALUES
(1, 'user', 'mail@com', '4d69a6fafd27e155fe2e24c60f0ad9dffd84b5b0', NULL, 0, NULL, 'mail@com'),
(2, 'name', 'm@c', 'ee18e3f6a485f0fd0d4185f050c15be2623a37a0', NULL, 0, NULL, 'm@c'),
(3, 'a', 'a@b.c', 'dd8b23ea0e2d32cbe01542b5ac5ad5285d91a0df', NULL, 0, NULL, 'a@b.c');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `administers`
--
ALTER TABLE `administers`
  ADD PRIMARY KEY (`administer_id`);

--
-- テーブルのインデックス `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `attendance_date` (`attendance_date`,`attendance_class`,`attendance_user`),
  ADD KEY `attendance_class` (`attendance_class`),
  ADD KEY `attendance_user` (`attendance_user`);

--
-- テーブルのインデックス `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`chat_id`),
  ADD KEY `chat_sender` (`from_chat`),
  ADD KEY `to_chat` (`to_chat`);

--
-- テーブルのインデックス `chat_recipients`
--
ALTER TABLE `chat_recipients`
  ADD PRIMARY KEY (`chat_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- テーブルのインデックス `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD KEY `class_community` (`class_community`);

--
-- テーブルのインデックス `class_users`
--
ALTER TABLE `class_users`
  ADD PRIMARY KEY (`user_id`,`class_id`),
  ADD KEY `class_users_ibfk_2` (`class_id`);

--
-- テーブルのインデックス `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `comment_class` (`comment_class`),
  ADD KEY `comment_user` (`comment_user`);

--
-- テーブルのインデックス `communities`
--
ALTER TABLE `communities`
  ADD PRIMARY KEY (`community_id`),
  ADD KEY `community_owner` (`community_owner`);

--
-- テーブルのインデックス `community_users`
--
ALTER TABLE `community_users`
  ADD PRIMARY KEY (`user_id`,`community_id`),
  ADD KEY `community_id` (`community_id`);

--
-- テーブルのインデックス `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `event_class` (`event_class`);

--
-- テーブルのインデックス `fruits`
--
ALTER TABLE `fruits`
  ADD PRIMARY KEY (`fruits_id`);

--
-- テーブルのインデックス `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`member_id`);

--
-- テーブルのインデックス `prefecture`
--
ALTER TABLE `prefecture`
  ADD PRIMARY KEY (`prefecture_id`);

--
-- テーブルのインデックス `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `from_user` (`from_user`),
  ADD KEY `to_user` (`to_user`);

--
-- テーブルのインデックス `temprates`
--
ALTER TABLE `temprates`
  ADD PRIMARY KEY (`temprate_id`),
  ADD KEY `temprate_user` (`temprate_user`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_login` (`user_login`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `administers`
--
ALTER TABLE `administers`
  MODIFY `administer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- テーブルの AUTO_INCREMENT `attendances`
--
ALTER TABLE `attendances`
  MODIFY `attendance_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `chats`
--
ALTER TABLE `chats`
  MODIFY `chat_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `communities`
--
ALTER TABLE `communities`
  MODIFY `community_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `fruits`
--
ALTER TABLE `fruits`
  MODIFY `fruits_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- テーブルの AUTO_INCREMENT `member`
--
ALTER TABLE `member`
  MODIFY `member_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `prefecture`
--
ALTER TABLE `prefecture`
  MODIFY `prefecture_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- テーブルの AUTO_INCREMENT `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `temprates`
--
ALTER TABLE `temprates`
  MODIFY `temprate_id` int NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_ibfk_1` FOREIGN KEY (`attendance_class`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `attendances_ibfk_2` FOREIGN KEY (`attendance_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`from_chat`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`to_chat`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `chat_recipients`
--
ALTER TABLE `chat_recipients`
  ADD CONSTRAINT `chat_recipients_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`chat_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `chat_recipients_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`class_community`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `class_users`
--
ALTER TABLE `class_users`
  ADD CONSTRAINT `class_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `class_users_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`comment_class`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`comment_user`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- テーブルの制約 `communities`
--
ALTER TABLE `communities`
  ADD CONSTRAINT `communities_ibfk_1` FOREIGN KEY (`community_owner`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- テーブルの制約 `community_users`
--
ALTER TABLE `community_users`
  ADD CONSTRAINT `community_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `community_users_ibfk_2` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`event_class`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`from_user`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`to_user`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- テーブルの制約 `temprates`
--
ALTER TABLE `temprates`
  ADD CONSTRAINT `temprates_ibfk_1` FOREIGN KEY (`temprate_user`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
