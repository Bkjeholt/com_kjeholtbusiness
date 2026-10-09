<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\TimereportAcl;

class TimereportsModel extends ListModel
{
    protected function populateState($ordering = 'tc.start_time', $direction = 'DESC')
    {
        parent::populateState($ordering, $direction);
    }

    public static function calculateHours(?string $start, ?string $end, int $adjustment): ?float
    {
        if (empty($end)) {
            return null;
        }

        $startTs = strtotime((string) $start);
        $endTs   = strtotime((string) $end);

        if ($startTs === false || $endTs === false || $endTs < $startTs) {
            return null;
        }

        $minutes = ($endTs - $startTs) / 60 + $adjustment;
        $hours   = $minutes / 60;

        return ceil($hours * 4) / 4;
    }

    protected function getListQuery()
    {
        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;

        $query = $db->getQuery(true);

        if (!TimereportAcl::seesAll()) {
            $query->where($db->quoteName('tc.created_by') . ' = :user_id')
                ->bind(':user_id', $userId, ParameterType::INTEGER);
        }

        $query->select(
            [
                $db->quoteName('tc.id'),
                $db->quoteName('tc.name'),
                $db->quoteName('tc.description'),
                $db->quoteName('tc.subproject_id'),
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
            ->order($db->quoteName('tc.start_time') . ' DESC');

        return $query;
    }
}
