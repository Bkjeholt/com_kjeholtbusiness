<?php

namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * KjeEng-BSS access helper.
 *
 * Scheme (redesigned):
 *  - Only two Joomla user groups: UG:KjeEng-BSS:Admin and UG:KjeEng-BSS:User,
 *    exposed through the two view levels ACL:KjeEng-BSS:Admin and
 *    ACL:KjeEng-BSS:User.
 *  - Fine-grained permissions live in the company_users.permissions
 *    column, a MySQL SET:
 *      admin, user-edit, project-edit, project-view, subproject-edit,
 *      timereport-edit, timereport-view, expense-edit, expense-view,
 *      invoice-approve, invoice-edit, customer-edit, customer-view,
 *      logbook-view
 *  - Suite admins (UG:KjeEng-BSS:Admin) implicitly hold every permission.
 */
class BssAcl
{
    /** All flags of the company_users.permissions SET, in column order. */
    public const FLAGS = [
        'admin',
        'user-edit',
        'project-edit',
        'project-view',
        'subproject-edit',
        'timereport-edit',
        'timereport-view',
        'expense-edit',
        'expense-view',
        'invoice-approve',
        'invoice-edit',
        'customer-edit',
        'customer-view',
        'logbook-view',
    ];

    /**
     * Legacy level names used by older call sites, mapped to SET flags.
     * 'Admin' and 'view' are the legacy company-level names.
     */
    private static $legacyMap = [
        'Admin'            => 'admin',
        'view'             => 'project-view',
        'project:edit'     => 'project-edit',
        'project:view'     => 'project-view',
        'subproject:edit'  => 'subproject-edit',
        'timereport:edit'  => 'timereport-edit',
        'timereport:view'  => 'timereport-view',
        'expense:edit'     => 'expense-edit',
        'expense:view'     => 'expense-view',
        'invoice:approve'  => 'invoice-approve',
        'invoice:edit'     => 'invoice-edit',
        'customer:edit'    => 'customer-edit',
        'customer:view'    => 'customer-view',
        'logbook:view'     => 'logbook-view',
        'user:edit'        => 'user-edit',
    ];

    /** @var array|null Cache of the current user's permission flags. */
    private static $userFlags;

    /**
     * The permission flags of the current (or given) user, from
     * company_users.permissions. Suite admins get all flags.
     *
     * @return string[]
     */
    public static function userFlags(?int $userId = null): array
    {
        $user = Factory::getUser($userId ?? null);

        if (!$user || $user->guest) {
            return [];
        }

        if (self::$userFlags !== null && $userId === null) {
            return self::$userFlags;
        }

        if (CompanyAcl::isSuiteAdmin($userId)) {
            $flags = self::FLAGS;
        } else {
            $flags = CompanyUser::permissions((int) $user->id);
        }

        if ($userId === null) {
            self::$userFlags = $flags;
        }

        return $flags;
    }

    /**
     * True when the current user holds the given permission flag.
     */
    public static function can(string $flag): bool
    {
        if (!\in_array($flag, self::FLAGS, true)) {
            return false;
        }

        return \in_array($flag, self::userFlags(), true);
    }

    /**
     * Legacy entry point: level names ('project:edit', 'Admin', ...)
     * are mapped to the new SET flags.
     */
    public static function hasAccessAny(string $level): bool
    {
        if (isset(self::$legacyMap[$level])) {
            return self::can(self::$legacyMap[$level]);
        }

        return self::can($level);
    }

    /**
     * True when the current user holds the permission for the given
     * company: suite admins always; company members when the flag is set
     * for their own company.
     */
    public static function hasAccess(string $companyName, string $level): bool
    {
        if (CompanyAcl::isSuiteAdmin()) {
            return true;
        }

        $userCompany = CompanyUser::companyName();

        if ($userCompany === null || $userCompany !== $companyName) {
            return false;
        }

        return self::hasAccessAny($level);
    }

    /**
     * Ensure the two Joomla user groups and the two access levels exist.
     * Callable at any time; existing records are updated, not duplicated.
     */
    public static function syncLevels(): void
    {
        $db = Factory::getDbo();

        $adminGroupId = self::ensureUserGroup('UG:KjeEng-BSS:Admin');
        $userGroupId  = self::ensureUserGroup('UG:KjeEng-BSS:User');

        self::ensureViewLevel('ACL:KjeEng-BSS:Admin', [$adminGroupId]);
        self::ensureViewLevel('ACL:KjeEng-BSS:User', [$adminGroupId, $userGroupId]);
    }

    /**
     * Kept for backwards compatibility with older call sites.
     */
    public static function createCompanyLevels(string $companyName, array $profileGroupIds): void
    {
        self::syncLevels();
    }

    private static function ensureUserGroup(string $title): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = :title');
        $query->bind(':title', $title);

        $id = (int) $db->setQuery($query)->loadResult();

        if ($id) {
            return $id;
        }

        $group        = new \stdClass();
        $group->parent_id = 1;
        $group->title = $title;
        $group->lft   = 0;
        $group->rgt   = 0;
        $db->insertObject('#__usergroups', $group);

        $table = \Joomla\CMS\Table\Table::getInstance('Usergroup');
        $table->rebuild();

        return (int) $db->insertid();
    }

    private static function ensureViewLevel(string $title, array $groupIds): void
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__viewlevels'))
            ->where($db->quoteName('title') . ' = :title');
        $query->bind(':title', $title);

        $existingId = (int) $db->setQuery($query)->loadResult();
        $rules      = \json_encode(\array_values(\array_unique(\array_map('intval', $groupIds))));

        if ($existingId) {
            $row        = new \stdClass();
            $row->id    = $existingId;
            $row->rules = $rules;
            $db->updateObject('#__viewlevels', $row, 'id');

            return;
        }

        $row        = new \stdClass();
        $row->title = $title;
        $row->rules = $rules;
        $db->insertObject('#__viewlevels', $row);
    }
}
