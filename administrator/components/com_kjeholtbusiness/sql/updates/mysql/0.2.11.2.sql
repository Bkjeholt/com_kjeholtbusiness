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
