<?php

namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Company ACL for the redesigned KjeEng-BSS scheme.
 *
 * Only two Joomla user groups exist:
 *  - UG:KjeEng-BSS:Admin : suite admins; create companies, assign
 *    company admins (the 'admin' flag in company_users.permissions)
 *  - UG:KjeEng-BSS:User  : company users, granted when connected in
 *    the company_users table
 *
 * Everything else is decided by the SET flags in
 * company_users.permissions (see BssAcl::FLAGS).
 */
class CompanyAcl
{
    public const GROUP_ADMIN = 'UG:KjeEng-BSS:Admin';
    public const GROUP_USER  = 'UG:KjeEng-BSS:User';

    private static ?int $adminGroupId = null;

    /**
     * True when the user is a suite admin (member of UG:KjeEng-BSS:Admin).
     */
    public static function isSuiteAdmin(?int $userId = null): bool
    {
        $user = Factory::getUser($userId ?? null);

        if (!$user || $user->guest) {
            return false;
        }

        $groupId = self::adminGroupId();

        if (!$groupId) {
            return false;
        }

        return \in_array($groupId, \array_map('intval', $user->getAuthorisedGroups()), true);
    }

    /**
     * Kept for backwards compatibility with older call sites.
     */
    public static function isSuiteSuperAdmin(): bool
    {
        return self::isSuiteAdmin();
    }

    /**
     * True when the user is a company admin: suite admin, or holds the
     * 'admin' flag for their own company.
     */
    public static function isCompanyAdmin(?int $userId = null): bool
    {
        if (self::isSuiteAdmin($userId)) {
            return true;
        }

        return \in_array('admin', BssAcl::userFlags($userId), true);
    }

    /**
     * Suite admins may create companies and modify any company.
     * Company admins may modify their own company.
     */
    public static function canEditCompany(?object $company): bool
    {
        if (self::isSuiteAdmin()) {
            return true;
        }

        if (!$company || empty($company->name)) {
            return false;
        }

        if (\in_array('admin', BssAcl::userFlags(), true)) {
            return CompanyUser::companyName() === $company->name;
        }

        return false;
    }

    /**
     * Suite admins may list all companies; company admins see their own.
     */
    public static function mayListAllCompanies(): bool
    {
        return self::isSuiteAdmin();
    }

    /**
     * Who may see a company's information: suite admin, company admin,
     * or a member of the company.
     */
    public static function canViewCompany(?object $company): bool
    {
        if (!$company || empty($company->name)) {
            return self::isSuiteAdmin();
        }

        if (self::canEditCompany($company)) {
            return true;
        }

        return CompanyUser::companyName() === $company->name;
    }

    private static function adminGroupId(): int
    {
        if (self::$adminGroupId !== null) {
            return self::$adminGroupId;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = :title');
        $query->bind(':title', self::GROUP_ADMIN);

        return self::$adminGroupId = (int) $db->setQuery($query)->loadResult();
    }
}
