-- FAQテーブルの作成
CREATE TABLE `faq` (
  `faq_id` int NOT NULL AUTO_INCREMENT,
  `faq_title` varchar(255) DEFAULT NULL,
  `faq_question` text NOT NULL,
  `faq_answer` text,
  `faq_user_id` int DEFAULT NULL,
  `faq_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `faq_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`faq_id`),
  KEY `faq_user_id` (`faq_user_id`),
  CONSTRAINT `faq_ibfk_1` FOREIGN KEY (`faq_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
