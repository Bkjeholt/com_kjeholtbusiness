<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * KjeEng-BSS access level (view level) helper.
 *
 * SIMPLIFIED SCHEME:
 * One global set of access levels named "ACL: KjeEng-BSS:<Level>".
 * Company isolation is handled through the company_users table
 * (users are connected to a company and the data queries are
 * scoped to that company), so per-company levels are not needed.
 *
 * Each level's rules include:
 *   UG: KjeEng-BSS:SuperAdmin
 *   UG: KjeEng-BSS:<AnyCompany>:SuperAdmin
 * plus the profile groups of ALL companies as defined in the matrix.
 *
 * The matrix maps each level to the company profiles (relative to
 * UG: KjeEng-BSS:<Company>:<Profile>) that hold it.
 */
class BssAcl
{
    /**
     * Level => member profiles.
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

    public static function levelTitle(string $level): string
    {
        return 'ACL: KjeEng-BSS:' . $level;
    }

    /**
     * True when the current user holds the given access level.
     * (Levels are global; company isolation comes from the
     * company_users table and data scoping.)
     */
    public static function hasAccess(string $companyName, string $level): bool
    {
        return self::hasAccessAny($level);
    }

    /**
     * True when the current user holds the given access level.
     */
    public static function hasAccessAny(string $level): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        $levelId = self::levelId(self::levelTitle($level));

        if (!$levelId) {
            return false;
        }

        return \in_array((int) $levelId, \array_map('intval', $user->getAuthorisedViewLevels()), true);
    }

    /**
     * (Re)create the global access levels, linking them to the
     * profile user groups of ALL existing companies per the matrix.
     * Called on install and whenever a company is created.
     *
     * $extraProfileGroupIds maps profile name to a user group id of a
     * newly created company, merged into the global rules.
     */
    public static function syncLevels(array $extraProfileGroupIds = []): void
    {
        $db = Factory::getDbo();

        $suiteSuperAdminId = self::suiteSuperAdminGroupId();

        // Collect all company profile groups: UG: KjeEng-BSS:<Company>:<Profile>
        $query = $db->getQuery(true)
            ->select([$db->quoteName('id'), $db->quoteName('title')])
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' LIKE :pattern');

        $pattern = 'UG: KjeEng-BSS:%';
        $query->bind(':pattern', $pattern);

        $rows = $db->setQuery($query)->loadObjectList() ?: [];

        $profileGroupIds = [];

        foreach ($rows as $row) {
            // "UG: KjeEng-BSS:<Company>:<Profile>" -> [Company, Profile]
            $parts = \explode(':', $row->title);

            if (\count($parts) !== 4) {
                continue;
            }

            $profile = $parts[3];

            $profileGroupIds[$profile][] = (int) $row->id;
        }

        // Merge groups of a newly created company
        foreach ($extraProfileGroupIds as $profile => $groupId) {
            $profileGroupIds[$profile][] = (int) $groupId;
        }

        foreach (self::$matrix as $level => $profiles) {
            $groupIds = [];

            if ($suiteSuperAdminId) {
                $groupIds[] = (int) $suiteSuperAdminId;
            }

            // All company SuperAdmin groups
            foreach ($profileGroupIds['SuperAdmin'] ?? [] as $groupId) {
                $groupIds[] = $groupId;
            }

            foreach ($profiles as $profile) {
                foreach ($profileGroupIds[$profile] ?? [] as $groupId) {
                    $groupIds[] = $groupId;
                }
            }

            $title      = self::levelTitle($level);
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

    /**
     * Kept for backwards compatibility with earlier call sites.
     * Creates/refreshes the global levels, optionally merging a
     * new company's profile groups.
     */
    public static function createCompanyLevels(string $companyName, array $profileGroupIds): void
    {
        self::syncLevels($profileGroupIds);
    }

    private static function levelId(string $title): int
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__viewlevels'))
            ->where($db->quoteName('title') . ' = :title');

        $query->bind(':title', $title);

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
