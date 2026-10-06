<?php

namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\Database\DatabaseAwareTrait;

class KjeholtBusinessComponent extends MVCComponent implements
    RouterServiceInterface, BootableExtensionInterface
{
    use RouterServiceTrait;
    use DatabaseAwareTrait;

    public function boot(ContainerInterface $container)
    {
    }
}
