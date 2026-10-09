<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

/**
 * Project/subproject ACL helper.
 *
 * A user may modify a project or its subprojects when they:
 *  - created the record (owner), or
 *  - are member of the "<Company>:Project - Admin" usergroup for the
 *    company owning the project.
 */
class ProjectAcl
{
    private static function userInGroupLike(string $pattern): bool
    {
        $user = Factory::getUser();

        if ($user->guest) {
            return false;
        }

        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' LIKE :title')
            ->bind(':title', $pattern);

        $groupIds = $db->setQuery($query)->loadColumn() ?: [];

        if (!$groupIds) {
            return false;
        }

        $userGroups = \array_map('intval', $user->getAuthorisedGroups());

        foreach ($groupIds as $groupId) {
            if (\in_array((int) $groupId, $userGroups, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * May the current user modify the given project?
     * Owner (created_by) or Project - Admin group member.
     */
    public static function canEditProject(?object $project): bool
    {
        if (!$project) {
            return false;
        }

        $user = Factory::getUser();

        if ((int) ($project->created_by ?? 0) === (int) $user->id && (int) $user->id > 0) {
            return true;
        }

        return self::userInGroupLike('%:Project - Admin');
    }

    /**
     * May the current user modify the given subproject?
     * Owner of the subproject or its parent project, or Project - Admin.
     */
    public static function canEditSubproject(?object $subproject): bool
    {
        if (!$subproject) {
            return false;
        }

        $user  = Factory::getUser();
        $owner = (int) ($subproject->created_by ?? 0) === (int) $user->id && (int) $user->id > 0;

        if ($owner) {
            return true;
        }

        // Fall back to the parent project owner
        if (!empty($subproject->project_id)) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('created_by'))
                ->from($db->quoteName('#__kjeholtbusiness_projects'))
                ->where($db->quoteName('id') . ' = :pid')
                ->bind(':pid', (int) $subproject->project_id, \Joomla\Database\ParameterType::INTEGER);
            $projectOwner = $db->setQuery($query)->loadResult();

            if ((int) $projectOwner === (int) $user->id && (int) $user->id > 0) {
                return true;
            }
        }

        return self::userInGroupLike('%:Project - Admin');
    }
}
