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

    public function save(array $data): bool
    {
        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;
        $id     = (int) ($data['id'] ?? 0);

        $row = new \stdClass();
        $row->name        = (string) ($data['name'] ?? '');
        $row->description = (string) ($data['description'] ?? '');
        $row->hourly_rate = (float) ($data['hourly_rate'] ?? 0);
        $row->estimated_amount_of_hours = (int) ($data['estimated_amount_of_hours'] ?? 0);
        $row->rot = (int) ($data['rot'] ?? 0) ? 1 : 0;
        $row->rut = (int) ($data['rut'] ?? 0) ? 1 : 0;
        $row->vat = (float) ($data['vat'] ?? 25);
        $row->status     = (string) ($data['status'] ?? 'not_started');
        $row->project_id = (int) ($data['project_id'] ?? 0);
        $row->modified_by = $userId;

        // Start date: own, else the parent project's, else today
        $startDate = trim((string) ($data['start_date'] ?? ''));

        if ($startDate === '' && $row->project_id) {
            $query = $db->getQuery(true)
                ->select($db->quoteName('start_date'))
                ->from($db->quoteName('#__kjeholtbusiness_projects'))
                ->where($db->quoteName('id') . ' = :pid')
                ->bind(':pid', $row->project_id, ParameterType::INTEGER);
            $startDate = (string) ($db->setQuery($query)->loadResult() ?? '');
        }

        $row->start_date = $startDate !== '' ? $startDate : Factory::getDate()->format('Y-m-d');

        try {
            if ($id) {
                $existing = $this->getItem($id);

                if (!$existing) {
                    $this->setError('Subproject not found.');
                    return false;
                }

                if ($row->name === '') {
                    $row->name = (string) $existing->name;
                }

                if (!$row->project_id) {
                    $row->project_id = (int) $existing->project_id;
                }

                $row->id = $id;

                return $db->updateObject('#__kjeholtbusiness_subprojects', $row, 'id');
            }

            if (!$row->project_id) {
                $this->setError('No project id provided.');
                return false;
            }

            $row->created_by = $userId;
            return $db->insertObject('#__kjeholtbusiness_subprojects', $row, 'id');
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Change only the status of a subproject.
     */
    public function setStatus(int $id, string $status): bool
    {
        $allowed = ['not_started', 'ongoing', 'waiting_for_payment', 'finalized', 'cancelled'];

        if (!in_array($status, $allowed, true)) {
            $this->setError('Invalid status.');
            return false;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__kjeholtbusiness_subprojects'))
            ->set($db->quoteName('status') . ' = :status')
            ->set($db->quoteName('modified_by') . ' = :user_id')
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':status', $status)
            ->bind(':user_id', (int) Factory::getUser()->id, ParameterType::INTEGER)
            ->bind(':id', $id, ParameterType::INTEGER);

        try {
            $db->setQuery($query)->execute();
            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    public function getItems(): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('sp.id'),
                $db->quoteName('sp.name'),
                $db->quoteName('sp.status'),
                $db->quoteName('sp.start_date'),
                $db->quoteName('sp.hourly_rate'),
                $db->quoteName('p.name', 'project_name'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'sp'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_projects', 'p'), $db->quoteName('p.id') . ' = ' . $db->quoteName('sp.project_id'))
            ->order($db->quoteName('p.name') . ' ASC, ' . $db->quoteName('sp.name') . ' ASC');

        $companyId = \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser::companyId();

        if ($companyId) {
            $query->where($db->quoteName('p.company_id') . ' = :company_id')
                ->bind(':company_id', $companyId, ParameterType::INTEGER);
        }

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
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
        if ($this->item !== null) {
            return (array) $this->item;
        }

        $sessionData = Factory::getApplication()->getUserState('com_kjeholtbusiness.subproject.edit.data', []);

        if ($sessionData) {
            return $sessionData;
        }

        return [
            'id'         => 0,
            'project_id' => (int) Factory::getApplication()->input->getInt('project_id', 0),
        ];
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
