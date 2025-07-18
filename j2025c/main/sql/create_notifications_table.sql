-- 通知テーブルの作成
CREATE TABLE `notifications` (
  `notification_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL COMMENT '通知を受け取るユーザーID',
  `from_user_id` int DEFAULT NULL COMMENT '通知の発生源ユーザーID',
  `notification_type` enum('chat_message','community_join','faq_answer','class_invite','system') NOT NULL COMMENT '通知の種類',
  `title` varchar(255) NOT NULL COMMENT '通知のタイトル',
  `message` text NOT NULL COMMENT '通知の内容',
  `related_id` int DEFAULT NULL COMMENT '関連するID（チャットID、コミュニティIDなど）',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '既読フラグ',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `read_at` datetime DEFAULT NULL COMMENT '既読日時',
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `from_user_id` (`from_user_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- テスト用通知データの挿入
INSERT INTO `notifications` (`user_id`, `from_user_id`, `notification_type`, `title`, `message`, `related_id`, `is_read`) VALUES
(1, 2, 'chat_message', '新しいメッセージ', '田中さんからメッセージが届きました', 1, 0),
(1, 3, 'community_join', 'コミュニティ参加', '佐藤さんがあなたのコミュニティに参加しました', 1, 0),
(1, NULL, 'system', 'システム通知', 'プロフィールを更新してください', NULL, 1),
(1, 2, 'faq_answer', 'FAQ回答', 'あなたの質問に回答がありました', 1, 0),
(1, 4, 'class_invite', 'クラス招待', '数学クラスに招待されました', 1, 1);
