<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

/**
 * Company-user connection helper.
 *
 * Maps users to companies via #__kjeholtbusiness_company_users.
 * Current model: one user belongs to exactly one company
 * (unique key on user_id); a company has many users.
 *
 * When multi-company per user is introduced later, the
 * unique key is dropped and these methods return arrays -
 * all call sites already go through this helper, so the
 * expansion will be contained here.
 */
class CompanyUser
{
    /**
     * The id of the company the current (or given) user belongs to.
     * Null when the user is not connected to any company.
     */
    public static function companyId(?int $userId = null): ?int
    {
        $userId = $userId ?? (int) Factory::getUser()->id;

        if (!$userId) {
            return null;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('company_id'))
            ->from($db->quoteName('#__kjeholtbusiness_company_users'))
            ->where($db->quoteName('user_id') . ' = :user_id')
            ->bind(':user_id', $userId, ParameterType::INTEGER);

        $companyId = $db->setQuery($query)->loadResult();

        return $companyId !== null ? (int) $companyId : null;
    }

    /**
     * The name of the company the current user belongs to. Null if none.
     */
    public static function companyName(?int $userId = null): ?string
    {
        $companyId = self::companyId($userId);

        if (!$companyId) {
            return null;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('name'))
            ->from($db->quoteName('#__kjeholtbusiness_companies'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $companyId, ParameterType::INTEGER);

        $name = $db->setQuery($query)->loadResult();

        return $name ?: null;
    }

    /**
     * The name of the company with the given id. Null if not found or deleted.
     */
    public static function companyNameForId(int $companyId): ?string
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('name'))
            ->from($db->quoteName('#__kjeholtbusiness_companies'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $companyId, ParameterType::INTEGER);

        $name = $db->setQuery($query)->loadResult();

        return $name ?: null;
    }

    /**
     * All user ids connected to the given company.
     *
     * @return int[]
     */
    public static function companyUserIds(int $companyId): array
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('user_id'))
            ->from($db->quoteName('#__kjeholtbusiness_company_users'))
            ->where($db->quoteName('company_id') . ' = :company_id')
            ->bind(':company_id', $companyId, ParameterType::INTEGER);

        return \array_map('intval', $db->setQuery($query)->loadColumn() ?: []);
    }

    /**
     * True when the current user belongs to the given company.
     */
    public static function belongsTo(int $companyId, ?int $userId = null): bool
    {
        return self::companyId($userId) === $companyId;
    }

    /**
     * The permission flags (company_users.permissions SET) of the
     * given user. Empty when the user is not connected to a company.
     *
     * @return string[]
     */
    public static function permissions(int $userId): array
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('permissions'))
            ->from($db->quoteName('#__kjeholtbusiness_company_users'))
            ->where($db->quoteName('user_id') . ' = :user_id')
            ->bind(':user_id', $userId, ParameterType::INTEGER);

        $raw = $db->setQuery($query)->loadResult();

        if (!$raw) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $raw))));
    }

    /**
     * Replace the permission flags of a connected user.
     *
     * @param string[] $flags
     */
    public static function setPermissions(int $userId, array $flags): bool
    {
        $flags = array_values(array_intersect(BssAcl::FLAGS, $flags));

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__kjeholtbusiness_company_users'))
            ->set($db->quoteName('permissions') . ' = :permissions')
            ->where($db->quoteName('user_id') . ' = :user_id')
            ->bind(':permissions', implode(',', $flags))
            ->bind(':user_id', $userId, ParameterType::INTEGER);

        try {
            $db->setQuery($query)->execute();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Connect a user to a company. In the one-company model this
     * replaces any existing connection. The user is added to the
     * UG:KjeEng-BSS:User group. Returns true on success.
     */
    public static function connect(int $userId, int $companyId, array $permissions = []): bool
    {
        $db = Factory::getDbo();

        try {
            // One-company model: remove existing connection first
            $query = $db->getQuery(true)
                ->delete($db->quoteName('#__kjeholtbusiness_company_users'))
                ->where($db->quoteName('user_id') . ' = :user_id')
                ->bind(':user_id', $userId, ParameterType::INTEGER);
            $db->setQuery($query)->execute();

            $flags = array_values(array_intersect(BssAcl::FLAGS, $permissions));

            $row            = new \stdClass();
            $row->user_id   = $userId;
            $row->company_id = $companyId;
            $row->is_primary = 1;
            $row->permissions = implode(',', $flags);
            $row->created_by = (int) Factory::getUser()->id;
            $db->insertObject('#__kjeholtbusiness_company_users', $row, 'id');

            self::syncUserGroup($userId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove a user's company connection and the UG:KjeEng-BSS:User
     * group membership.
     */
    public static function disconnect(int $userId): bool
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->delete($db->quoteName('#__kjeholtbusiness_company_users'))
            ->where($db->quoteName('user_id') . ' = :user_id')
            ->bind(':user_id', $userId, ParameterType::INTEGER);

        try {
            $db->setQuery($query)->execute();
            self::syncUserGroup($userId);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Add the user to (or remove from) the UG:KjeEng-BSS:User group
     * depending on whether they are connected to a company.
     */
    private static function syncUserGroup(int $userId): void
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = :title');
        $query->bind(':title', CompanyAcl::GROUP_USER);
        $groupId = (int) $db->setQuery($query)->loadResult();

        if (!$groupId) {
            return;
        }

        $user = Factory::getUser($userId);

        if ($user->guest) {
            return;
        }

        $groups = array_map('intval', $user->groups ?: []);
        $connected = self::companyId($userId) !== null;

        if ($connected && !in_array($groupId, $groups, true)) {
            $user->groups = array_merge($groups, [$groupId]);
            $user->save();
        } elseif (!$connected && in_array($groupId, $groups, true)) {
            $user->groups = array_values(array_diff($groups, [$groupId]));
            $user->save();
        }

        Factory::getUser($userId)->clearAccessRights();
    }
}
