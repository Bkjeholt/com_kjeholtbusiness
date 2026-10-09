-- -----------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.11.2
-- -----------------------------------------------
-- Logbook table (audit trail for important events)
CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_logbook` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `event` VARCHAR(255) NOT NULL DEFAULT '',
  `event_text` TEXT,
  `comment` TEXT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_logbook_event_time` (`event_time`),
  KEY `idx_logbook_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__kjeholtbusiness_companies`
  ADD COLUMN `bank_name` VARCHAR(255) DEFAULT NULL AFTER `website`,
  ADD COLUMN `bankgiro` VARCHAR(50) DEFAULT NULL AFTER `bank_name`,
  ADD COLUMN `iban` VARCHAR(50) DEFAULT NULL AFTER `bankgiro`;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_customers` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `org_number` VARCHAR(50) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `postal_code` VARCHAR(20) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__kjeholtbusiness_invoices`
  ADD COLUMN `customer_id` INT(11) UNSIGNED DEFAULT NULL AFTER `invoice_number`,
  ADD COLUMN `project_id` INT(11) UNSIGNED DEFAULT NULL AFTER `customer_id`,
  ADD COLUMN `rot_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `rot_percentage`,
  ADD COLUMN `rut_reduction` ENUM('yes','no') DEFAULT 'no' AFTER `rot_amount`,
  ADD COLUMN `rut_percentage` DECIMAL(5,2) NOT NULL DEFAULT 50.00 AFTER `rut_reduction`,
  ADD COLUMN `rut_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `rut_percentage`;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_invoice_subprojects` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_id` INT(11) UNSIGNED NOT NULL,
  `subproject_id` INT(11) UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_invsub_invoice` (`invoice_id`),
  KEY `idx_invsub_subproject` (`subproject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
