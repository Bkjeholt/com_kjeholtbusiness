CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `status` enum('preliminary','confirmed','on-going','finalized','cancelled') NOT NULL DEFAULT 'preliminary',
  `customer_id` int NOT NULL,
  `company_id` int NOT NULL,
  `article_id` int NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int NOT NULL DEFAULT 0,
  `modified_by` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_subprojects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sequence_id` int NOT NULL,
  `project_id` int NOT NULL,
  `start_date` date DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `status` enum('not-started','active','ready','waiting-for-payment','closed') NOT NULL DEFAULT 'not-started',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int NOT NULL DEFAULT 0,
  `modified_by` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_project_id` (`project_id`),
  CONSTRAINT `fk_subproject_project` FOREIGN KEY (`project_id`) REFERENCES `#__kjeholtbusiness_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_expencies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `subproject_id` int NOT NULL,
  `date` date DEFAULT NULL,
  `type` enum('hours','costs') NOT NULL DEFAULT 'hours',
  `value` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int NOT NULL DEFAULT 0,
  `modified_by` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_subproject_id` (`subproject_id`),
  CONSTRAINT `fk_expency_subproject` FOREIGN KEY (`subproject_id`) REFERENCES `#__kjeholtbusiness_subprojects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_user_authorities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `company_id` int NOT NULL,
  `authorities` enum('admin','manager','accounting','employee','project_viewer','accounting_viewer') NOT NULL DEFAULT 'project_viewer',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
