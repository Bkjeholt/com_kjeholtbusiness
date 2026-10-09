-- -------------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.10.6
-- -----------------------------------------------
-- Remove VAT breakdown and supplier columns from the expenses table.
ALTER TABLE `#__kjeholtbusiness_expenses`
    DROP COLUMN `amount_excl_vat`,
    DROP COLUMN `vat`,
    DROP COLUMN `apply_rounding`,
    DROP COLUMN `rounding`,
    DROP COLUMN `supplier`;
