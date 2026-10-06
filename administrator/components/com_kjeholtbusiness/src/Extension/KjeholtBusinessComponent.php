<?php

namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Log\Log;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\Database\DatabaseAwareTrait;

class KjeholtBusinessComponent extends MVCComponent implements
    RouterServiceInterface, BootableExtensionInterface
{
    use RouterServiceTrait;
    use DatabaseAwareTrait;

    public function __construct($dispatcherFactory)
    {
        Log::add('KjeholtBusinessComponent: constructor entered.', Log::DEBUG, 'com_kjeholtbusiness');
        parent::__construct($dispatcherFactory);
        Log::add('KjeholtBusinessComponent: parent constructor done.', Log::DEBUG, 'com_kjeholtbusiness');
    }

    public function boot(ContainerInterface $container)
    {
        Log::add('boot: com_kjeholtbusiness booted. BUILD=9d9d666+ctor', Log::DEBUG, 'com_kjeholtbusiness');
    }
}
