<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ItemModel;

class ProjectsummaryModel extends ItemModel
{
    public function getTable($type = 'Project', $prefix = 'Table', $config = [])
    {
        return parent::getTable($type, $prefix, $config);
    }

    public function getItem($pk = null)
    {
        return parent::getItem($pk);
    }

    public function getSubprojects($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('projectsummary.id'));

        if (empty($pk)) {
            return [];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('s.id'),
                $db->quoteName('s.name'),
                $db->quoteName('s.description'),
                $db->quoteName('s.sequence_id'),
                $db->quoteName('s.start_date'),
                $db->quoteName('s.hourly_rate'),
                $db->quoteName('s.status'),
                'SUM(CASE WHEN ' . $db->quoteName('e.type') . ' = ' . $db->quote('hours') . ' THEN ' . $db->quoteName('e.value') . ' ELSE 0 END) AS ' . $db->quoteName('spent_hours'),
                'SUM(CASE WHEN ' . $db->quoteName('e.type') . ' = ' . $db->quote('costs') . ' THEN ' . $db->quoteName('e.value') . ' ELSE 0 END) AS ' . $db->quoteName('spent_costs'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 's'))
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_expencies', 'e'),
                $db->quoteName('e.subproject_id') . ' = ' . $db->quoteName('s.id')
            )
            ->where($db->quoteName('s.project_id') . ' = ' . $pk)
            ->group($db->quoteName('s.id'))
            ->order($db->quoteName('s.sequence_id') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    public function getTotals($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('projectsummary.id'));

        if (empty($pk)) {
            return (object) ['total_hours' => 0, 'total_costs' => 0, 'total_time_cost' => 0];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                'COALESCE(SUM(CASE WHEN ' . $db->quoteName('e.type') . ' = ' . $db->quote('hours') . ' THEN ' . $db->quoteName('e.value') . ' ELSE 0 END), 0) AS ' . $db->quoteName('total_hours'),
                'COALESCE(SUM(CASE WHEN ' . $db->quoteName('e.type') . ' = ' . $db->quote('costs') . ' THEN ' . $db->quoteName('e.value') . ' ELSE 0 END), 0) AS ' . $db->quoteName('total_costs'),
                'COALESCE(SUM(CASE WHEN ' . $db->quoteName('e.type') . ' = ' . $db->quote('hours') . ' THEN ' . $db->quoteName('e.value') . ' * ' . $db->quoteName('s.hourly_rate') . ' ELSE 0 END), 0) AS ' . $db->quoteName('total_time_cost'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 's'))
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_expencies', 'e'),
                $db->quoteName('e.subproject_id') . ' = ' . $db->quoteName('s.id')
            )
            ->where($db->quoteName('s.project_id') . ' = ' . $pk);

        $db->setQuery($query);

        return $db->loadObject() ?: (object) ['total_hours' => 0, 'total_costs' => 0, 'total_time_cost' => 0];
    }
}
