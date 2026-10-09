<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Log\Log;

class AuthorityHelper {

    function __construct($db) {
        
    }
        public static function getACLnames($companyId)
        {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__viewlevels'))
                ->where($db->quoteName('title') . ' LIKE ' . $db->quote('UG: KjeEng-BSS' . $companyId . '%'));
            $db->setQuery($query);
            return $db->loadColumn();
        }
        public static function getACLgroupId($companyName, $profile) {
            $aclName = 'ACL: ' . $companyName . ' - ' . $profile;
            
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__viewlevels'))
                ->where($db->quoteName('title') . ' = ' . $db->quote($aclName));
            $db->setQuery($query);
            return $db->loadResult();
        }
        
        private static function _createACL($aclGroupName, $userGroupId) {
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
                $groupId = $db->insertid();
            }
            
            return $groupId;
        }
        
        private static function _createUG($userGroupName, $parentGroupId) {
            $db = Factory::getDbo();
            
            // Kontrollera om gruppen redan finns
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__usergroups'))
                ->where($db->quoteName('title') . ' = ' . $db->quote($userGroupName));
            $db->setQuery($query);
            $groupId = $db->loadResult();
            
            // Skapa gruppen om den inte finns
            if (!$groupId)
            {
                $group = new stdClass();
                $group->title = $userGroupName;
                $group->parent_id = $parentGroupId;
                $db->insertObject('#__usergroups', $group);
                $groupId = $db->insertid();
            }
            
            return $groupId;
        }
        public static function createUG($companyName) {
            
            Log::add('(Admin) AuthorityHelper->createUG: Skapa en UserGroup', Log::DEBUG, 'com_kjeholtbusiness');
                       
            $parentGroupId = $this->_createUG('UG: KjeEng-BSS', 1);
            
            Log::add('AuthorityHelper->createUG: UserGroup UG: "Kjeholt Business Support Suite" skapad/hittad med id=' . $parentGroupId, Log::DEBUG, 'com_kjeholtbusiness');

            $childGroupId = $this->_createUG('UG: ' . $companyName, $parentGroupId);
            
            Log::add('AuthorityHelper->createUG: UserGroup "UG: ' . $companyName . '" är nu skapad/hittad i UserGroup med id=' . $childGroupId, Log::DEBUG, 'com_kjeholtbusiness');
            
          
            // Skapa en uppsättning av UG fö de olika användarprofilerna
            
            $userProfiles = array('SuperAdmin',
                'CompanyAdmin',
                'ProjectAdmin', 'ProjectUser','ProjectViewer',
                'AccountingAdmin','AccountingUser','AccountingViewer');
            
            foreach ($userProfiles as $profile) {
                $groupTitle = 'UG: ' . $companyName . ' - ' . $profile;
                
                $groupId = $this->_createUG($groupTitle, $childGroupId);
                Log::add('AuthorityHelper->createUG: UserGroup "' . $groupTitle . '" är nu skapad/hittad i UserGroup med id=' . $groupId, Log::DEBUG, 'com_kjeholtbusiness');
                
                $aclId = $this->_createACL('ACL: ' . $companyName . ' - ' . $profile, $groupId);
                Log::add('AuthorityHelper->createACL: ACL "' . 'ACL: ' . $companyName . ' - ' . $profile . '" är nu skapad/hittad i ACL med id=' . $aclIdId, Log::DEBUG, 'com_kjeholtbusiness');
            }
            
        }
}