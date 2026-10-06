<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Menu\AbstractMenu;

class Router extends RouterView
{
    public function __construct(CMSApplicationInterface $application, AbstractMenu $menu)
    {
        $this->registerView(new RouterViewConfiguration('display'));
        $this->registerView(new RouterViewConfiguration('kjeholtbusiness'));
        $this->registerView(new RouterViewConfiguration('projects'));
        $this->registerView(new RouterViewConfiguration('project'));
        $this->registerView(new RouterViewConfiguration('subprojects'));
        $this->registerView(new RouterViewConfiguration('subproject'));
        $this->registerView(new RouterViewConfiguration('expancies'));
        $this->registerView(new RouterViewConfiguration('expancy'));
        $this->registerView(new RouterViewConfiguration('timereport'));
        $this->registerView(new RouterViewConfiguration('timereportstart'));

        parent::__construct($application, $menu);
    }
}
