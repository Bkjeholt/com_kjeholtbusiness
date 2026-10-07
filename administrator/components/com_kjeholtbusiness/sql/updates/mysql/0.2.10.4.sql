-- -------------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.10.4
-- -----------------------------------------------
-- Add VAT fields to the expenses table.
-- `amount` becomes the calculated amount incl VAT.
ALTER TABLE `#__kjeholtbusiness_expenses`
    ADD COLUMN `amount_excl_vat` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `subproject_id`,
    ADD COLUMN `vat` DECIMAL(5,2) NOT NULL DEFAULT 25.00 AFTER `amount_excl_vat`,
    ADD COLUMN `rounding` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `vat`,
    MODIFY COLUMN `amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount incl VAT (calculated)';
