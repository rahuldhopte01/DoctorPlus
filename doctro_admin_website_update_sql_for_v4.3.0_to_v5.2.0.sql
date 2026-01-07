ALTER TABLE `doctor` ADD `patient_vcall` BOOLEAN NOT NULL DEFAULT FALSE AFTER `language`;
ALTER TABLE `settings`  DROP `paypal_sandbox_key`,  DROP `paypal_producation_key`;
ALTER TABLE `purchase_medicine` ADD FOREIGN KEY (`pharmacy_id`) REFERENCES `pharmacy`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `hospital` CHANGE `lat` `lat` TEXT NOT NULL, CHANGE `lng` `lng` TEXT NOT NULL;
ALTER TABLE `offer` CHANGE `use_count` `use_count` INT(11) NULL DEFAULT '0';
ALTER TABLE `settings` ADD `facebook_url` TEXT NULL DEFAULT NULL AFTER `pathologist_commission`, ADD `linkdin_url` TEXT NULL DEFAULT NULL AFTER `facebook_url`, ADD `instagram_url` TEXT NULL DEFAULT NULL AFTER `linkdin_url`, ADD `twitter_url` TEXT NULL DEFAULT NULL AFTER `instagram_url`;
ALTER TABLE `settings` ADD `home_content` VARCHAR(255) NULL DEFAULT NULL AFTER `twitter_url`, ADD `home_content_desc` TEXT NULL DEFAULT NULL AFTER `home_content`;
ALTER TABLE `settings` ADD `lat` INT(10) NULL DEFAULT NULL AFTER `home_content_desc`, ADD `lang` INT(10) NULL DEFAULT NULL AFTER `lat`;
ALTER TABLE `settings` CHANGE `lat` `lat` BIGINT(20) NULL DEFAULT NULL, CHANGE `lang` `lang` BIGINT(20) NULL DEFAULT NULL;
ALTER TABLE `settings` CHANGE `lat` `lat` VARCHAR(255) NULL DEFAULT NULL, CHANGE `lang` `lang` VARCHAR(255) NULL DEFAULT NULL;
UPDATE `settings` SET `lat` = '40.7127281', `lang` = '-74.0060152' WHERE `settings`.`id` = 1;
UPDATE `settings` SET `home_content` = 'Browse by Specialities', `home_content_desc` = '<p><span style=\"color: rgb(102, 102, 102); font-family: Inter, sans-serif; text-transform: none; background-color: rgb(244, 251, 253);\">Lorem ipsum dolor sit amet, elit. Euismod habitasse pulvinar faucibus eget.Lorem ipsum dolor sit amet, elit. Euismod habitasse pulvinar faucibus eget.</span><br></p>' WHERE `settings`.`id` = 1;
ALTER TABLE `banner` ADD `name` VARCHAR(255) NOT NULL AFTER `id`;

-- Update v5.2.0 -- DB Modification Queries
ALTER TABLE `settings` ADD `landing_popup_image` VARCHAR(255) NOT NULL AFTER `banner_url`, ADD `popup_timer_seconds` INT(11) NOT NULL AFTER `landing_popup_image`;
ALTER TABLE `settings` CHANGE `popup_timer_seconds` `popup_timer_seconds` INT(11) NOT NULL DEFAULT '5';
ALTER TABLE `settings` ADD `popup_target_url` TEXT NOT NULL AFTER `popup_timer_seconds`;
ALTER TABLE `settings` ADD `landing_popup_switch` TINYINT(1) NOT NULL DEFAULT '0' AFTER `popup_target_url`;
