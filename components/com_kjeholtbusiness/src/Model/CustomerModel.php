<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Database\ParameterType;

class CustomerModel extends FormModel
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

        $query->select('a.*')
            ->from($db->quoteName('#__kjeholtbusiness_customers', 'a'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        $this->item = $db->loadObject();

        return $this->item;
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_kjeholtbusiness.customer',
            'customer',
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
        $sessionData = Factory::getApplication()->getUserState('com_kjeholtbusiness.customer.edit.data');

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

        $row = new \stdClass();
        $row->company_id       = (int) ($data['company_id'] ?? \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser::companyId() ?? 0);
        $row->name             = (string) ($data['name'] ?? '');
        $row->ssn              = (string) ($data['ssn'] ?? '');
        $row->org_number       = (string) ($data['org_number'] ?? '');
        $row->address          = (string) ($data['address'] ?? '');
        $row->postal_code      = (string) ($data['postal_code'] ?? '');
        $row->city             = (string) ($data['city'] ?? '');
        $row->property_name    = (string) ($data['property_name'] ?? '');
        $row->property_address = (string) ($data['property_address'] ?? '');
        $row->phone            = (string) ($data['phone'] ?? '');
        $row->email            = (string) ($data['email'] ?? '');
        $row->modified_by      = $userId;

        try {
            if ($id) {
                $row->id = $id;
                return $db->updateObject('#__kjeholtbusiness_customers', $row, 'id');
            }

            $row->created_by = $userId;
            return $db->insertObject('#__kjeholtbusiness_customers', $row, 'id');
        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }
}
