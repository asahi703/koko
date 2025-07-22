-- 通知テーブルの作成
CREATE TABLE IF NOT EXISTS `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `from_user_id` int DEFAULT NULL,
  `notification_type` enum('chat_message','group_chat','community_join','community_create','faq_answer','faq_question','class_invite','system') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_id` int DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `from_user_id` (`from_user_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- グループチャット用のテーブルも確認・作成
CREATE TABLE IF NOT EXISTS `group_chats` (
  `group_id` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `group_chat_messages` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `group_id` (`group_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `group_chat_members` (
  `group_id` int NOT NULL,
  `user_id` int NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`, `user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- クラスチャット用のテーブル
CREATE TABLE IF NOT EXISTS `class_chats` (
  `chat_id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`chat_id`),
  KEY `class_id` (`class_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- コミュニティ招待コード用のテーブル
CREATE TABLE IF NOT EXISTS `community_invite_codes` (
  `invite_code` varchar(20) NOT NULL,
  `community_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`invite_code`),
  KEY `community_id` (`community_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 外部キー制約の追加
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_from_user_id` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

ALTER TABLE `group_chat_messages`
  ADD CONSTRAINT `group_chat_messages_group_id` FOREIGN KEY (`group_id`) REFERENCES `group_chats` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_chat_messages_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `group_chat_members`
  ADD CONSTRAINT `group_chat_members_group_id` FOREIGN KEY (`group_id`) REFERENCES `group_chats` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `group_chat_members_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `class_chats`
  ADD CONSTRAINT `class_chats_class_id` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_chats_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

ALTER TABLE `community_invite_codes`
  ADD CONSTRAINT `community_invite_codes_community_id` FOREIGN KEY (`community_id`) REFERENCES `communities` (`community_id`) ON DELETE CASCADE;
