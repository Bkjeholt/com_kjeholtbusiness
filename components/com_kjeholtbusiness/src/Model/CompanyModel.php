<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Database\ParameterType;

class CompanyModel extends FormModel
{
    protected $item;

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

                // Create the company's user groups (UG: KjeEng-BSS:<Company>:*)
                $this->createCompanyUserGroups((string) $row->name);
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

    /**
     * Create the UG: KjeEng-BSS:<CompanyName> group hierarchy for a new company.
     */
    private function createCompanyUserGroups(string $companyName): void
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = :title')
            $bssRootTitle = 'UG: KjeEng-BSS';
        $query->bind(':title', $bssRootTitle);

        $bssRootId = (int) $db->setQuery($query)->loadResult();

        if (!$bssRootId) {
            return;
        }

        // Company group under the BSS root
        $companyGroup        = new \stdClass();
        $companyGroup->title = 'UG: KjeEng-BSS:' . $companyName;
        $companyGroup->parent_id = $bssRootId;
        $db->insertObject('#__usergroups', $companyGroup);
        $companyGroupId = (int) $companyGroup->id = $db->insertid();

        $profiles = ['SuperAdmin', 'Admin', 'Economy', 'Employee', 'Visitor'];
        $profileGroupIds = [];

        foreach ($profiles as $profile) {
            $profileGroup            = new \stdClass();
            $profileGroup->parent_id = $companyGroupId;
            $profileGroup->title     = 'UG: KjeEng-BSS:' . $companyName . ':' . $profile;
            $db->insertObject('#__usergroups', $profileGroup);
            $profileGroupIds[$profile] = (int) $db->insertid();
        }

        // Create the access levels (ACL: KjeEng-BSS:<Company>:<Level>)
        \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl::createCompanyLevels(
            $companyName,
            $profileGroupIds
        );

        // Rebuild the nested-set tree so lft/rgt stay consistent
        \Joomla\CMS\Access\Access::clearCache();
        $table = \Joomla\CMS\Table\Table::getInstance('Usergroup');
        $table->rebuild();
    }
}
