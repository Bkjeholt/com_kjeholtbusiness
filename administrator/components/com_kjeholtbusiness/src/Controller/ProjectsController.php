<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class ProjectsController extends AdminController
{
    protected $view_list = 'projects';

    public function getModel($name = 'Project', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }
}
