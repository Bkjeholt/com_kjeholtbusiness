<?php
defined('_JEXEC') or die;

use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Extension\KjeholtBusinessComponent;

return new class implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->registerServiceProvider(new MVCFactory('\\KjeholtEngineering\Component\KjeholtBusiness'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\KjeholtEngineering\Component\KjeholtBusiness'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                return $component;
            }
        );
/*        
        $container->set(
            ComponentDispatcherFactoryInterface::class,
            function (Container $container) {
                $component = $container->get(MVCComponent::class);
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                return $component;
            }
        ); */
    }
};