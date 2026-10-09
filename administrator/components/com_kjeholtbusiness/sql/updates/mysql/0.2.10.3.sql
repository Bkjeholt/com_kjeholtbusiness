-- -------------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.10.3
-- -----------------------------------------------
-- Add supplier and status columns to the expenses table
ALTER TABLE `#__kjeholtbusiness_expenses`
    ADD COLUMN `supplier` VARCHAR(255) NOT NULL DEFAULT '' AFTER `amount`,
    ADD COLUMN `status` ENUM('new','validated','froozen') DEFAULT 'new' AFTER `supplier`;
