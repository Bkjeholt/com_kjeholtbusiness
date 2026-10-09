<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

/**
 * Audit trail for important events.
 *
 * Entries are immutable except for the comment field,
 * which may be edited afterwards via updateComment().
 */
class Logbook
{
    /**
     * Record an event in the logbook.
     *
     * @param   string  $event       Short event identifier, e.g. "timereport.validated"
     * @param   string  $eventText   Human readable description of what happened
     * @param   ?string $comment     Optional initial comment
     * @param   ?int    $userId      Actor user id; null = current user
     *
     * @return  bool  True on success
     */
    public static function log(string $event, string $eventText, ?string $comment = null, ?int $userId = null): bool
    {
        $db     = Factory::getDbo();
        $userId = $userId ?? (int) Factory::getUser()->id;

        $row             = new \stdClass();
        $row->event_time = Factory::getDate()->toSql();
        $row->user_id    = $userId;
        $row->event      = $event;
        $row->event_text = $eventText;
        $row->comment    = $comment;
        $row->modified_by = $userId;

        try {
            $db->insertObject('#__kjeholtbusiness_logbook', $row, 'id');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update the comment on an existing logbook entry.
     * The comment is the only field that may be modified afterwards.
     *
     * @param   int     $id       Logbook entry id
     * @param   string  $comment  New comment text
     * @param   ?int    $userId   Actor user id; null = current user
     *
     * @return  bool  True on success
     */
    public static function updateComment(int $id, string $comment, ?int $userId = null): bool
    {
        $db     = Factory::getDbo();
        $userId = $userId ?? (int) Factory::getUser()->id;

        $row             = new \stdClass();
        $row->id         = $id;
        $row->comment    = $comment;
        $row->modified_by = $userId;

        try {
            return $db->updateObject('#__kjeholtbusiness_logbook', $row, ['id'], false);
        } catch (\Exception $e) {
            return false;
        }
    }
}
