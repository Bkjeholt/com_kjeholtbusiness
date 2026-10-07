<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Database\ParameterType;

class ExpenseModel extends FormModel
{
    protected $item;

    public function getItem(int $id = 0): ?object
    {
        if ($this->item) {
            return $this->item;
        }

        $app = Factory::getApplication();
        $id  = $id ?: (int) $app->input->getInt('id');

        if (!$id) {
            return null;
        }

        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__kjeholtbusiness_expenses'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('created_by') . ' = :user_id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':user_id', $userId, ParameterType::INTEGER);

        $this->item = $db->setQuery($query)->loadObject();

        return $this->item;
    }

    public function getForm($data = [], $loadData = true)
    {
        return $this->loadForm(
            'com_kjeholtbusiness.expense',
            'expense',
            ['control' => 'jform', 'load_data' => $loadData]
        );
    }

    protected function loadFormData()
    {
        return (array) $this->getItem();
    }

    public function save(array $data): bool
    {
        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;

        $row = new \stdClass();
        $row->name         = (string) ($data['name'] ?? '');
        $row->description  = (string) ($data['description'] ?? '');
        $row->subproject_id = (int) ($data['subproject_id'] ?? 0);
        $row->date         = (string) ($data['date'] ?? Factory::getDate()->format('Y-m-d'));
        $row->amount       = (float) ($data['amount'] ?? 0);
        $row->supplier     = (string) ($data['supplier'] ?? '');
        $row->status       = (string) ($data['status'] ?? 'new');
        $row->created_by   = $userId;

        $id = (int) ($data['id'] ?? 0);

        try {
            if ($id > 0) {
                $existing = $this->getItem($id);

                if (!$existing) {
                    $this->setError('Expense not found or not owned by the current user.');

                    return false;
                }

                $row->id         = $id;
                $row->created_by = (int) $existing->created_by;
                $row->modified_by = $userId;

                if (!$db->updateObject('#__kjeholtbusiness_expenses', $row, 'id')) {
                    throw new \RuntimeException($db->getError());
                }
            } else {
                if (!$db->insertObject('#__kjeholtbusiness_expenses', $row, 'id')) {
                    throw new \RuntimeException($db->getError());
                }
            }
        } catch (\Throwable $e) {
            $this->setError($e->getMessage());

            return false;
        }

        return true;
    }
}
