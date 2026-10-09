<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Timereport ACL based on the KjeEng-BSS access levels.
 *
 * timereport:edit - UG <Company>:Admin, :Employee (+ company/ suite SuperAdmin)
 * timereport:view - UG <Company>:Admin, :Employee, :Visitor
 */
class TimereportAcl
{
    public static function canEdit(object $item): bool
    {
        if (self::mayEditAny()) {
            return true;
        }

        return (int) $item->created_by === (int) Factory::getUser()->id;
    }

    public static function canView(object $item): bool
    {
        if (self::mayViewAny()) {
            return true;
        }

        return (int) $item->created_by === (int) Factory::getUser()->id;
    }

    public static function canCreate(): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        return BssAcl::hasAccessAny('timereport:edit');
    }

    public static function canDelete(object $item): bool
    {
        if (self::mayDeleteAny()) {
            return true;
        }

        if (isset($item->status) && \in_array($item->status, ['validated', 'froozen'], true)) {
            return false;
        }

        return (int) $item->created_by === (int) Factory::getUser()->id;
    }

    public static function mayDeleteAny(): bool
    {
        return BssAcl::hasAccessAny('timereport:edit');
    }

    public static function mayEditAny(): bool
    {
        return BssAcl::hasAccessAny('timereport:edit');
    }

    public static function mayViewAny(): bool
    {
        return BssAcl::hasAccessAny('timereport:view');
    }

    /**
     * True when the user may see every user's reports (for list scoping).
     */
    public static function seesAll(): bool
    {
        return self::mayViewAny();
    }

    /**
     * Admin action: may revert a validated report back to ended.
     * (timereport:edit level)
     */
    public static function canUnvalidate(): bool
    {
        return BssAcl::hasAccessAny('timereport:edit');
    }
}
