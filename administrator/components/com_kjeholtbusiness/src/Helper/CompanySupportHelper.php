<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Log\Log;

class CompanySupportHelper {
    
        public function getCompanyInfo($companyId)
        {
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('*'))
                ->from($db->quoteName('#__kjeholtbusiness_companies'))
                ->where($db->quoteName('id') . ' = ' . $db->quote($companyId));
            $db->setQuery($query);
            $companyInfoResult = $db->loadAssoc();
            
            if ($companyInfoResult)
            {
                $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__viewlevels'))
                ->where($db->quoteName('title') . ' LIKE ' . $db->quote('ACL: ' . $companyInfoResult->name . '%'));
                $db->setQuery($query);
                $companyInfoResult['aclGroups'] = $db->loadAssocList();
            }
            else
            {
                return null; // eller hantera det på annat sätt, t.ex. kasta ett undantag
            }
        }
        /**
        * Kontrollerar om den inloggade användaren har behörighet att se företaget.
        * Behörigheten baseras på att användaren är medlem i en specifik ACL-grupp.
        * Returnerar true om användaren har behörighet, annars false.
        */
        public static function userHasCompanyAccess($companyId)
        {
            $user = Factory::getUser();
            
            // Kontrollera om användaren är gäst
            if ($user->guest)
            {
                return false;
            }
            
            // Hämta ACL-gruppen som är kopplad till företaget
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('id'))
                ->from($db->quoteName('#__usergroups'))
                ->where($db->quoteName('title') . ' = ' . $db->quote('ACL: Företag ' . $companyId));
            $db->setQuery($query);
            $groupId = $db->loadResult();
            
            if (!$groupId)
            {
                // Om ingen ACL-grupp hittades för företaget, så finns det ingen åtkomst
                return false;
            }
            
            // Kontrollera om användaren är medlem i ACL-gruppen
            return in_array($groupId, $user->getAuthorisedGroups());
        }
}