<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class ExpenciesModel extends ListModel
{
    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['id', 'name', 'type', 'value', 'date', 'subproject_id']))
            ->from($db->quoteName('#__kjeholtbusiness_expencies'));

        $subprojectId = (int) $this->getState('filter.subproject_id');

        if ($subprojectId > 0) {
            $query->where($db->quoteName('subproject_id') . ' = ' . $subprojectId);
        }

        $query->order($db->quoteName('date') . ' DESC');

        return $query;
    }
}
