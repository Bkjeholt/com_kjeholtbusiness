<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

/**
 * Central logic for timecard (time report) lifecycle:
 * ending ongoing reports, the 24h auto-close rule and the midnight split.
 */
class TimecardService
{
    /**
     * End the currently ongoing timecard(s) for a user.
     * Optionally splits the report per date if it passed midnight.
     *
     * @param   int      $userId   The user id
     * @param   ?string  $endTime  Explicit end time (Y-m-d H:i:s); null = now
     *
     * @return  int  Number of reports ended
     */
    public static function endOngoing(int $userId, ?string $endTime = null): int
    {
        $db     = Factory::getDbo();
        $userId = (int) $userId;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__kjeholtbusiness_timecards'))
            ->where($db->quoteName('created_by') . ' = :user_id')
            ->where($db->quoteName('status') . ' = ' . $db->quote('ongoing'))
            ->bind(':user_id', $userId, ParameterType::INTEGER);

        $ongoing = $db->setQuery($query)->loadObjectList();

        if (!$ongoing) {
            return 0;
        }

        $count = 0;

        foreach ($ongoing as $report) {
            $end   = $endTime ?: Factory::getDate()->toSql();
            $parts = self::splitOnMidnight($report->start_time, $end);

            if (\count($parts) > 1) {
                // First piece: ends at midnight of the start date
                $first            = new \stdClass();
                $first->id        = (int) $report->id;
                $first->end_time  = $parts[0]['end'];
                $first->status    = 'ended';
                $first->modified_by = $userId;
                $db->updateObject('#__kjeholtbusiness_timecards', $first, 'id');

                // Second piece: new row from midnight to the end time
                $second               = new \stdClass();
                $second->name          = $report->name;
                $second->description   = $report->description;
                $second->subproject_id = (int) $report->subproject_id;
                $second->start_time    = $parts[1]['start'];
                $second->end_time      = $parts[1]['end'];
                $second->adjustment    = 0;
                $second->status        = 'ended';
                $second->created_by    = $userId;
                $second->created_at    = $parts[1]['start'];
                $db->insertObject('#__kjeholtbusiness_timecards', $second);
            } else {
                $row              = new \stdClass();
                $row->id         = (int) $report->id;
                $row->end_time   = $end;
                $row->status     = 'ended';
                $row->modified_by = $userId;
                $db->updateObject('#__kjeholtbusiness_timecards', $row, 'id');
            }

            $count++;
        }

        return $count;
    }

    /**
     * Auto-close reports still ongoing after 24 hours:
     * ended with an end time 23 hours 59 minutes after start.
     * Midnight split applies to those as well.
     *
     * @param   int  $userId  The user id
     *
     * @return  int  Number of reports auto-closed
     */
    public static function autoCloseAfter24h(int $userId): int
    {
        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__kjeholtbusiness_timecards'))
            ->where($db->quoteName('created_by') . ' = :user_id')
            ->where($db->quoteName('status') . ' = ' . $db->quote('ongoing'))
            ->where($db->quoteName('start_time') . ' < :cutoff')
            ->bind(':user_id', $userId, ParameterType::INTEGER)
            ->bind(':cutoff', Factory::getDate()->modify('-24 hours')->toSql());

        $stale = $db->setQuery($query)->loadObjectList();

        $count = 0;

        foreach ($stale as $report) {
            $end = Factory::getDate($report->start_time)->modify('+23 hours +59 minutes')->toSql();
            self::closeReport($report, $end, $userId);
            $count++;
        }

        return $count;
    }

    /**
     * Close a single timecard report at the given end time,
     * applying the midnight split when the range spans two dates.
     */
    private static function closeReport(object $report, string $end, int $userId): void
    {
        $db    = Factory::getDbo();
        $parts = self::splitOnMidnight($report->start_time, $end);

        if (\count($parts) > 1) {
            $first              = new \stdClass();
            $first->id          = (int) $report->id;
            $first->end_time    = $parts[0]['end'];
            $first->status      = 'ended';
            $first->modified_by = $userId;
            $db->updateObject('#__kjeholtbusiness_timecards', $first, 'id');

            $second               = new \stdClass();
            $second->name          = $report->name;
            $second->description   = $report->description;
            $second->subproject_id = (int) $report->subproject_id;
            $second->start_time    = $parts[1]['start'];
            $second->end_time      = $parts[1]['end'];
            $second->adjustment    = 0;
            $second->status        = 'ended';
            $second->created_by    = $userId;
            $second->created_at    = $parts[1]['start'];
            $db->insertObject('#__kjeholtbusiness_timecards', $second);
        } else {
            $row              = new \stdClass();
            $row->id          = (int) $report->id;
            $row->end_time    = $end;
            $row->status      = 'ended';
            $row->modified_by = $userId;
            $db->updateObject('#__kjeholtbusiness_timecards', $row, 'id');
        }
    }

    /**
     * Split a start/end range at midnight.
     *
     * @return  array  One part if within a single date, two parts if it passed midnight
     */
    private static function splitOnMidnight(string $start, string $end): array
    {
        $startDate = substr($start, 0, 10);
        $endDate   = substr($end, 0, 10);

        if ($startDate === $endDate) {
            return [['start' => $start, 'end' => $end]];
        }

        return [
            ['start' => $start, 'end' => $startDate . ' 23:59:59'],
            ['start' => $endDate . ' 00:00:00', 'end' => $end],
        ];
    }
}
