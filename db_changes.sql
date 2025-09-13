
-- INSERT INTO `pages` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (NULL, 'Mobile User App Home Page', 'user_app_home', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- ALTER TABLE `pages` ADD `content` TEXT NULL DEFAULT NULL AFTER `slug`;
-- UPDATE `dropdowns` SET `name` = 'Immigration Positions', `slug` = 'immigration_positions', `created_at` = NULL, `updated_at` = NULL WHERE `dropdowns`.`id` = 21;

-- INSERT INTO `dropdowns` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Job Positions', 'job_positions', '1','2025-09-13 10:49:32', '2025-09-13 10:49:32');

-- INSERT INTO `dropdowns` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Training Positions', 'training_positions', '1', '2025-09-13 10:49:32', '2025-09-13 10:49:32');

-- ALTER TABLE `request_legal_translations` ADD `delivery_amount` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `translator_amount`, ADD `tax` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `delivery_amount`;

-- ALTER TABLE `translation_assignment_histories` ADD `delivery_amount` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `translator_amount`, ADD `tax` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `delivery_amount`;

-- ALTER TABLE `service_requests` ADD `request_success` TINYINT(1) NOT NULL DEFAULT '0' AFTER `reference_code`;

UPDATE `service_requests` SET `request_success`=1 WHERE `payment_status` IS NULL;
UPDATE `service_requests` SET `request_success`=1 WHERE `payment_status` != 'pending';
UPDATE `service_requests` SET `request_success`=0 WHERE `payment_status` = 'failed'