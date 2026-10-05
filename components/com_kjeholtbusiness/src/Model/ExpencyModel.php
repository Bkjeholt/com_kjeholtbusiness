<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;

class ExpencyModel extends ItemModel
{
    public function getTable($name = 'Expency', $prefix = 'Table', $options = [])
    {
        return parent::getTable($name, $prefix, $options);
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_kjeholtbusiness.expency',
            'expency',
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

    public function save($data)
    {
        $app  = Factory::getApplication();
        $user = $app->getIdentity();
        $db   = $this->getDatabase();

        unset($data['created_by'], $data['modified_by'], $data['created_at'], $data['modified_at']);

        if (empty($data['id'])) {
            $data['created_by'] = (int) $user->id;
        } else {
            $data['modified_by'] = (int) $user->id;
        }

        $table = $this->getTable();

        if (!$table->save($data)) {
            $this->setError($table->getError());
            return false;
        }

        return $table->id;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_kjeholtbusiness.edit.expency.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }
}
