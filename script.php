<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Access\UserGroups;


class Com_KjeholtbusinessInstallerScript
{
    private function createCompany($companyName) {
        $db = Factory::getDbo();
        
        // Kontrollera om företaget redan finns

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__kjeholtbusiness_companies'))
            ->where($db->quoteName('name') . ' = ' . $db->quote($companyName));
        
        Log::add('Check to see if company "' . $companyName . '" exist in the database', Log::DEBUG, 'com_kjeholtbusiness');
            
            
        $companyId = $db->setQuery($query)->loadResult();
        
        if ($companyId) {
            return $companyId;
        }
        
        $newCompany = new stdClass();
        $newCompany->name = $companyName;
        
        $db->insertObject('#__kjeholtbusiness_companies', $newCompany);        
        $companyId = $db->insertid();
        
        return $companyId;
    }
    
    private function createACL($aclGroupName, $userGroupId) {
        $db = Factory::getDbo();
        
        // Kontrollera om gruppen redan finns
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__viewlevels'))
            ->where($db->quoteName('title') . ' = ' . $db->quote($aclGroupName));
        $db->setQuery($query);
        $groupId = $db->loadResult();
        
        // Skapa gruppen om den inte finns
        if (!$groupId)
        {
            $group = new stdClass();
            $group->title = $aclGroupName;
            $group->rules = '[' . $userGroupId . ']';
            $db->insertObject('#__viewlevels', $group);
            return $db->insertid();
        }
        
        return $groupId;
    }
    
    private function createUserGroup($name, $parentId = 1)
    {
        $db = Factory::getDbo();
        
        // Kontrollera om gruppen redan finns
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' = ' . $db->quote($name));
        $db->setQuery($query);
        $groupId = $db->loadResult();
        
        // Skapa gruppen om den inte finns
        if (!$groupId)
        {
            $group = new stdClass();
            $group->title = $name;
            $group->parent_id = $parentId;
            $db->insertObject('#__usergroups', $group);
            return $db->insertid();
        }
        
        return $groupId;
    }

    // Get ACL (viewlevel) ID from ACL name (title)
    private function getAclIdByName($aclName) {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__viewlevels'))
            ->where($db->quoteName('title') . ' = ' . $db->quote($aclName));
        $db->setQuery($query);
        return $db->loadResult();
    }

    public function install($parent)
    {
        $db = Factory::getDbo();
        $app = Factory::getApplication();
        
        // Logga att installationen startar
        Log::add('Startar installation mha script.se', Log::INFO, 'com_kjeholtbusiness');
        
        // Sökväg till SQL-filen
        $sqlFilePath = __DIR__ . '/administrator/components/com_kjeholtbusiness/sql/install.mysql.utf8.sql';
        
        // Kontrollera om SQL-filen finns
        if (!file_exists($sqlFilePath))
        {
            Log::add('SQL-filen hittades inte: ' . $sqlFilePath, Log::ERROR, 'com_kjeholtbusiness');
            $app->enqueueMessage('SQL-filen hittades inte: ' . $sqlFilePath, 'error');
            return false;
        }
        
        // Läs innehållet i SQL-filen
        $buffer = file_get_contents($sqlFilePath);
        
        if ($buffer === false)
        {
            Log::add('Kunde inte läsa SQL-filen: ' . $sqlFilePath, Log::ERROR, 'com_kjeholtbusiness');
            $app->enqueueMessage('Kunde inte läsa SQL-filen: ' . $sqlFilePath, 'error');
            return false;
        }
        
        // Dela upp SQL-filen i enskilda frågor
        $queries = $db->splitSql($buffer);
        
        if (empty($queries))
        {
            Log::add('Inga SQL-frågor hittades i filen: ' . $sqlFilePath, Log::WARNING, 'com_kjeholtbusiness');
            $app->enqueueMessage('Inga SQL-frågor hittades i filen: ' . $sqlFilePath, 'warning');
            return true;
        }
        
        // Kör varje SQL-fråga
        foreach ($queries as $query)
        {
            $query = trim($query);
            if (!empty($query))
            {
                try
                {
                    $db->setQuery($query);
                    $db->execute();
                    Log::add('SQL-fråga kördes framgångsrikt: ' . substr($query, 0, 50) . '...', Log::DEBUG, 'com_kjeholtbusiness');
                }
                catch (Exception $e)
                {
                    Log::add('Fel vid körning av SQL-fråga: ' . $e->getMessage(), Log::ERROR, 'com_kjeholtbusiness');
                    $app->enqueueMessage('Fel vid körning av SQL-fråga: ' . $e->getMessage(), 'error');
                }
            }
        }

// ------------------------------------------------------------------
        // User groups (UG) - KjeEng-BSS scheme
        //
        //   UG: KjeEng-BSS:SuperAdmin
        //   UG: KjeEng-BSS:<CompanyName>:SuperAdmin
        //   UG: KjeEng-BSS:<CompanyName>:Admin
        //   UG: KjeEng-BSS:<CompanyName>:Economy
        //   UG: KjeEng-BSS:<CompanyName>:Employee
        //   UG: KjeEng-BSS:<CompanyName>:Visitor
        // ------------------------------------------------------------------
        // Only the structural groups are created - no companies or other
        // predefined content. Per-company groups are created when a company
        // is added by a suite SuperAdmin.
        $bssRootId = $this->createUserGroup('UG: KjeEng-BSS', 1);
        Log::add('UserGroup "UG: KjeEng-BSS" skapad/hittad med id=' . $bssRootId, Log::DEBUG, 'com_kjeholtbusiness');

        $suiteSuperAdminId = $this->createUserGroup('UG: KjeEng-BSS:SuperAdmin', $bssRootId);
        Log::add('UserGroup "UG: KjeEng-BSS:SuperAdmin" skapad/hittad med id=' . $suiteSuperAdminId, Log::DEBUG, 'com_kjeholtbusiness');

        // Create the global access levels (ACL: KjeEng-BSS:<Level>)
        // linked to the suite SuperAdmin group. When companies are created,
        // their profile groups are merged into the level rules.
        \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl::syncLevels();
        Log::add('Access levels (ACL: KjeEng-BSS:*) synkroniserade.', Log::DEBUG, 'com_kjeholtbusiness');

        Log::add('Installationen slutförd.', Log::DEBUG, 'com_kjeholtbusiness');
        $app->enqueueMessage('Installationen av com_kjeholtbusiness slutförd.', 'message');
    }
    
    public function uninstall($parent)
    {
        $db = Factory::getDbo();
        $app = Factory::getApplication();
        
        // Logga att avinstallationen startar
        Log::add('Startar avinstallation av com_kjeholtbusiness.', Log::INFO, 'com_kjeholtbusiness');
        
        // Sökväg till SQL-filen för avinstallation
        
//        $sqlFilePath = __DIR__ . '/administrator/components/com_kjeholtbusiness/sql/uninstall.mysql.utf8.sql';
        $sqlFilePath = __DIR__ . '/sql/uninstall.mysql.utf8.sql';
        
        // Kontrollera om SQL-filen finns
        if (!file_exists($sqlFilePath))
        {
            Log::add('SQL-filen för avinstallation hittades inte: ' . $sqlFilePath, Log::ERROR, 'com_kjeholtbusiness');
            $app->enqueueMessage('SQL-filen för avinstallation hittades inte: ' . $sqlFilePath, 'error');
            return false;
        }
        
        // Läs innehållet i SQL-filen
        $buffer = file_get_contents($sqlFilePath);
        
        if ($buffer === false)
        {
            Log::add('Kunde inte läsa SQL-filen för avinstallation: ' . $sqlFilePath, Log::ERROR, 'com_kjeholtbusiness');
            $app->enqueueMessage('Kunde inte läsa SQL-filen för avinstallation: ' . $sqlFilePath, 'error');
            return false;
        }
        
        // Dela upp SQL-filen i enskilda frågor
        $queries = $db->splitSql($buffer);
        
        if (empty($queries))
        {
            Log::add('Inga SQL-frågor för avinstallation hittades i filen: ' . $sqlFilePath, Log::WARNING, 'com_kjeholtbusiness');
            $app->enqueueMessage('Inga SQL-frågor för avinstallation hittades i filen: ' . $sqlFilePath, 'warning');
            return true;
        }
        
        // Kör varje SQL-fråga
        foreach ($queries as $query)
        {
            $query = trim($query);
            if (!empty($query))
            {
                try
                {
                    $db->setQuery($query);
                    $db->execute();
                    Log::add('SQL-fråga för avinstallation kördes framgångsrikt: ' . substr($query, 0, 50) . '...', Log::INFO, 'com_kjeholtbusiness');
                }
                catch (Exception $e)
                {
                    Log::add('Fel vid körning av SQL-fråga för avinstallation: ' . $e->getMessage(), Log::ERROR, 'com_kjeholtbusiness');
                    $app->enqueueMessage('Fel vid körning av SQL-fråga för avinstallation: ' . $e->getMessage(), 'error');
                }
            }
        }
        
        // Remove the component's user groups (UG: KjeEng-BSS*) via the API,
        // keeping the nested-set tree (lft/rgt) consistent
        $this->removeUserGroupsByPattern('UG: KjeEng-BSS%');
        $this->removeUserGroupsByPattern('KjeBus: %');
        
        Log::add('Avinstallationen av com_kjeholtbusiness slutförd.', Log::INFO, 'com_kjeholtbusiness');
        $app->enqueueMessage('Avinstallationen av com_kjeholtbusiness slutförd.', 'message');
    }

    /**
     * Remove all user groups whose title matches the given LIKE pattern,
     * children first, using the UserGroups API to keep the tree consistent.
     */
    private function removeUserGroupsByPattern(string $pattern): void
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('title') . ' LIKE :pattern')
            ->bind(':pattern', $pattern);

        $groupIds = $db->setQuery($query)->loadColumn() ?: [];

        if (!$groupIds) {
            return;
        }

        // Delete deepest groups first (higher rgt = deeper or later; sort by rgt DESC)
        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__usergroups'))
            ->where($db->quoteName('id') . ' IN (' . implode(',', array_map('intval', $groupIds)) . ')')
            ->order($db->quoteName('rgt') . ' DESC');

        $ordered = $db->setQuery($query)->loadColumn() ?: [];

        foreach ($ordered as $groupId) {
            try {
                UserGroups::delete((int) $groupId);
                Log::add('UserGroup med id=' . $groupId . ' borttagen.', Log::DEBUG, 'com_kjeholtbusiness');
            } catch (\Throwable $e) {
                Log::add('Kunde inte ta bort UserGroup id=' . $groupId . ': ' . $e->getMessage(), Log::ERROR, 'com_kjeholtbusiness');
            }
        }
    }
}
