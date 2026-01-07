ALTER TABLE `appointment` CHANGE COLUMN `appointment_status` `appointment_status` ENUM('pending','approve','complete','cancel') NULL DEFAULT NULL COLLATE 'utf8mb4_unicode_ci' AFTER `payment_token`;
ALTER TABLE `appointment` ADD COLUMN `scheduled_notification_id_patient` varchar(50) DEFAULT NULL AFTER `appointment_status`;
ALTER TABLE `appointment` ADD COLUMN `scheduled_notification_id_doctor` varchar(50) DEFAULT NULL AFTER `appointment_status`;

ALTER TABLE `settings` ADD `zoom_page_content` TEXT NULL DEFAULT NULL AFTER `zoom_redirect_url`;
