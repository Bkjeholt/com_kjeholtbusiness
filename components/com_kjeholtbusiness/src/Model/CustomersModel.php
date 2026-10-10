<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class CustomersModel extends BaseDatabaseModel
{
    public function getItems(): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('id'),
                $db->quoteName('name'),
                $db->quoteName('ssn'),
                $db->quoteName('property_name'),
                $db->quoteName('city'),
                $db->quoteName('phone'),
                $db->quoteName('email'),
                $db->quoteName('created_by'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_customers'))
            ->order($db->quoteName('name') . ' ASC');

        $companyId = \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser::companyId();

        if ($companyId) {
            $query->where($db->quoteName('company_id') . ' = :company_id')
                ->bind(':company_id', $companyId, \Joomla\Database\ParameterType::INTEGER);
        }

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }
}
