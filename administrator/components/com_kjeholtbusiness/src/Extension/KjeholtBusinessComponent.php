<?php

namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Log\Log;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\Database\DatabaseAwareTrait;

Log::add('Extension file loaded.', Log::DEBUG, 'com_kjeholtbusiness');

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
        Log::add('boot: com_kjeholtbusiness booted. BUILD=aed3abb-guardfix', Log::DEBUG, 'com_kjeholtbusiness');
    }
}
