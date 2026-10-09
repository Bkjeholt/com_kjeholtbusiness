<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Company ACL based on the KjeEng-BSS access levels (view levels).
 *
 * Matrix (per company, plus suite SuperAdmin implicitly):
 *  - Admin       : UG <Company>:Admin + :Economy
 *  - view        : UG <Company>:Admin
 *  - project:*   : see BssAcl
 *  - timereport:*: see BssAcl
 *  - expense:*   : see BssAcl
 *  - invoice:*   : see BssAcl
 */
class CompanyAcl
{
    private static function isSuiteSuperAdmin(): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = :title')
            ->bind(':title', 'UG: KjeEng-BSS:SuperAdmin');

        $groupId = (int) $db->setQuery($query)->loadResult();

        if (!$groupId) {
            return false;
        }

        return \in_array($groupId, \array_map('intval', $user->getAuthorisedGroups()), true);
    }

    /**
     * Suite SuperAdmins may create companies and modify any company.
     * Company Admins (level "Admin") may modify their own company.
     */
    public static function canEditCompany(?object $company): bool
    {
        if (self::isSuiteSuperAdmin()) {
            return true;
        }

        if (!$company || empty($company->name)) {
            return false;
        }

        return BssAcl::hasAccess($company->name, 'Admin');
    }

    /**
     * Suite SuperAdmins may list all companies.
     */
    public static function mayListAllCompanies(): bool
    {
        return self::isSuiteSuperAdmin();
    }

    /**
     * Who may see a company's information: suite SuperAdmin, or the
     * company "view" level (Admin profile).
     */
    public static function canViewCompany(?object $company): bool
    {
        if (self::mayListAllCompanies() || self::canEditCompany($company)) {
            return true;
        }

        if (!$company || empty($company->name)) {
            return false;
        }

        return BssAcl::hasAccess($company->name, 'view');
    }
}
