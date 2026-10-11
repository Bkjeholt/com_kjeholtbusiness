ALTER TABLE `#__kjeholtbusiness_company_users`
  ADD COLUMN `permissions` SET('admin','user-edit','project-edit','project-view','subproject-edit','timereport-edit','timereport-view','expense-edit','expense-view','invoice-approve','invoice-edit','customer-edit','customer-view','logbook-view') NOT NULL DEFAULT '' AFTER `is_primary`;
