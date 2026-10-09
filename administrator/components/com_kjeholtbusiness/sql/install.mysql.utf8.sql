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
  `acl_view_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_user_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_admin_id` INT(11) UNSIGNED DEFAULT 0,
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
  `description` TEXT,
  `property_name` VARCHAR(255) DEFAULT NULL,
  `start_date` DATE NULL DEFAULT NULL,
  `end_date` DATE NULL DEFAULT NULL,
  `status` ENUM('preliminary','confirmed','ongoing','closing','finalized','cancelled') DEFAULT 'ongoing',
  `customer_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'FK to #__customers',
  `company_id` INT(11) UNSIGNED NOT NULL DEFAULT 1,
  `article_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'FK to #__content',
  `acl_view_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_user_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_admin_id` INT(11) UNSIGNED DEFAULT 0,
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
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `project_id` INT(11) UNSIGNED NOT NULL,
  `start_date` DATE NULL DEFAULT NULL,
  `end_date` DATE NULL DEFAULT NULL,
  `invoice_id` INT(11) UNSIGNED NULL DEFAULT NULL,
  `hourly_rate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `estimated_amount_of_hours` INT NOT NULL DEFAULT 0,
  `status` ENUM('not_started','ongoing','waiting_for_payment','finalized','cancelled') DEFAULT 'ongoing',
  `acl_view_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_user_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_admin_id` INT(11) UNSIGNED DEFAULT 0,
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
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `subproject_id` INT(11) UNSIGNED NOT NULL,
  `date` DATE NOT NULL DEFAULT CURRENT_DATE,
  `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `status` ENUM('new','validated','froozen') DEFAULT 'new',
  `acl_view_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_user_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_admin_id` INT(11) UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_timecards` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `description` TEXT,
  `subproject_id` INT(11) UNSIGNED NOT NULL,
  `start_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end_time` TIMESTAMP NULL DEFAULT NULL,
  `adjustment` INT(11) NOT NULL DEFAULT 0,  -- Adjustment in minutes, can be positive or negative
  `status` ENUM('ongoing','ended','validated','froozen') DEFAULT 'ongoing',
  `acl_view_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_user_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_admin_id` INT(11) UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_invoices` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `invoice_number` VARCHAR(50) NOT NULL DEFAULT '',
  `subproject_id` INT(11) UNSIGNED NOT NULL,
  `date` DATE NOT NULL DEFAULT CURRENT_DATE,
  `due_date` DATE NOT NULL DEFAULT CURRENT_DATE,
  `total_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `rot_reduction` ENUM('yes','no') DEFAULT 'no',
  `rot_percentage` DECIMAL(5,2) NOT NULL DEFAULT 30.00,
  `status` ENUM('draft','sent','paid','overdue') DEFAULT 'draft',
  `acl_view_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_user_id` INT(11) UNSIGNED DEFAULT 0,
  `acl_admin_id` INT(11) UNSIGNED DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `created_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `modified_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modified_by` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  `params` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;
