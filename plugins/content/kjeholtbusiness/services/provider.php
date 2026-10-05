<?php
defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Plugin\PluginFactoryInterface;
use Joomla\Registry\Registry;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use KjeholtEngineering\Plugin\Content\KjeholtBusiness\KjeholtBusiness;

return new class implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->set(
            \Joomla\CMS\Extension\PluginInterface::class,
            function (Container $container) {
                $subject = $container->get(PluginFactoryInterface::class)->getPlugin('content', 'kjeholtbusiness');
                $plugin = new KjeholtBusiness(
                    $subject,
                    [
                        'name'   => (string) ($subject->name ?? 'kjeholtbusiness'),
                        'params' => new Registry((array) ($subject->params ?? [])),
                    ]
                );
                $plugin->setApplication($container->get(CMSApplicationInterface::class));
                return $plugin;
            }
        );
    }
};
