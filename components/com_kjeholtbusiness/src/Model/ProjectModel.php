<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;

class ProjectModel extends ItemModel
{
    public function getTable($name = 'Project', $prefix = 'Table', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_kjeholtbusiness.project',
            'project',
            [
                'control'   => 'jform',
                'load_data' => $loadData,
            ]
        );

        if (empty($form)) {
            throw new \Exception(implode("\n", (array) $this->getErrors()), 500);
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_kjeholtbusiness.edit.project.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}
