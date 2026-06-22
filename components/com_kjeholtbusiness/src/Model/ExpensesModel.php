<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ItemModel;

class ExpensesModel extends ItemModel
{
    public function getTable($type = 'Expency', $prefix = 'Table', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        return parent::getItem($pk);
    }
}

