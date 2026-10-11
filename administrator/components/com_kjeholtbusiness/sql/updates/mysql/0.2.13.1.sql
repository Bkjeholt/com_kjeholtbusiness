ALTER TABLE `#__kjeholtbusiness_companies`
  DROP COLUMN `acl_view_id`,
  DROP COLUMN `acl_user_id`,
  DROP COLUMN `acl_admin_id`,
  ADD UNIQUE KEY `uq_companies_name` (`name`);
