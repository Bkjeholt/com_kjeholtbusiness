-- -------------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.12.3
-- -------------------------------------------------

ALTER TABLE `#__kjeholtbusiness_companies`
  ADD COLUMN `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `iban`;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_company_users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `company_id` INT(11) UNSIGNED NOT NULL,
  `is_primary` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_company_users_user` (`user_id`),
  KEY `idx_company_users_company` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `#__kjeholtbusiness_customers`
  ADD COLUMN `company_id` INT(11) UNSIGNED DEFAULT NULL AFTER `id`;
