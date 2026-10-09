<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Company ACL helper.
 *
 * Maps actions to the KjeBus usergroups:
 *  - Create/modify any company:   "KjeBus: Business Support Suite:Admin - SuperAdmin"
 *  - List all companies:          "KjeBus: Business Support Suite:Admin - Info"
 *  - Modify own company:          "KjeBus: <Company>:Company - Admin"
 *  - Show own company information "KjeBus: <Company>:Company - View"
 */
class CompanyAcl
{
    private static function userInGroup(string $title): bool
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
            ->bind(':title', $title);

        $groupId = $db->setQuery($query)->loadResult();

        if (!$groupId) {
            return false;
        }

        return \in_array((int) $groupId, \array_map('intval', $user->getAuthorisedGroups()), true);
    }

    /**
     * May create new companies and modify any existing company.
     */
    public static function isSuiteSuperAdmin(): bool
    {
        return self::userInGroup('KjeBus: Business Support Suite:Admin - SuperAdmin');
    }

    /**
     * May get a list of all companies.
     */
    public static function mayListAllCompanies(): bool
    {
        return self::isSuiteSuperAdmin()
            || self::userInGroup('KjeBus: Business Support Suite:Admin - Info');
    }

    /**
     * May modify the given company (own company admin or suite super admin).
     */
    public static function canEditCompany(?object $company): bool
    {
        if (self::isSuiteSuperAdmin()) {
            return true;
        }

        if (!$company || empty($company->name)) {
            return false;
        }

        return self::userInGroup('KjeBus: ' . $company->name . ':Company - Admin');
    }

    /**
     * May see information about the given company.
     */
    public static function canViewCompany(?object $company): bool
    {
        if (self::mayListAllCompanies() || self::canEditCompany($company)) {
            return true;
        }

        if (!$company || empty($company->name)) {
            return false;
        }

        return self::userInGroup('KjeBus: ' . $company->name . ':Company - View');
    }
}
