<?php

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory as ComponentDispatcherFactoryServiceProvider;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory as CategoryFactorServiceProvider;
use Joomla\CMS\Extension\Service\Provider\MVCFactory as MVCFactoryServiceProvider;
use Joomla\CMS\Extension\Service\Provider\RouterFactory as RouterFactoryServiceProvider;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use KjeholtEngineering\Component\KjeholtBusiness\Administrator\Extension\KjeholtBusinessComponent;
use KjeholtEngineering\Component\KjeholtBusiness\Administrator\Helper\AuthorityHelper;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Log\Log;


return new class implements ServiceProviderInterface {
    
    public function register(Container $container): void 
    {

        Log::add('Reistrering av com_kjeholtbusiness.', Log::INFO, 'com_kjeholtbusiness');
 
        // Registrera autoloader för Helper-namnutrymmet (om det inte redan görs via composer.json)
/*        $loader = new \Joomla\CMS\Autoloader\Psr4AutoloaderClass;
        $loader->addNamespace('KjeholtEngineering\Component\KjeholtBusiness\Administrator\Helper', 
                              JPATH_ADMINISTRATOR . '/components/com_kjeholtbusiness/src/Helper');
        $loader->register();
  */      
        // Registrera AuthorityHelper som en service i containern
/*        $container->set(
            AuthorityHelper::class,
            function (Container $container) {
                return new AuthorityHelper();
            }
            );
  */      
        /* The line below will call register() in libraries/src/Extension/Service/Provider/CategoryFactory.php
            * That function will create an entry in our component's child DIC with:
            *   key = 'Joomla\CMS\Categories\CategoryFactoryInterface'
            *   value = a function which will 
            *      1. create an instance of CategoryFactory, instantiated with the namespace string passed in
            *      2. call setDatabase() on that CategoryFactory instance, so that it's got access to the database object
            *      3. return the CategoryFactory instance
            */
        
        $container->registerServiceProvider(new CategoryFactorServiceProvider('\\KjeholtEngineering\\Component\\KjeholtBusiness'));
        $container->registerServiceProvider(new MVCFactoryServiceProvider('\\KjeholtEngineering\\Component\\KjeholtBusiness'));
        $container->registerServiceProvider(new ComponentDispatcherFactoryServiceProvider('\\KjeholtEngineering\\Component\\KjeholtBusiness'));
        $container->registerServiceProvider(new RouterFactoryServiceProvider('\\KjeholtEngineering\\Component\\KjeholtBusiness'));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                // The next line creates an instance of our com_example component Extension class
                $component = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));
                $component->setDatabase($container->get(DatabaseInterface::class));

                return $component;
            }
        );
        
        Log::add('Debug: The component is registered.', Log::DEBUG, 'com_kjeholtbusiness');
        
    }
};
