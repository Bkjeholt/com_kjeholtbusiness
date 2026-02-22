<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Controller;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\AdminController;

class ProjectController extends AdminController
{
    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->modelName = 'Project';
    }
}

