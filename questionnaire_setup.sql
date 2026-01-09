-- =====================================================
-- QUESTIONNAIRE CMS DATABASE SETUP
-- Run this SQL after the base Doctro database is installed
-- =====================================================

-- Table: questionnaires
CREATE TABLE IF NOT EXISTS `questionnaires` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `treatment_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `version` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questionnaires_treatment_id_foreign` (`treatment_id`),
  CONSTRAINT `questionnaires_treatment_id_foreign` 
    FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: questionnaire_sections
CREATE TABLE IF NOT EXISTS `questionnaire_sections` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `questionnaire_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questionnaire_sections_questionnaire_id_foreign` (`questionnaire_id`),
  CONSTRAINT `questionnaire_sections_questionnaire_id_foreign` 
    FOREIGN KEY (`questionnaire_id`) REFERENCES `questionnaires` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: questionnaire_questions
CREATE TABLE IF NOT EXISTS `questionnaire_questions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `question_text` text NOT NULL,
  `field_type` enum('text','textarea','number','dropdown','radio','checkbox','file') NOT NULL DEFAULT 'text',
  `options` json DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `validation_rules` json DEFAULT NULL,
  `conditional_logic` json DEFAULT NULL,
  `flagging_rules` json DEFAULT NULL,
  `doctor_notes` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questionnaire_questions_section_id_foreign` (`section_id`),
  CONSTRAINT `questionnaire_questions_section_id_foreign` 
    FOREIGN KEY (`section_id`) REFERENCES `questionnaire_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: questionnaire_answers
CREATE TABLE IF NOT EXISTS `questionnaire_answers` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `appointment_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `questionnaire_version` int(11) NOT NULL DEFAULT 1,
  `answer_value` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `is_flagged` tinyint(1) NOT NULL DEFAULT 0,
  `flag_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `questionnaire_answers_appointment_id_foreign` (`appointment_id`),
  KEY `questionnaire_answers_question_id_foreign` (`question_id`),
  KEY `questionnaire_answers_appointment_question_idx` (`appointment_id`, `question_id`),
  CONSTRAINT `questionnaire_answers_appointment_id_foreign` 
    FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`id`) ON DELETE CASCADE,
  CONSTRAINT `questionnaire_answers_question_id_foreign` 
    FOREIGN KEY (`question_id`) REFERENCES `questionnaire_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Modify appointments table to add questionnaire fields
ALTER TABLE `appointment` 
  ADD COLUMN IF NOT EXISTS `questionnaire_id` bigint(20) UNSIGNED NULL AFTER `hospital_id`,
  ADD COLUMN IF NOT EXISTS `questionnaire_completed_at` timestamp NULL DEFAULT NULL AFTER `questionnaire_id`,
  ADD COLUMN IF NOT EXISTS `questionnaire_blocked` tinyint(1) NOT NULL DEFAULT 0 AFTER `questionnaire_completed_at`,
  ADD COLUMN IF NOT EXISTS `questionnaire_locked` tinyint(1) NOT NULL DEFAULT 0 AFTER `questionnaire_blocked`;

-- Add foreign key for questionnaire_id (only if it doesn't exist)
SET @fk_exists = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'appointment' 
    AND CONSTRAINT_NAME = 'appointment_questionnaire_id_foreign');

SET @sql = IF(@fk_exists = 0, 
    'ALTER TABLE `appointment` ADD CONSTRAINT `appointment_questionnaire_id_foreign` FOREIGN KEY (`questionnaire_id`) REFERENCES `questionnaires` (`id`) ON DELETE SET NULL',
    'SELECT 1');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add index on questionnaire_id (only if it doesn't exist)
SET @idx_exists = (SELECT COUNT(*) FROM information_schema.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'appointment' 
    AND INDEX_NAME = 'appointment_questionnaire_id_index');

SET @sql = IF(@idx_exists = 0, 
    'ALTER TABLE `appointment` ADD INDEX `appointment_questionnaire_id_index` (`questionnaire_id`)',
    'SELECT 1');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =====================================================
-- ADD QUESTIONNAIRE PERMISSIONS
-- =====================================================

-- Insert questionnaire permissions
INSERT IGNORE INTO `permissions` (`name`, `guard_name`, `created_at`, `updated_at`) VALUES
('questionnaire_access', 'web', NOW(), NOW()),
('questionnaire_add', 'web', NOW(), NOW()),
('questionnaire_edit', 'web', NOW(), NOW()),
('questionnaire_delete', 'web', NOW(), NOW());

-- Assign permissions to super admin role (role_id = 1 typically)
INSERT IGNORE INTO `role_has_permissions` (`permission_id`, `role_id`)
SELECT p.id, r.id 
FROM `permissions` p, `roles` r 
WHERE p.name IN ('questionnaire_access', 'questionnaire_add', 'questionnaire_edit', 'questionnaire_delete')
AND r.name = 'super admin';

-- =====================================================
-- SAMPLE QUESTIONNAIRE DATA (Optional - for testing)
-- =====================================================

-- Uncomment below to insert sample questionnaire data

/*
-- Insert sample questionnaire (assuming treatment_id = 1 exists)
INSERT INTO `questionnaires` (`treatment_id`, `name`, `description`, `status`, `version`, `created_at`, `updated_at`) VALUES
(1, 'General Medical History', 'Please complete this questionnaire before your appointment.', 1, 1, NOW(), NOW());

SET @questionnaire_id = LAST_INSERT_ID();

-- Insert sample section
INSERT INTO `questionnaire_sections` (`questionnaire_id`, `name`, `description`, `order`, `created_at`, `updated_at`) VALUES
(@questionnaire_id, 'Medical History', 'Please provide your medical history information.', 0, NOW(), NOW());

SET @section_id = LAST_INSERT_ID();

-- Insert sample questions
INSERT INTO `questionnaire_questions` (`section_id`, `question_text`, `field_type`, `options`, `required`, `flagging_rules`, `doctor_notes`, `order`, `created_at`, `updated_at`) VALUES
(@section_id, 'Do you have any allergies?', 'radio', '["Yes", "No"]', 1, '{"flag_type": "soft", "conditions": [{"operator": "equals", "value": "Yes", "flag_message": "Patient has allergies - please review"}]}', 'Ask about specific allergens if Yes', 0, NOW(), NOW()),
(@section_id, 'If yes, please list your allergies:', 'textarea', NULL, 0, NULL, NULL, 1, NOW(), NOW()),
(@section_id, 'Are you currently taking any medications?', 'radio', '["Yes", "No"]', 1, NULL, NULL, 2, NOW(), NOW()),
(@section_id, 'Please list your current medications:', 'textarea', NULL, 0, NULL, 'Check for drug interactions', 3, NOW(), NOW()),
(@section_id, 'Do you have a history of heart disease?', 'radio', '["Yes", "No"]', 1, '{"flag_type": "hard", "conditions": [{"operator": "equals", "value": "Yes", "flag_message": "Patient has heart disease history - requires specialist consultation"}]}', 'Critical information for treatment planning', 4, NOW(), NOW());
*/

-- =====================================================
-- END OF QUESTIONNAIRE SETUP
-- =====================================================



