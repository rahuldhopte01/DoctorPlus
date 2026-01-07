DROP TABLE `zoom_meeting`;

CREATE TABLE `zoom_oauth` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `access_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `refresh_token` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_type` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `zoom_oauth` ADD `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);

-- Foreign Keys
ALTER TABLE `zoom_oauth`
  ADD FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `settings`
ADD COLUMN `zoom_switch` tinyint(1) DEFAULT '0' AFTER `agora_app_certificate`,
ADD COLUMN `zoom_client_id` text DEFAULT NULL AFTER `zoom_switch`,
ADD COLUMN `zoom_client_secret` text DEFAULT NULL AFTER `zoom_client_id`,
ADD COLUMN `zoom_redirect_url` varchar(255) DEFAULT NULL AFTER `zoom_client_secret`;

--   Insert into zoom_oauth table as much as user in users table
INSERT INTO `zoom_oauth` (`user_id`) SELECT `id` FROM `users`;
