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
        $this->registerView(new RouterViewConfiguration('dashboard'));
        $this->registerView(new RouterViewConfiguration('kjeholtbusiness'));
        $this->registerView(new RouterViewConfiguration('projects'));
        $this->registerView(new RouterViewConfiguration('projectsummary'));
        $this->registerView(new RouterViewConfiguration('projectshow'));
        $this->registerView(new RouterViewConfiguration('project'));
        $this->registerView(new RouterViewConfiguration('subprojects'));
        $this->registerView(new RouterViewConfiguration('subproject'));
        $this->registerView(new RouterViewConfiguration('timereport'));
        $this->registerView(new RouterViewConfiguration('timereportstart'));
        $this->registerView(new RouterViewConfiguration('timecharts'));
        $this->registerView(new RouterViewConfiguration('timereports'));
        $this->registerView(new RouterViewConfiguration('timereportedit'));
        $this->registerView(new RouterViewConfiguration('logbook'));
        $this->registerView(new RouterViewConfiguration('company'));
        $this->registerView(new RouterViewConfiguration('companies'));
        $this->registerView(new RouterViewConfiguration('companyusers'));
        $this->registerView(new RouterViewConfiguration('invoice'));
        $this->registerView(new RouterViewConfiguration('customer'));
        $this->registerView(new RouterViewConfiguration('customers'));
        $this->registerView(new RouterViewConfiguration('invoices'));
        $this->registerView(new RouterViewConfiguration('expenses'));
        $this->registerView(new RouterViewConfiguration('expense'));

        parent::__construct($application, $menu);
    }
}
