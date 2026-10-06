<?php
defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use KjeholtEngineering\Component\KjeholtBusiness\Administrator\Extension\KjeholtBusinessComponent;

return new class implements ServiceProviderInterface {
    public function register(Container $container): void
    {
        \Joomla\CMS\Log\Log::add('Registrering av com_kjeholtbusiness (site).', \Joomla\CMS\Log\Log::INFO, 'com_kjeholtbusiness');

        $container->registerServiceProvider(new MVCFactory('\\KjeholtEngineering\\Component\\KjeholtBusiness'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\KjeholtEngineering\\Component\\KjeholtBusiness'));
        $container->registerServiceProvider(new RouterFactory('\\KjeholtEngineering\\Component\\KjeholtBusiness'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new KjeholtBusinessComponent(
                    $container->get(ComponentDispatcherFactoryInterface::class)
                );
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));
                $component->setDatabase($container->get(DatabaseInterface::class));

                return $component;
            }
        );
    }
};
