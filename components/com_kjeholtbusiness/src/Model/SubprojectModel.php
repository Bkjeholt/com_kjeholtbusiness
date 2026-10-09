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
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Database\ParameterType;

class SubprojectModel extends FormModel
{
    private $item;

    public function getItem($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('subproject.id') ?: Factory::getApplication()->input->getInt('id', 0));

        if (empty($pk)) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'a'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $pk, ParameterType::INTEGER);

        $db->setQuery($query);

        $this->item = $db->loadObject();
        $this->setState('subproject.id', $pk);

        return $this->item;
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_kjeholtbusiness.subproject',
            'subproject',
            [
                'control'   => 'jform',
                'load_data' => $loadData,
            ]
        );

        if (empty($form)) {
            throw new \Exception(implode("\n", $this->getErrors()), 500);
        }

        return $form;
    }

    protected function loadFormData()
    {
        return $this->item !== null
            ? (array) $this->item
            : Factory::getApplication()->getUserState('com_kjeholtbusiness.subproject.edit.data', []);
    }

    public function getTimecards($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('subproject.id'));

        if (empty($pk)) {
            return [];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $validated = $db->quoteName('status') . ' IN (' . $db->quote('validated') . ', ' . $db->quote('froozen') . ')';

        $query->select(
            [
                $db->quoteName('id'),
                $db->quoteName('description'),
                $db->quoteName('start_time'),
                $db->quoteName('end_time'),
                $db->quoteName('adjustment'),
                '(TIMESTAMPDIFF(MINUTE, ' . $db->quoteName('start_time') . ', ' . $db->quoteName('end_time') . ') + ' . $db->quoteName('adjustment') . ') / 60 AS ' . $db->quoteName('hours'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_timecards'))
            ->where($db->quoteName('subproject_id') . ' = :subproject_id')
            ->where($validated)
            ->bind(':subproject_id', $pk, ParameterType::INTEGER)
            ->order($db->quoteName('start_time') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    public function getExpenses($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('subproject.id'));

        if (empty($pk)) {
            return [];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $validated = $db->quoteName('status') . ' IN (' . $db->quote('validated') . ', ' . $db->quote('froozen') . ')';

        $query->select(
            [
                $db->quoteName('id'),
                $db->quoteName('name'),
                $db->quoteName('description'),
                $db->quoteName('date'),
                $db->quoteName('amount'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_expenses'))
            ->where($db->quoteName('subproject_id') . ' = :subproject_id')
            ->where($validated)
            ->bind(':subproject_id', $pk, ParameterType::INTEGER)
            ->order($db->quoteName('date') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    public function getTotals($pk = null)
    {
        $item = $this->getItem($pk);

        $totals = (object) [
            'total_hours'     => 0.0,
            'total_costs'     => 0.0,
            'total_time_cost' => 0.0,
            'grand_total'     => 0.0,
        ];

        if (!$item) {
            return $totals;
        }

        foreach ($this->getTimecards() as $timecard) {
            $totals->total_hours += (float) $timecard->hours;
        }

        foreach ($this->getExpenses() as $expense) {
            $totals->total_costs += (float) $expense->amount;
        }

        $hourlyRate          = isset($item->hourly_rate) ? (float) $item->hourly_rate : 0.0;
        $totals->total_hours = round($totals->total_hours, 2);
        $totals->total_costs = round($totals->total_costs, 2);
        $totals->total_time_cost = round($totals->total_hours * $hourlyRate, 2);
        $totals->grand_total = round($totals->total_time_cost + $totals->total_costs, 2);

        return $totals;
    }
}
