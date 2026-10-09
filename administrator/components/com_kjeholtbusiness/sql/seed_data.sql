INSERT INTO `#__kjeholtbusiness_user_authorities` (`user_id`, `company_id`, `authorities`) VALUES
(42, 1, 'admin'),
(43, 1, 'manager'),
(44, 1, 'accounting'),
(45, 1, 'employee'),
(46, 1, 'project_viewer');

INSERT INTO `#__kjeholtbusiness_projects` (`name`, `description`, `property_name`, `start_date`, `status`, `customer_id`, `company_id`, `article_id`, `created_by`, `modified_by`) VALUES
('Project Alpha', 'Initial project for client Alpha', 'Alpha Property', '2024-01-15', 'on-going', 1, 1, 1, 42, 42),
('Project Beta', 'Initial project for client Beta', 'Beta Property', '2024-02-20', 'confirmed', 2, 1, 2, 42, 42),
('Project Gamma', 'Initial project for client Gamma', 'Gamma Property', '2024-03-10', 'preliminary', 3, 1, 3, 42, 42);

INSERT INTO `#__kjeholtbusiness_subprojects` (`name`, `description`, `sequence_id`, `project_id`, `start_date`, `hourly_rate`, `status`, `created_by`, `modified_by`) VALUES
('Subproject Alpha 1', 'First subproject for Project Alpha', 1, 1, '2024-01-16', 120.00, 'active', 42, 42),
('Subproject Alpha 2', 'Second subproject for Project Alpha', 2, 1, '2024-02-01', 120.00, 'ready', 42, 42),
('Subproject Beta 1', 'First subproject for Project Beta', 1, 2, '2024-02-21', 110.00, 'not-started', 42, 42);

INSERT INTO `#__kjeholtbusiness_expencies` (`name`, `description`, `subproject_id`, `date`, `type`, `value`, `created_by`, `modified_by`) VALUES
('Development Hours', 'Initial development hours for Subproject Alpha 1', 1, '2024-01-16', 'hours', 8.5, 42, 42),
('Material Costs', 'Materials purchased for Subproject Alpha 1', 1, '2024-01-17', 'costs', 250.00, 42, 42),
('Consultation Hours', 'Consultation hours for Subproject Alpha 2', 2, '2024-02-02', 'hours', 5.0, 42, 42);
