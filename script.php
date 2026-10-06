<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Table;

class Com_KjeholtbusinessInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param  Installer  $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function install($parent)
    {
        $parent->getParent()->setRedirectURL('index.php?option=com_kjeholtbusiness');
        return true;
    }

    /**
     * Method to uninstall the component.
     *
     * @param  Installer  $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function uninstall($parent)
    {
        return true;
    }

    /**
     * Method to update the component.
     *
     * @param  Installer  $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function update($parent)
    {
        return true;
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param   string    $type    The type of change (install, update, discover_install, uninstall).
     * @param   Installer $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function preflight($type, $parent)
    {
        return true;
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param   string    $type    The type of change (install, update, discover_install, uninstall).
     * @param   Installer $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function postflight($type, $parent)
    {
        if ($type === 'install' || $type === 'update')
        {
            // Enable the plugin after installation or update
            $this->enablePlugin();
        }
        return true;
    }

    /**
     * Enable the content plugin after installation.
     */
    private function enablePlugin()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('element') . ' = ' . $db->quote('kjeholtbusiness'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('content'));

        try
        {
            $db->setQuery($query);
            $db->execute();
        }
        catch (Exception $e)
        {
            Log::add(Text::_('COM_KJEHOLTBUSINESS_ERROR_ENABLE_PLUGIN') . ': ' . $e->getMessage(), Log::ERROR, 'com_kjeholtbusiness');
        }
    }
}
use Joomla\CMS\Log\Log;

 Log::add('Installationsscriptet laddat', Log::INFO, 'com_kjeholtbusiness');

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

        $companyName = 'Företaget Test AB';
                
        Log::add('Skapa en UserGroup', Log::DEBUG, 'com_kjeholtbusiness');

        $parentUserGroupId = $this->createUserGroup('KjeBus: Business Support Suite', 1);

        Log::add('UserGroup "KjeBus: Business Support Suite" skapad/hittad med id=' . $parentUserGroupId, Log::DEBUG, 'com_kjeholtbusiness');
        
        $childGroupId = $this->createUserGroup('KjeBus: ' . $companyName, $parentUserGroupId);
        $companyId = $this->createCompany($companyName);
        
        Log::add('UserGroup "KjeBus: ' . $companyName . '" är nu skapad/hittad i UserGroup med id=' . $childGroupId, Log::DEBUG, 'com_kjeholtbusiness');
        
        // Skapa en uppsättning av UG fö de olika användarprofilerna
        
/*        $userProfiles = array('SuperAdmin',
            'CompanyAdmin','CompanyViewer',
            'ProjectAdmin', 'ProjectUser','ProjectViewer',
            'AccountingAdmin','AccountingUser','AccountingViewer');
  */      
        $userProfileGroups = array(
            array('grp' => 'Admin', 'profiles' => array('SuperAdmin')),
            array('grp' => 'Company', 'profiles' => array('Admin', 'Viewer')),
            array('grp' => 'Project', 'profiles' => array('Admin', 'User', 'Viewer')),
            array('grp' => 'Accounting', 'profiles' => array('Admin', 'User', 'Viewer'))
        );
        
/*        $userProfileGroups = [{'grp':'Admin', 'profiles': ['SuperAdmin']},
                              {'grp':'Company', 'profiles': ['Admin','Viewer']},
                              {'grp':'Project', 'profiles': ['Admin', 'User','Viewer']},
                              {'grp':'Accounting', 'profiles': ['Admin','User','Viewer']}];
*/
        foreach ($userProfileGroups as $userProfileGroup) {
            if ($userProfileGroup['grp'] == 'Admin') {
                $userGroupPrefix = 'KjeBus: Business Support Suite:Admin';
                $aclPrefix = 'KjeBus: Business Support Suite:Admin';
            } else {
                $userGroupPrefix = 'KjeBus: ' . $companyName . ':' . $userProfileGroup['grp'];
                $aclPrefix = 'KjeBus: ' . $companyName . ':' . $userProfileGroup['grp'];
            }
            
            $infoUserGroupId = $this->createUserGroup($userGroupPrefix . ' - Info', $childGroupId);
            
            Log::add('UserGroup "' . $userGroupPrefix . ' - Info'. '" är nu skapad/hittad i UserGroup med id=' . $infoUserGroupId, Log::DEBUG, 'com_kjeholtbusiness');
            
            $infoAclId = $this->createACL($aclPrefix . ' - Info', $infoUserGroupId);
            
            Log::add('ACL "' . $aclPrefix . ' - Info' . '" är nu skapad/hittad i ACL med id=' . $aclId, Log::DEBUG, 'com_kjeholtbusiness');
            
            foreach ($userProfileGroup['profiles'] as $profile) {
                $groupId = $this->createUserGroup($userGroupPrefix . ' - ' . $profile, $childGroupId);
                Log::add('UserGroup "' . $userGroupPrefix . ' - ' . $profile . '" är nu skapad/hittad i UserGroup med id=' . $groupId, Log::DEBUG, 'com_kjeholtbusiness');
                
                $aclId = $this->createACL($aclPrefix . ' - ' . $profile, $groupId);
                Log::add('ACL "' . $aclPrefix . ' - ' . $profile . '" är nu skapad/hittad i ACL med id=' . $aclId, Log::DEBUG, 'com_kjeholtbusiness');
            }
        }
/*        
        foreach ($userProfiles as $profile) {
            $groupId = $this->createUserGroup('UG: ' . $companyName . ' - ' . $profile, $childGroupId);
            Log::add('UserGroup "' . 'UG: ' . $companyName . ' - ' . $profile . '" är nu skapad/hittad i UserGroup med id=' . $groupId, Log::DEBUG, 'com_kjeholtbusiness');
            
            $aclId = $this->createACL('ACL: ' . $companyName . ' - ' . $profile, $groupId);
            Log::add('ACL "' . 'ACL: ' . $companyName . ' - ' . $profile . '" är nu skapad/hittad i ACL med id=' . $aclId, Log::DEBUG, 'com_kjeholtbusiness');
        }
  */      
    
        
        
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
        
        // Uninstall av ACL-grupper och UserGroups kan gör
        
        Log::add('Avinstallationen av com_kjeholtbusiness slutförd.', Log::INFO, 'com_kjeholtbusiness');
        $app->enqueueMessage('Avinstallationen av com_kjeholtbusiness slutförd.', 'message');
    }
}
