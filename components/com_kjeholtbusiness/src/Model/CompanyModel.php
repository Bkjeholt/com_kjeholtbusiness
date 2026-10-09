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

        if (!$id) {
            $this->setError('No company id provided.');
            return false;
        }

        $existing = $this->getItem($id);

        if (!$existing) {
            $this->setError('Company not found.');
            return false;
        }

        $row = new \stdClass();
        $row->id          = $id;
        $row->name        = (string) ($data['name'] ?? $existing->name);
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
            return $db->updateObject('#__kjeholtbusiness_companies', $row, 'id');
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}
