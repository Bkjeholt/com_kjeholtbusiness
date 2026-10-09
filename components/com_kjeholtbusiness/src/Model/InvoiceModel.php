<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Database\ParameterType;

class InvoiceModel extends FormModel
{
    protected $item;

    public function getItem(int $id = 0): ?object
    {
        if ($this->item) {
            return $this->item;
        }

        $id = $id ?: (int) Factory::getApplication()->input->getInt('id', 0);

        if (!$id) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('i.*'),
                $db->quoteName('c.name', 'customer_name'),
                $db->quoteName('p.name', 'project_name'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_invoices', 'i'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_customers', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('i.customer_id'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_projects', 'p'), $db->quoteName('p.id') . ' = ' . $db->quoteName('i.project_id'))
            ->where($db->quoteName('i.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        $this->item = $db->loadObject();

        if ($this->item) {
            $this->item->subproject_ids = $this->getSubprojectIds($id);
        }

        return $this->item;
    }

    public function getSubprojectIds(int $invoiceId): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($db->quoteName('subproject_id'))
            ->from($db->quoteName('#__kjeholtbusiness_invoice_subprojects'))
            ->where($db->quoteName('invoice_id') . ' = :invoice_id')
            ->bind(':invoice_id', $invoiceId, ParameterType::INTEGER);

        $db->setQuery($query);

        return \array_map('intval', $db->loadColumn() ?: []);
    }

    public function getForm($data = [], $loadData = true)
    {
        \Joomla\CMS\Form\Form::addFieldPath(JPATH_SITE . '/components/com_kjeholtbusiness/src/Field');

        $form = $this->loadForm(
            'com_kjeholtbusiness.invoice',
            'invoice',
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
        $sessionData = Factory::getApplication()->getUserState('com_kjeholtbusiness.invoice.edit.data');

        if ($sessionData) {
            return $sessionData;
        }

        $item = $this->getItem();

        if (!$item) {
            return [];
        }

        $data                    = (array) $item;
        $data['subprojects']     = $item->subproject_ids;

        return $data;
    }

    public function save(array $data): bool
    {
        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;
        $id     = (int) ($data['id'] ?? 0);

        $subprojectIds = \array_map('intval', (array) ($data['subprojects'] ?? []));
        unset($data['subprojects']);

        // Calculate totals from the selected subprojects
        $amount   = $this->sumSubprojectAmounts($subprojectIds);
        $rotPct   = (float) ($data['rot_percentage'] ?? 30);
        $rutPct   = (float) ($data['rut_percentage'] ?? 50);
        $rotApply = ($data['rot_reduction'] ?? 'no') === 'yes';
        $rutApply = ($data['rut_reduction'] ?? 'no') === 'yes';

        $rotAmount = $rotApply ? round($amount * $rotPct / 100, 2) : 0.0;
        $rutAmount = $rutApply ? round($amount * $rutPct / 100, 2) : 0.0;

        $row = new \stdClass();

        if ($id) {
            $row->id = $id;
        }

        $row->invoice_number = (string) ($data['invoice_number'] ?? '');
        $row->customer_id    = (int) ($data['customer_id'] ?? 0);
        $row->project_id    = (int) ($data['project_id'] ?? 0);
        $row->date          = (string) ($data['date'] ?? Factory::getDate()->format('Y-m-d'));
        $row->due_date      = (string) ($data['due_date'] ?? Factory::getDate()->format('Y-m-d'));
        $row->total_amount  = $amount;
        $row->rot_reduction = $rotApply ? 'yes' : 'no';
        $row->rot_percentage = $rotPct;
        $row->rot_amount    = $rotAmount;
        $row->rut_reduction = $rutApply ? 'yes' : 'no';
        $row->rut_percentage = $rutPct;
        $row->rut_amount    = $rutAmount;
        $row->status        = (string) ($data['status'] ?? 'draft');
        $row->modified_by   = $userId;

        try {
            if ($id) {
                $db->updateObject('#__kjeholtbusiness_invoices', $row, 'id');
            } else {
                $row->created_by = $userId;
                $db->insertObject('#__kjeholtbusiness_invoices', $row, 'id');
                $id = (int) $row->id;
            }

            $this->saveSubprojects($id, $subprojectIds);
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }

        return true;
    }

    private function sumSubprojectAmounts(array $subprojectIds): float
    {
        if (!$subprojectIds) {
            return 0.0;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $validatedCards = $db->quoteName('tc.status') . ' IN (' . $db->quote('validated') . ', ' . $db->quote('froozen') . ')';
        $validatedExp   = $db->quoteName('e.status') . ' IN (' . $db->quote('validated') . ', ' . $db->quote('froozen') . ')';

        $query->select(
            [
                'COALESCE(SUM(TIMESTAMPDIFF(MINUTE, tc.start_time, tc.end_time) + tc.adjustment) / 60 * ' . $db->quoteName('sp.hourly_rate') . ', 0) AS ' . $db->quoteName('time_cost'),
                'COALESCE((SELECT SUM(e.amount) FROM #__kjeholtbusiness_expenses AS e WHERE e.subproject_id = ' . $db->quoteName('sp.id') . ' AND ' . $validatedExp . '), 0) AS ' . $db->quoteName('expense_cost'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'sp'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_timecards', 'tc'), $db->quoteName('tc.subproject_id') . ' = ' . $db->quoteName('sp.id') . ' AND ' . $validatedCards)
            ->where($db->quoteName('sp.id') . ' IN (' . \implode(',', \array_fill(0, \count($subprojectIds), '?')) . ')')
            ->group($db->quoteName('sp.id'));

        $db->setQuery($query, 0, 0);
        $rows = $db->loadObjectList();

        $total = 0.0;

        foreach ($rows as $row) {
            $total += (float) $row->time_cost + (float) $row->expense_cost;
        }

        return round($total, 2);
    }

    private function saveSubprojects(int $invoiceId, array $subprojectIds): void
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__kjeholtbusiness_invoice_subprojects'))
            ->where($db->quoteName('invoice_id') . ' = :invoice_id')
            ->bind(':invoice_id', $invoiceId, ParameterType::INTEGER);
        $db->setQuery($query)->execute();

        foreach ($subprojectIds as $subprojectId) {
            $row                = new \stdClass();
            $row->invoice_id    = $invoiceId;
            $row->subproject_id = $subprojectId;
            $row->amount        = 0.00;
            $db->insertObject('#__kjeholtbusiness_invoice_subprojects', $row);
        }
    }
}
