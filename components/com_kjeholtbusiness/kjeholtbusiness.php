<?php
defined('_JEXEC') or die;

$container = \Joomla\CMS\Factory::getContainer();
$container->registerServiceProvider(
    require JPATH_ADMINISTRATOR . '/components/com_kjeholtbusiness/services/provider.php'
);
