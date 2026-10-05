<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\AdminModel;

class ProjectsModel extends AdminModel
{
    public function getTable($name = 'Project', $prefix = 'Administrator', $options = [])
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
        $app  = $this->app;
        $data = $app->getUserState('com_kjeholtbusiness.edit.project.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['id', 'name', 'status', 'start_date']))
            ->from($db->quoteName('#__kjeholtbusiness_projects'))
            ->order($db->quoteName('start_date') . ' DESC');

        return $query;
    }
}
