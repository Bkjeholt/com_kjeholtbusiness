-- Companies table
CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_companies` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `org_number` VARCHAR(50) DEFAULT NULL,
  `address` VARCHAR(255) DEFAULT NULL,
  `postal_code` VARCHAR(20) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `country` VARCHAR(100) DEFAULT 'Sweden',
  `phone` VARCHAR(50) DEFAULT NULL,
  `email` VARCHAR(255) DEFAULT NULL,
  `website` VARCHAR(255) DEFAULT NULL,
  `description` TEXT,
  `checked_out` INT(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Projects table
CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_projects` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `alias` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` TEXT,
  `property_name` VARCHAR(255) DEFAULT NULL,
  `start_date` DATE NULL DEFAULT NULL,
  `end_date` DATE NULL DEFAULT NULL,
  `status` ENUM('preliminary','confirmed','ongoing','closing','finalized','cancelled') DEFAULT 'preliminary',
  `customer_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'FK to #__customers',
  `company_id` INT(11) UNSIGNED NOT NULL DEFAULT 1,
  `article_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'FK to #__content',
  `checked_out` INT(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Subprojects table
CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_subprojects` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__assets',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `alias` VARCHAR(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `description` TEXT,
  `project_id` INT(11) UNSIGNED NOT NULL,
  `start_date` DATE NULL DEFAULT NULL,
  `invoice_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `hourly_rate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('not_started','ongoing','waiting_for_payment','finalized','cancelled') DEFAULT 'not_started',
  `checked_out` INT(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

-- Expenses table (combining both time entries and costs)
CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_expenses` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to #__assets',
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `subproject_id` INT(11) UNSIGNED NOT NULL,
  `date` DATE NOT NULL DEFAULT CURRENT_DATE,
  `type` ENUM('hours','costs') DEFAULT 'hours',
  `value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `checked_out` INT(11) UNSIGNED DEFAULT NULL,
  `checked_out_time` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
