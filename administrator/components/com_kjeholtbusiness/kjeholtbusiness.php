
<?php

/*
defined('_JEXEC') or die;

$composerAutoload = JPATH_ADMINISTRATOR . '/components/com_kjeholtbusiness/vendor/autoload.php';
if (file_exists($composerAutoload))
{
    require_once $composerAutoload;
}

$container = \Joomla\CMS\Factory::getContainer();
$container->registerServiceProvider(new KjeholtEngineering\Component\KjeholtBusiness\Administrator\Service\Provider());

/**
 * Entry point for com_kjeholtbusiness (Administrator)
 *
 * @package     Joomla.Administrator
 * @subpackage  com_accounting
 *
 * @copyright   (C) 2025 Kjeholt Engineering
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Extension\Service\Provider\Component;
use Joomla\CMS\Factory;

// Register the namespace for autoloading
\JLoader::registerNamespace(
    'KjeholtEngineering\\Component\\KjeholtBusiness',
    __DIR__ . '/src',
    false,
    false,
    'psr4'
    );

// Get the application
$app = Factory::getApplication();

// Try to get dispatcher through Joomla services
$container = $app->bootComponent('com_kjeholtbusiness');

// Dispatch the request
echo $container->getMVCFactory()
->createController('Display', 'Administrator')
->execute();
