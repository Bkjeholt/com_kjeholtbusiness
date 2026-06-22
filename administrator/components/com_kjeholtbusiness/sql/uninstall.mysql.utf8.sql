DROP TABLE IF EXISTS `#__kjeholtbusiness_customers`;
DROP TABLE IF EXISTS `#__kjeholtbusiness_projects`;
DROP TABLE IF EXISTS `#__kjeholtbusiness_subprojects`;
DROP TABLE IF EXISTS `#__kjeholtbusiness_expencies`;
DROP TABLE IF EXISTS `#__kjeholtbusiness_user_authorities`;

DELETE FROM `#__usergroups` WHERE `title` LIKE 'KjeBus: %';
DELETE FROM `#__viewlevels` WHERE `title` LIKE 'KjeBus: %';

