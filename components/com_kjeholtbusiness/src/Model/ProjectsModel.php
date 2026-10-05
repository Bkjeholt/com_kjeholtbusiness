<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;

class ProjectsModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['id', 'name', 'status', 'start_date'];
        }

        parent::__construct($config);
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(['id', 'name', 'status', 'start_date']))
            ->from($db->quoteName('#__kjeholtbusiness_projects'));

        $status = $this->getState('filter.status');

        if ($status) {
            $query->where($db->quoteName('status') . ' = ' . $db->quote($status));
        }

        $search = $this->getState('filter.search');

        if ($search) {
            $query->where($db->quoteName('name') . ' LIKE ' . $db->quote('%' . $search . '%'));
        }

        $query->order($db->quoteName('start_date') . ' DESC');

        return $query;
    }
}
