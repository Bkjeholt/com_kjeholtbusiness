-- -------------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.10.5
-- -----------------------------------------------
-- Widen vat column (was DECIMAL(5,2), overflowed above 999.99) and add
-- the optional apply_rounding flag (oresavrundning is per-supplier).
ALTER TABLE `#__kjeholtbusiness_expenses`
    MODIFY COLUMN `vat` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    ADD COLUMN `apply_rounding` TINYINT(1) NOT NULL DEFAULT 0 AFTER `vat`;
