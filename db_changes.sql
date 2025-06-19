CREATE TABLE `document_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_type_id` bigint(20) UNSIGNED NOT NULL,
  `lang` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `document_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_type_id` (`document_type_id`);

  ALTER TABLE `document_type_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `document_type_translations`
  ADD CONSTRAINT `document_type_translations_ibfk_1` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE;

  ALTER TABLE `document_types` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

  ALTER TABLE `document_type_translations` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

  CREATE TABLE `free_zones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `emirate_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `free_zones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emirate_id` (`emirate_id`);

  ALTER TABLE `free_zones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `free_zones`
  ADD CONSTRAINT `free_zones_ibfk_1` FOREIGN KEY (`emirate_id`) REFERENCES `emirates` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

  CREATE TABLE `free_zone_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `free_zone_id` bigint(20) UNSIGNED NOT NULL,
  `lang` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `free_zone_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `free_zone_translations_free_zone_id_foreign` (`free_zone_id`);

  ALTER TABLE `free_zone_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `free_zone_translations`
  ADD CONSTRAINT `free_zone_translations_free_zone_id_foreign` FOREIGN KEY (`free_zone_id`) REFERENCES `free_zones` (`id`) ON DELETE CASCADE;


CREATE TABLE `contract_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `contract_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_types_parent_id_foreign` (`parent_id`);

  ALTER TABLE `contract_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `contract_types`
  ADD CONSTRAINT `contract_types_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `contract_types` (`id`) ON DELETE CASCADE;



  CREATE TABLE `contract_type_translations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `contract_type_id` bigint(20) UNSIGNED NOT NULL,
  `lang` varchar(5) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `contract_type_translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_type_id` (`contract_type_id`);

  ALTER TABLE `contract_type_translations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `contract_type_translations`
  ADD CONSTRAINT `contract_type_translations_ibfk_1` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`) ON DELETE CASCADE;

  ALTER TABLE `contract_types` CHANGE `name` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;