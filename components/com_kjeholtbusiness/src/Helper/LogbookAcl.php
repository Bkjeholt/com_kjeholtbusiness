<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Logbook ACL based on the KjeEng-BSS access levels.
 *
 * Suite SuperAdmins and company Admins (level "Admin") may view
 * and comment all entries for their company; other users only
 * see and may comment their own entries.
 *
 * NOTE: per your matrix, the logbook cannot be modified - the
 * comment field is the only editable part and stays restricted.
 */
class LogbookAcl
{
    /**
     * True when the user holds any company "Admin" level (or is suite SuperAdmin).
     */
    public static function isCompanyAdmin(): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        if (CompanyAcl::mayListAllCompanies()) {
            return true;
        }

        return BssAcl::hasAccessAny('Admin');
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
