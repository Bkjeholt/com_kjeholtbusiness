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
                $db->quoteName('s.start_date'),
                $db->quoteName('s.hourly_rate'),
                $db->quoteName('s.estimated_amount_of_hours'),
                $db->quoteName('s.status'),
                'COALESCE(SUM(CASE WHEN ' . $db->quoteName('e.status') . ' IN (' . $db->quote('new') . ',' . $db->quote('validated') . ') THEN ' . $db->quoteName('e.amount') . ' ELSE 0 END), 0) AS ' . $db->quoteName('spent_costs'),
                '(SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE, tc.start_time, COALESCE(tc.end_time, NOW())) + tc.adjustment), 0) / 60 FROM #__kjeholtbusiness_timecards AS tc WHERE tc.subproject_id = ' . $db->quoteName('s.id') . ' AND tc.status IN (' . $db->quote('ended') . ',' . $db->quote('validated') . ')) AS ' . $db->quoteName('spent_hours'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 's'))
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_expenses', 'e'),
                $db->quoteName('e.subproject_id') . ' = ' . $db->quoteName('s.id')
            )
            ->where($db->quoteName('s.project_id') . ' = ' . $pk)
            ->group(
                [
                    $db->quoteName('s.id'),
                    $db->quoteName('s.name'),
                    $db->quoteName('s.description'),
                    $db->quoteName('s.start_date'),
                    $db->quoteName('s.hourly_rate'),
                    $db->quoteName('s.estimated_amount_of_hours'),
                    $db->quoteName('s.status'),
                ]
            )
            ->order($db->quoteName('s.id') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    public function getTotals($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('projectsummary.id'));

        $empty = (object) ['total_hours' => 0, 'total_costs' => 0, 'total_time_cost' => 0];

        if (empty($pk)) {
            return $empty;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                'COALESCE(SUM(CASE WHEN ' . $db->quoteName('c.status') . ' IN (' . $db->quote('ended') . ',' . $db->quote('validated') . ') THEN TIMESTAMPDIFF(MINUTE, ' . $db->quoteName('c.start_time') . ', COALESCE(' . $db->quoteName('c.end_time') . ', NOW())) + ' . $db->quoteName('c.adjustment') . ' ELSE 0 END), 0) AS ' . $db->quoteName('total_minutes'),
                'COALESCE(SUM(CASE WHEN ' . $db->quoteName('e.status') . ' IN (' . $db->quote('new') . ',' . $db->quote('validated') . ') THEN ' . $db->quoteName('e.amount') . ' ELSE 0 END), 0) AS ' . $db->quoteName('total_costs'),
                'COALESCE(SUM(CASE WHEN ' . $db->quoteName('c.status') . ' IN (' . $db->quote('ended') . ',' . $db->quote('validated') . ') THEN ((TIMESTAMPDIFF(MINUTE, ' . $db->quoteName('c.start_time') . ', COALESCE(' . $db->quoteName('c.end_time') . ', NOW())) + ' . $db->quoteName('c.adjustment') . ') * ' . $db->quoteName('s.hourly_rate') . ' / 60) ELSE 0 END), 0) AS ' . $db->quoteName('total_time_cost'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 's'))
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_timecards', 'c'),
                $db->quoteName('c.subproject_id') . ' = ' . $db->quoteName('s.id')
            )
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_expenses', 'e'),
                $db->quoteName('e.subproject_id') . ' = ' . $db->quoteName('s.id')
            )
            ->where($db->quoteName('s.project_id') . ' = ' . $pk);

        $db->setQuery($query);

        $totals = $db->loadObject();

        if (!$totals) {
            return $empty;
        }

        $totals->total_hours = round($totals->total_minutes / 60, 2);

        return $totals;
    }
}
