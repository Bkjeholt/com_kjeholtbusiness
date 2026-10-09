-- -------------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.12.3
-- -------------------------------------------------

ALTER TABLE `#__kjeholtbusiness_companies`
  ADD COLUMN `deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 AFTER `iban`;
