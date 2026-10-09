-- -------------------------------------------------
-- Joomla! SQL Update File
-- Version 0.2.10
-- -----------------------------------------------
-- Extend the timecards status enum with 'validated' (already present on fresh installs)
ALTER TABLE `#__kjeholtbusiness_timecards`
    MODIFY `status` ENUM('ongoing','ended','validated','froozen') DEFAULT 'ongoing';
