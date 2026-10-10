<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

class ExpensesModel extends ListModel
{
    protected function populateState($ordering = 'e.date', $direction = 'DESC')
    {
        parent::populateState($ordering, $direction);
    }

    protected function getListQuery()
    {
        $companyId = \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser::companyId();

        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;

        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('e.id'),
                $db->quoteName('e.name'),
                $db->quoteName('e.description'),
                $db->quoteName('e.subproject_id'),
                $db->quoteName('e.date'),
                $db->quoteName('e.amount'),
                $db->quoteName('e.status'),
                $db->quoteName('e.created_by'),
                $db->quoteName('sp.name', 'subproject_name'),
                $db->quoteName('p.name', 'project_name'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_expenses', 'e'))
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_subprojects', 'sp'),
                $db->quoteName('sp.id') . ' = ' . $db->quoteName('e.subproject_id')
            )
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_projects', 'p'),
                $db->quoteName('p.id') . ' = ' . $db->quoteName('sp.project_id')
            )
            ->where($db->quoteName('e.created_by') . ' = :user_id')
            ->bind(':user_id', $userId, ParameterType::INTEGER)
            ->order($db->quoteName('e.date') . ' DESC');

        if ($companyId) {
            $query->where(
                'sp.id IN (SELECT sp2.id FROM #__kjeholtbusiness_subprojects AS sp2'
                . ' JOIN #__kjeholtbusiness_projects AS p2 ON p2.id = sp2.project_id'
                . ' WHERE p2.company_id = :scope_company_id)'
            )->bind(':scope_company_id', $companyId, \Joomla\Database\ParameterType::INTEGER);
        }

        return $query;
    }
}
