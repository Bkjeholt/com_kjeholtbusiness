<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Database\ParameterType;

/**
 * Project/subproject ACL based on the KjeEng-BSS access levels.
 *
 * project:edit - UG <Company>:Admin, :Economy (+ company/ suite SuperAdmin)
 * project:view - UG <Company>:Admin, :Economy, :Employee, :Visitor
 *
 * The owning company of a project is looked up via projects.company_id.
 * A user may always edit a project/subproject they created (owner).
 */
class ProjectAcl
{
    private static function companyNameForProject(int $projectId): ?string
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('c.name'))
            ->from($db->quoteName('#__kjeholtbusiness_projects', 'p'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_companies', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('p.company_id'))
            ->where($db->quoteName('p.id') . ' = :pid')
            ->bind(':pid', $projectId, ParameterType::INTEGER);

        $name = $db->setQuery($query)->loadResult();

        return $name ?: null;
    }

    private static function companyNameForSubproject(int $subprojectId): ?string
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('c.name'))
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'sp'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_projects', 'p'), $db->quoteName('p.id') . ' = ' . $db->quoteName('sp.project_id'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_companies', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('p.company_id'))
            ->where($db->quoteName('sp.id') . ' = :sid')
            ->bind(':sid', $subprojectId, ParameterType::INTEGER);

        $name = $db->setQuery($query)->loadResult();

        return $name ?: null;
    }

    /**
     * May the current user modify the given project?
     * Owner (created_by) or the company's project:edit level.
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

        $companyName = self::companyNameForProject((int) $project->id);

        return $companyName !== null && BssAcl::hasAccess($companyName, 'project:edit');
    }

    /**
     * May the current user modify the given subproject?
     * Owner of the subproject or its parent project, or project:edit.
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

        // Parent project owner
        if (!empty($subproject->project_id)) {
            $db    = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('created_by'))
                ->from($db->quoteName('#__kjeholtbusiness_projects'))
                ->where($db->quoteName('id') . ' = :pid')
                ->bind(':pid', (int) $subproject->project_id, ParameterType::INTEGER);
            $projectOwner = $db->setQuery($query)->loadResult();

            if ((int) $projectOwner === (int) $user->id && (int) $user->id > 0) {
                return true;
            }
        }

        $companyName = self::companyNameForSubproject((int) $subproject->id);

        return $companyName !== null && BssAcl::hasAccess($companyName, 'project:edit');
    }
}
