<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Company ACL helper.
 *
 * Maps actions to the KjeBus usergroups:
 *  - Create/modify any company:   "UG: KjeEng-BSS:SuperAdmin"
 *  - List all companies:          "UG: KjeEng-BSS:SuperAdmin"
 *  - Modify own company:          "UG: KjeEng-BSS:<Company>:Admin"
 *  - Show own company information "UG: KjeEng-BSS:<Company>:Visitor"
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
        return self::userInGroup('UG: KjeEng-BSS:SuperAdmin');
    }

    /**
     * May get a list of all companies.
     */
    public static function mayListAllCompanies(): bool
    {
        return self::isSuiteSuperAdmin()
            || self::userInGroup('UG: KjeEng-BSS:SuperAdmin');
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

        return self::userInGroup('UG: KjeEng-BSS:' . $company->name . ':Admin');
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

        return self::userInGroup('UG: KjeEng-BSS:' . $company->name . ':Visitor');
    }
}
