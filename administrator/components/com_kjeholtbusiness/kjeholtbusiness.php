<?php
defined('_JEXEC') or die;

$composerAutoload = JPATH_ADMINISTRATOR . '/components/com_kjeholtbusiness/vendor/autoload.php';
if (file_exists($composerAutoload))
{
    require_once $composerAutoload;
}

$container = \Joomla\CMS\Factory::getContainer();
$container->registerServiceProvider(new KjeholtEngineering\Component\KjeholtBusiness\Administrator\Service\Provider());
