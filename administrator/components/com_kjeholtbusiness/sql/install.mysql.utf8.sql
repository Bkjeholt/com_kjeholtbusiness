DROP TABLE IF EXISTS `#__kjeholtbusiness_projects`;
DROP TABLE IF EXISTS `#__kjeholtbusiness_subprojects`;
DROP TABLE IF EXISTS `#__kjeholtbusiness_expencies`;
DROP TABLE IF EXISTS `#__kjeholtbusiness_user_authorities`;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `property_name` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `status` enum('preliminary','confirmed','on-going','finalized','cancelled') DEFAULT 'preliminary' NOT NULL,
  `customer_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_subprojects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sequence_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `status` enum('not-started','active','ready','waiting-for-payment','closed') DEFAULT 'not-started' NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_expencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `subproject_id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `type` enum('hours','costs') DEFAULT 'hours' NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `#__kjeholtbusiness_user_authorities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `authorities` enum('admin','manager','accounting','employee','project_viewer','accounting_viewer') NOT NULL DEFAULT 'project_viewer',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

