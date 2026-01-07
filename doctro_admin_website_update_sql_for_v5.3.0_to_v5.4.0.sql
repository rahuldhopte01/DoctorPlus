ALTER TABLE `appointment`
    ADD `is_insured` BOOLEAN NOT NULL DEFAULT FALSE AFTER `zoom_url`, ADD `policy_insurer_name` VARCHAR(100) NULL COMMENT 'Name of the insurer' AFTER `is_insured`, ADD `policy_number` VARCHAR(20) NULL AFTER `policy_insurer_name`;

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'insurer_access', 'web', NULL, NULL);
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'insurer_add', 'web', NULL, NULL);
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'insurer_edit', 'web', NULL, NULL);
INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES (NULL, 'insurer_delete', 'web', NULL, NULL);


CREATE TABLE `insurers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL COMMENT 'Name of the insurer',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'Status of the insurer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `insurers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `insurers_name_unique` (`name`);

ALTER TABLE `insurers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
