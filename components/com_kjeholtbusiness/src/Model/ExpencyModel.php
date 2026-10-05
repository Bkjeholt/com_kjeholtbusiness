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
