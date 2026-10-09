<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * KjeEng-BSS access level (view level) helper.
 *
 * Access levels are Joomla view levels named "ACL: KjeEng-BSS:<Company>:<Level>"
 * and are linked to the component's user groups (UG) as follows.
 *
 * Every per-company level implicitly includes:
 *   UG: KjeEng-BSS:SuperAdmin  (suite super admin may do everything)
 *   UG: KjeEng-BSS:<Company>:SuperAdmin
 * plus the profiles listed in the matrix below.
 */
class BssAcl
{
    /**
     * Level => member profiles (relative to UG: KjeEng-BSS:<Company>:<Profile>).
     */
    private static $matrix = [
        'Admin'            => ['Admin', 'Economy'],
        'view'             => ['Admin'],
        'project:edit'     => ['Admin', 'Economy'],
        'project:view'     => ['Admin', 'Economy', 'Employee', 'Visitor'],
        'timereport:edit'  => ['Admin', 'Employee'],
        'timereport:view'  => ['Admin', 'Employee', 'Visitor'],
        'expense:edit'     => ['Admin', 'Economy'],
        'expense:view'     => ['Admin', 'Economy', 'Employee'],
        'invoice:approve'  => ['Admin', 'Economy'],
        'invoice:edit'     => ['Admin', 'Economy'],
    ];

    public static function levels(): array
    {
        return \array_keys(self::$matrix);
    }

    public static function levelTitle(string $companyName, string $level): string
    {
        return 'ACL: KjeEng-BSS:' . $companyName . ':' . $level;
    }

    /**
     * True when the current user holds the given access level for the company.
     */
    public static function hasAccess(string $companyName, string $level): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        $levelId = self::levelId(self::levelTitle($companyName, $level));

        if (!$levelId) {
            return false;
        }

        return \in_array((int) $levelId, \array_map('intval', $user->getAuthorisedViewLevels()), true);
    }

    /**
     * True when the current user holds the given access level for ANY company.
     * (A user normally belongs to one company, so this is equivalent in practice.)
     */
    public static function hasAccessAny(string $level): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__viewlevels'))
            ->where($db->quoteName('title') . ' LIKE :title');

        $title = 'ACL: KjeEng-BSS:%:' . $level;
        $query->bind(':title', $title);

        $levelIds = \array_map('intval', $db->setQuery($query)->loadColumn() ?: []);

        if (!$levelIds) {
            return false;
        }

        $userLevels = \array_map('intval', $user->getAuthorisedViewLevels());

        foreach ($userLevels as $userLevel) {
            if (\in_array($userLevel, $levelIds, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create the access levels for a company, based on the matrix.
     *
     * $profileGroupIds maps profile name (SuperAdmin/Admin/...) to the
     * user group id of "UG: KjeEng-BSS:<Company>:<Profile>".
     */
    public static function createCompanyLevels(string $companyName, array $profileGroupIds): void
    {
        $db = Factory::getDbo();

        $suiteSuperAdminId = self::suiteSuperAdminGroupId();

        foreach (self::$matrix as $level => $profiles) {
            $groupIds = [];

            if ($suiteSuperAdminId) {
                $groupIds[] = (int) $suiteSuperAdminId;
            }

            if (!empty($profileGroupIds['SuperAdmin'])) {
                $groupIds[] = (int) $profileGroupIds['SuperAdmin'];
            }

            foreach ($profiles as $profile) {
                if (!empty($profileGroupIds[$profile])) {
                    $groupIds[] = (int) $profileGroupIds[$profile];
                }
            }

            $title = self::levelTitle($companyName, $level);
            $existingId = self::levelId($title);

            if ($existingId) {
                $row        = new \stdClass();
                $row->id    = (int) $existingId;
                $row->rules = \json_encode(\array_values(\array_unique($groupIds)));
                $db->updateObject('#__viewlevels', $row, 'id');
            } else {
                $row        = new \stdClass();
                $row->title = $title;
                $row->rules = \json_encode(\array_values(\array_unique($groupIds)));
                $db->insertObject('#__viewlevels', $row);
            }
        }
    }

    private static function levelId(string $title): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__viewlevels'))
            ->where($db->quoteName('title') . ' = :title')
            ->bind(':title', $title);

        return (int) $db->setQuery($query)->loadResult();
    }

    private static function suiteSuperAdminGroupId(): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = :title');

        $suiteSuperAdminTitle = 'UG: KjeEng-BSS:SuperAdmin';
        $query->bind(':title', $suiteSuperAdminTitle);

        return (int) $db->setQuery($query)->loadResult();
    }
}
