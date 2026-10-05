<?php
defined('_JEXEC') or die;

$container = \Joomla\CMS\Factory::getContainer();
$container->registerServiceProvider(
    require __DIR__ . '/services/provider.php'
);
