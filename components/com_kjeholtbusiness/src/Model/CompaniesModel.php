<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;

class CompaniesModel extends BaseDatabaseModel
{
    public function getItems(): array
    {
        if (!CompanyAcl::mayListAllCompanies()) {
            return [];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('id'),
                $db->quoteName('name'),
                $db->quoteName('org_number'),
                $db->quoteName('city'),
                $db->quoteName('email'),
                $db->quoteName('phone'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_companies'))
            ->order($db->quoteName('name') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }
}
