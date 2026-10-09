<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Permission helper for time reports.
 *
 * The timereport.* ACL actions are defined in the component manifest.
 * Users holding timereport.edit / timereport.view may act on any report;
 * otherwise only on reports they created themselves (edit.own semantics).
 */
class TimereportAcl
{
    public static function canEdit(object $item): bool
    {
        $user = Factory::getUser();

        if ($user->authorise('timereport.edit', 'com_kjeholtbusiness')) {
            return true;
        }

        return (int) $item->created_by === (int) $user->id;
    }

    public static function canView(object $item): bool
    {
        $user = Factory::getUser();

        if ($user->authorise('timereport.view', 'com_kjeholtbusiness')) {
            return true;
        }

        return (int) $item->created_by === (int) $user->id;
    }

    public static function canCreate(): bool
    {
        return (bool) Factory::getUser()->authorise('timereport.create', 'com_kjeholtbusiness');
    }

    public static function canDelete(object $item): bool
    {
        $user = Factory::getUser();

        if ($user->authorise('timereport.delete', 'com_kjeholtbusiness')) {
            return true;
        }

        // Users may delete their own reports unless validated or frozen
        if (isset($item->status) && \in_array($item->status, ['validated', 'froozen'], true)) {
            return false;
        }

        return (int) $item->created_by === (int) $user->id;
    }

    public static function mayDeleteAny(): bool
    {
        return (bool) Factory::getUser()->authorise('timereport.delete', 'com_kjeholtbusiness');
    }

    /**
     * Admin action: may revert a validated report back to ended.
     */
    public static function canUnvalidate(): bool
    {
        return (bool) Factory::getUser()->authorise('timereport.unvalidate', 'com_kjeholtbusiness');
    }

    /**
     * True when the user may see every user's reports (for list scoping).
     */
    public static function seesAll(): bool
    {
        return (bool) Factory::getUser()->authorise('timereport.view', 'com_kjeholtbusiness');
    }
}
