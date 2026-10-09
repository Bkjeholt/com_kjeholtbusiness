<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;
use Joomla\CMS\MVC\Model\ListModel;

class TimeChartsModel extends ListModel
{
    protected function getListQuery()
    {
        $app       = Factory::getApplication();
        $companyId = (int) $app->getUserState('com_kjeholtbusiness.company.id', 1);

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('tc.id'),
                $db->quoteName('tc.name'),
                $db->quoteName('tc.description'),
                $db->quoteName('tc.start_time'),
                $db->quoteName('tc.end_time'),
                $db->quoteName('tc.adjustment'),
                $db->quoteName('tc.status'),
                $db->quoteName('tc.created_by'),
                $db->quoteName('sp.name', 'subproject_name'),
                $db->quoteName('p.name', 'project_name'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_timecards', 'tc'))
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_subprojects', 'sp'),
                $db->quoteName('sp.id') . ' = ' . $db->quoteName('tc.subproject_id')
            )
            ->join(
                'LEFT',
                $db->quoteName('#__kjeholtbusiness_projects', 'p'),
                $db->quoteName('p.id') . ' = ' . $db->quoteName('sp.project_id')
            )
            ->where(
                '(' . $db->quoteName('p.company_id') . ' = :company_id'
                . ' OR ' . $db->quoteName('tc.subproject_id') . ' = 0)'
            )
            ->bind(':company_id', $companyId, ParameterType::INTEGER)
            ->order($db->quoteName('tc.start_time') . ' DESC');

        return $query;
    }
}
