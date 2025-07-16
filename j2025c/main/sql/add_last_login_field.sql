-- usersテーブルに最終ログイン時間フィールドを追加
ALTER TABLE `users` ADD COLUMN `last_login_time` DATETIME DEFAULT NULL;

-- 既存ユーザーに現在時刻を設定（オプション）
UPDATE `users` SET `last_login_time` = NOW() WHERE `last_login_time` IS NULL;
