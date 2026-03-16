<?php

namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\Database\DatabaseAwareTrait;

    
class KjeholtBusinessComponent extends MVCComponent implements 
    CategoryServiceInterface, RouterServiceInterface, BootableExtensionInterface
{
    use CategoryServiceTrait;
    use RouterServiceTrait;
    use DatabaseAwareTrait;
    
    // Use a static variable to store the Categories instance
    public static $categories;

    public function boot(ContainerInterface $container)
    {
        self::$categories = $this->categoryFactory->createCategory();
    }
    
}
