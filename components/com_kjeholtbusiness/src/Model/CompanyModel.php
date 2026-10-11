<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Database\ParameterType;

class CompanyModel extends FormModel
{
    protected $item;


    /**
     * The responsible company admin user for the given company:
     * the user connected via company_users who holds the 'admin' flag.
     */
    public function getAdmin(int $companyId = 0): ?object
    {
        if (!$companyId) {
            $item = $this->getItem();
            $companyId = (int) ($item->id ?? 0);
        }

        if (!$companyId) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select(
            [
                $db->quoteName('u.id'),
                $db->quoteName('u.name'),
                $db->quoteName('u.username'),
                $db->quoteName('u.email'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_company_users', 'cu'))
            ->join('INNER', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('cu.user_id'))
            ->where($db->quoteName('cu.company_id') . ' = :company_id')
            ->where("FIND_IN_SET('admin', " . $db->quoteName('cu.permissions') . ")")
            ->bind(':company_id', $companyId, ParameterType::INTEGER)
            ->order($db->quoteName('cu.id') . ' ASC');

        return $db->setQuery($query)->loadObject() ?: null;
    }

    /**
     * Make the given user the responsible company admin: connect the
     * user to the company (if needed) with the 'admin' permission flag.
     */
    public function setAdmin(int $companyId, int $userId): bool
    {
        $existing = CompanyUser::permissions($userId);

        if (!in_array('admin', $existing, true)) {
            $existing[] = 'admin';
        }

        if (CompanyUser::companyId($userId) === $companyId) {
            return CompanyUser::setPermissions($userId, $existing);
        }

        return CompanyUser::connect($userId, $companyId, $existing);
    }

    /**
     * Joomla users available for assignment as company admin: not
     * connected to another company.
     */
    public function getAvailableAdminUsers(): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select(
            [
                $db->quoteName('u.id'),
                $db->quoteName('u.name'),
                $db->quoteName('u.username'),
            ]
        )
            ->from($db->quoteName('#__users', 'u'))
            ->where($db->quoteName('u.block') . ' = 0')
            ->where($db->quoteName('u.id') . ' NOT IN (SELECT user_id FROM #__kjeholtbusiness_company_users)')
            ->order($db->quoteName('u.name') . ' ASC');

        return $db->setQuery($query)->loadObjectList() ?: [];
    }

    public function getItem(int $id = 0): ?object
    {
        if ($this->item) {
            return $this->item;
        }

        $app = Factory::getApplication();

        if (!$id) {
            $id = (int) $app->input->getInt('id', 0);
        }

        if (!$id) {
            $id = (int) $app->getUserState('com_kjeholtbusiness.company.id', 0);
        }

        if (!$id) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__kjeholtbusiness_companies', 'a'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        $this->item = $db->loadObject();

        return $this->item;
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_kjeholtbusiness.company',
            'company',
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
        $sessionData = Factory::getApplication()->getUserState('com_kjeholtbusiness.company.edit.data');

        if ($sessionData) {
            return $sessionData;
        }

        $item = $this->getItem();

        return $item ? (array) $item : [];
    }

    public function save(array $data): bool
    {
        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;
        $id     = (int) ($data['id'] ?? 0);

        $existing = $id ? $this->getItem($id) : null;

        if ($id && !$existing) {
            $this->setError('Company not found.');
            return false;
        }

        $row = new \stdClass();
        $row->name        = (string) ($data['name'] ?? ($existing->name ?? ''));
        $row->org_number  = (string) ($data['org_number'] ?? '');
        $row->address     = (string) ($data['address'] ?? '');
        $row->postal_code = (string) ($data['postal_code'] ?? '');
        $row->city        = (string) ($data['city'] ?? '');
        $row->phone       = (string) ($data['phone'] ?? '');
        $row->website     = (string) ($data['website'] ?? '');
        $row->email       = (string) ($data['email'] ?? '');
        $row->bank_name   = (string) ($data['bank_name'] ?? '');
        $row->bankgiro    = (string) ($data['bankgiro'] ?? '');
        $row->iban        = (string) ($data['iban'] ?? '');
        $row->modified_by = $userId;

        try {
            if ($id) {
                $row->id = $id;
                $db->updateObject('#__kjeholtbusiness_companies', $row, 'id');
            } else {
                $row->created_by = $userId;
                $db->insertObject('#__kjeholtbusiness_companies', $row, 'id');
                $id = (int) $row->id;
            }

            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    /**
     * Soft-delete a company: mark it deleted in the database.
     * The row and its user groups are kept for referential integrity.
     */
    public function delete(int $id): bool
    {
        $db = $this->getDatabase();

        $item = $this->getItem($id);

        if (!$item) {
            $this->setError('Company not found.');
            return false;
        }

        try {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__kjeholtbusiness_companies'))
                ->set($db->quoteName('deleted') . ' = 1')
                ->set($db->quoteName('modified_by') . ' = :user_id')
                ->where($db->quoteName('id') . ' = :id')
                ->bind(':user_id', (int) Factory::getUser()->id, ParameterType::INTEGER)
                ->bind(':id', $id, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\Logbook::log(
                'company.deleted',
                sprintf('Company #%d (%s) was marked as deleted.', $id, $item->name)
            );

            return true;
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }


}
