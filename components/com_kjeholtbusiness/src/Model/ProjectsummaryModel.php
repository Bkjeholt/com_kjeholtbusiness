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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

class ProjectsummaryModel extends ItemModel
{
    private $item;

    public function getItem($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('projectsummary.id') ?: Factory::getApplication()->input->getInt('id', 0));

        if (empty($pk)) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__kjeholtbusiness_projects', 'a'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $pk, ParameterType::INTEGER);

        $db->setQuery($query);

        $this->item = $db->loadObject();
        $this->setState('projectsummary.id', $pk);

        return $this->item;
    }

    public function getSubprojects($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('projectsummary.id'));

        if (empty($pk)) {
            return [];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $validatedCards = $db->quoteName('tc.status') . ' IN (' . $db->quote('validated') . ', ' . $db->quote('froozen') . ')';
        $validatedExp   = $db->quoteName('e.status') . ' IN (' . $db->quote('validated') . ', ' . $db->quote('froozen') . ')';

        $query->select(
            [
                $db->quoteName('s.id'),
                $db->quoteName('s.name'),
                $db->quoteName('s.description'),
                $db->quoteName('s.start_date'),
                $db->quoteName('s.hourly_rate'),
                $db->quoteName('s.estimated_amount_of_hours'),
                $db->quoteName('s.status'),
                '(SELECT COALESCE(SUM(TIMESTAMPDIFF(MINUTE, tc.start_time, tc.end_time) + tc.adjustment), 0) / 60'
                    . ' FROM #__kjeholtbusiness_timecards AS tc'
                    . ' WHERE tc.subproject_id = ' . $db->quoteName('s.id')
                    . ' AND ' . $validatedCards . ') AS ' . $db->quoteName('spent_hours'),
                '(SELECT COALESCE(SUM(e.amount), 0)'
                    . ' FROM #__kjeholtbusiness_expenses AS e'
                    . ' WHERE e.subproject_id = ' . $db->quoteName('s.id')
                    . ' AND ' . $validatedExp . ') AS ' . $db->quoteName('spent_costs'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 's'))
            ->where($db->quoteName('s.project_id') . ' = :project_id')
            ->bind(':project_id', $pk, ParameterType::INTEGER)
            ->order($db->quoteName('s.id') . ' ASC');

        $db->setQuery($query);

        $subprojects = $db->loadObjectList() ?: [];

        foreach ($subprojects as $subproject) {
            $subproject->spent_hours = (float) $subproject->spent_hours;
            $subproject->spent_costs = (float) $subproject->spent_costs;
            $subproject->time_cost   = round($subproject->spent_hours * (float) $subproject->hourly_rate, 2);
            $subproject->total_cost  = round($subproject->time_cost + $subproject->spent_costs, 2);
        }

        return $subprojects;
    }

    public function getTotals($pk = null)
    {
        $subprojects = $this->getSubprojects($pk);

        $totals = (object) [
            'total_hours'     => 0.0,
            'total_costs'     => 0.0,
            'total_time_cost' => 0.0,
            'grand_total'     => 0.0,
        ];

        foreach ($subprojects as $subproject) {
            $totals->total_hours     += $subproject->spent_hours;
            $totals->total_costs     += $subproject->spent_costs;
            $totals->total_time_cost += $subproject->time_cost;
        }

        $totals->total_hours     = round($totals->total_hours, 2);
        $totals->total_costs     = round($totals->total_costs, 2);
        $totals->total_time_cost = round($totals->total_time_cost, 2);
        $totals->grand_total     = round($totals->total_time_cost + $totals->total_costs, 2);

        return $totals;
    }
}
