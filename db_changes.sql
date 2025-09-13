
INSERT INTO `pages` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (NULL, 'Mobile User App Home Page', 'user_app_home', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

ALTER TABLE `pages` ADD `content` TEXT NULL DEFAULT NULL AFTER `slug`;
UPDATE `dropdowns` SET `name` = 'Immigration Positions', `slug` = 'immigration_positions', `created_at` = NULL, `updated_at` = NULL WHERE `dropdowns`.`id` = 21;

INSERT INTO `dropdowns` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Job Positions', 'job_positions', '1','2025-09-13 10:49:32', '2025-09-13 10:49:32');

INSERT INTO `dropdowns` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Training Positions', 'training_positions', '1', '2025-09-13 10:49:32', '2025-09-13 10:49:32');