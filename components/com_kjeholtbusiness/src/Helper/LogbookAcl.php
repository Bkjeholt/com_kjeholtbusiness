<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * ACL helper for the logbook.
 *
 * Company admins (members of a "UG: <company> - CompanyAdmin" user group)
 * may view and comment all entries; regular users only see and may
 * comment their own entries.
 */
class LogbookAcl
{
    /**
     * True when the current user is a company admin
     * (member of any "UG: <company> - CompanyAdmin" group).
     */
    public static function isCompanyAdmin(): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        $groupIds = $user->getAuthorisedGroups();

        if (!$groupIds) {
            return false;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' LIKE ' . $db->quote('UG: % - CompanyAdmin'));

        $adminGroupIds = $db->setQuery($query)->loadColumn();

        if (!$adminGroupIds) {
            return false;
        }

        foreach ($groupIds as $groupId) {
            if (\in_array((int) $groupId, \array_map('intval', $adminGroupIds), true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * May the current user view the given logbook entry?
     */
    public static function canView(object $entry): bool
    {
        if (self::isCompanyAdmin()) {
            return true;
        }

        return (int) $entry->user_id === (int) Factory::getUser()->id;
    }

    /**
     * May the current user update the comment on the given entry?
     * Company admins may update any entry; users only their own.
     */
    public static function canUpdateComment(object $entry): bool
    {
        if (self::isCompanyAdmin()) {
            return true;
        }

        return (int) $entry->user_id === (int) Factory::getUser()->id;
    }
}
