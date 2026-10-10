<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser;

class CompanyusersModel extends BaseDatabaseModel
{
    /**
     * Users connected to the given company: id, name, username, email.
     */
    public function getItems(): array
    {
        $companyId = $this->companyId();

        if (!$companyId) {
            return [];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('u.id'),
                $db->quoteName('u.name'),
                $db->quoteName('u.username'),
                $db->quoteName('u.email'),
                $db->quoteName('cu.is_primary'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_company_users', 'cu'))
            ->join('INNER', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('cu.user_id'))
            ->where($db->quoteName('cu.company_id') . ' = :company_id')
            ->bind(':company_id', $companyId, ParameterType::INTEGER)
            ->order($db->quoteName('u.name') . ' ASC');

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    /**
     * Joomla users not yet connected to any company, for the add dropdown.
     */
    public function getAvailableUsers(): array
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

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }

    public function companyId(): int
    {
        $companyId = (int) Factory::getApplication()->input->getInt('company_id', 0);

        if ($companyId) {
            return $companyId;
        }

        return (int) (CompanyUser::companyId() ?? 0);
    }

    public function companyName(): string
    {
        return CompanyUser::companyNameForId($this->companyId()) ?? '';
    }
}
