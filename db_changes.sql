
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

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-annual-retainer-agreement', 'View - Companies Retainership Annual Agreement Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-annual-retainer-agreement', 'Export - Companies Retainership Annual Agreement Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-annual-retainer-agreement', 'Change Status - Companies Retainership Annual Agreement Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'sales-annual-retainer-agreement', 'View Sales - Companies Retainership Annual Agreement Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-company-setup', 'View - Company Setup Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-company-setup', 'Export - Company Setup Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-company-setup', 'Change Status - Company Setup Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-contract-drafting', 'View - Contract Drafting Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-contract-drafting', 'Export - Contract Drafting Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-contract-drafting', 'Change Status - Contract Drafting Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-court-case-submission', 'View - Court Case Submission Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-court-case-submission', 'Export - Court Case Submission Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-court-case-submission', 'Change Status - Court Case Submission Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-criminal-complaint', 'View - Criminal Complaint Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-criminal-complaint', 'Export - Criminal Complaint Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-criminal-complaint', 'Change Status - Criminal Complaint Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-debts-collection', 'View - Debts Collection Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-debts-collection', 'Export - Debts Collection Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-debts-collection', 'Change Status - Debts Collection Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-escrow-accounts', 'View - Escrow Accounts Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-escrow-accounts', 'Export - Escrow Accounts Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-escrow-accounts', 'Change Status - Escrow Accounts Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-last-will-and-testament', 'View - Last Will & Testament Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-last-will-and-testament', 'Export - Last Will & Testament Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-last-will-and-testament', 'Change Status - Last Will & Testament Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-memo-writing', 'View - Memo Writing Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-memo-writing', 'Export - Memo Writing Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-memo-writing', 'Change Status - Memo Writing Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-power-of-attorney', 'View - Power Of Attorney Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-power-of-attorney', 'Export - Power Of Attorney Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-power-of-attorney', 'Change Status - Power Of Attorney Requests', 'web', '1', NULL, NULL);

-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-expert-report', 'View - Expert Report Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-expert-report', 'Export - Expert Report Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-expert-report', 'Change Status - Expert Report Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'sales-expert-report', 'View Sales - Expert Report Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-immigration-requests', 'View - Immigration Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-immigration-requests', 'Export - Immigration Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-immigration-requests', 'Change Status - Immigration Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'sales-immigration-requests', 'View Sales - Immigration Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'view-request-submission', 'View - Request Submission Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'export-request-submission', 'Export - Request Submission Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'change-status-request-submission', 'Change Status - Request Submission Requests', 'web', '1', NULL, NULL);
-- INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES (NULL, '59', 'sales-request-submission', 'View Sales - Request Submission Requests', 'web', '1', NULL, NULL);


ALTER TABLE `consultations` ADD `is_extended` TINYINT(1) NOT NULL DEFAULT '0' AFTER `amount`;

INSERT INTO `permissions` (`id`, `parent_id`, `name`, `title`, `guard_name`, `is_active`, `created_at`, `updated_at`) VALUES
(183, 181, 'export_consultation_requests', 'Export Consultation Requests', 'web', 1, NULL, NULL),
(182, 181, 'view_consultation_requests', 'View Consultation Requests', 'web', 1, NULL, NULL),
(181, NULL, 'manage_consultation_requests', 'Manage Consultation Requests', 'web', 1, NULL, NULL);
ALTER TABLE `consultations` ADD FOREIGN KEY (`case_type`) REFERENCES `case_types`(`id`) ON DELETE SET NULL ON UPDATE SET NULL;