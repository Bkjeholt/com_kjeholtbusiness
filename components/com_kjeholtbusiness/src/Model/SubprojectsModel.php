<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class SubprojectsModel extends ListModel
{
    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['id', 'name', 'status', 'start_date', 'project_id']))
            ->from($db->quoteName('#__kjeholtbusiness_subprojects'));

        $projectId = (int) $this->getState('filter.project_id');

        if ($projectId > 0) {
            $query->where($db->quoteName('project_id') . ' = ' . $projectId);
        }

        $query->order($db->quoteName('sequence_id') . ' ASC');

        return $query;
    }
}
