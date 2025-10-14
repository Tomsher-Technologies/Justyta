
-- INSERT INTO `pages` (`id`, `name`, `slug`, `created_at`, `updated_at`) VALUES (NULL, 'Mobile User App Home Page', 'user_app_home', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

-- ALTER TABLE `pages` ADD `content` TEXT NULL DEFAULT NULL AFTER `slug`;
-- UPDATE `dropdowns` SET `name` = 'Immigration Positions', `slug` = 'immigration_positions', `created_at` = NULL, `updated_at` = NULL WHERE `dropdowns`.`id` = 21;

-- INSERT INTO `dropdowns` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Job Positions', 'job_positions', '1','2025-09-13 10:49:32', '2025-09-13 10:49:32');

-- INSERT INTO `dropdowns` (`id`, `name`, `slug`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, 'Training Positions', 'training_positions', '1', '2025-09-13 10:49:32', '2025-09-13 10:49:32');

-- ALTER TABLE `request_legal_translations` ADD `delivery_amount` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `translator_amount`, ADD `tax` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `delivery_amount`;

-- ALTER TABLE `translation_assignment_histories` ADD `delivery_amount` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `translator_amount`, ADD `tax` DECIMAL(10,2) NOT NULL DEFAULT '0' AFTER `delivery_amount`;

-- ALTER TABLE `service_requests` ADD `request_success` TINYINT(1) NOT NULL DEFAULT '0' AFTER `reference_code`;

-- UPDATE `service_requests` SET `request_success`=1 WHERE `payment_status` IS NULL;
-- UPDATE `service_requests` SET `request_success`=1 WHERE `payment_status` != 'pending';
-- UPDATE `service_requests` SET `request_success`=0 WHERE `payment_status` = 'failed'



-- CREATE TABLE `service_request_timelines` (
--   `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
--   `service_request_id` BIGINT UNSIGNED NOT NULL,
--   `status` VARCHAR(40) NOT NULL,
--   `label` VARCHAR(100) NULL,
--   `note` TEXT NULL,
--   `changed_by` BIGINT UNSIGNED NULL,
--   `meta` JSON NULL,
--   `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--   `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

--   PRIMARY KEY (`id`),

--   INDEX `idx_srt_request_id_created_at` (`service_request_id`, `created_at`),
--   INDEX `idx_srt_status_created_at` (`status`, `created_at`),
--   INDEX `idx_srt_changed_by_created_at` (`changed_by`, `created_at`),

--   CONSTRAINT `fk_srt_service_request`
--     FOREIGN KEY (`service_request_id`)
--     REFERENCES `service_requests` (`id`)
--     ON DELETE CASCADE
--     ON UPDATE CASCADE,

--   CONSTRAINT `fk_srt_changed_by_user`
--     FOREIGN KEY (`changed_by`)
--     REFERENCES `users` (`id`)
--     ON DELETE SET NULL
--     ON UPDATE CASCADE

-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ALTER TABLE `service_request_timelines` CHANGE `status` `status` ENUM('pending','under_review','ongoing','completed','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; 

-- alter table service_request_timelines add COLUMN service_slug VARCHAR(255) after service_request_id;

-- ALTER TABLE `service_request_timelines` CHANGE `status` `status` ENUM('pending','under_review','ongoing','completed','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '\'pending\',\'under_review\',\'ongoing\',\'completed\',\'rejected\''; 

-- alter table service_requests add column completed_files text DEFAULT null;