<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\MVC\Model\AdminModel;

class ProjectModel extends AdminModel
{
    public function getTable($name = 'Project', $prefix = 'Administrator', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = parent::getForm($data, $loadData);
        return $form;
    }

    protected function loadFormData()
    {
        $data = parent::loadFormData();
        return $data;
    }
}

