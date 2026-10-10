<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Log\Log;

class ProjectsModel extends ListModel
{
    public function getItems() { /* Changed from getListQuery() to getList() to match the method name in the ListModel class */
        $app = Factory::getApplication();
        
        $companyId = \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser::companyId();

        Log::add('ProjectsModel->getListQuery: companyId (from cookie)='.$companyId, Log::DEBUG, 'com_kjeholtbusiness');
        
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        $query->select('a.*')
              ->from($db->quoteName('#__kjeholtbusiness_projects', 'a'))
              ->where($db->quoteName('company_id') . ' LIKE :company_id')
              ->order($db->quoteName('id') . ' ASC')
              ->bind(':company_id', $companyId);
        
        $db->setQuery($query);
        $rows = $db->loadAssocList();
//        Log::add('ProjectsModel->getListQuery result= ' . htmlspecialchars(print_r($rows, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
//        Log::add('ProjectsModel->getListQuery test result= ' . htmlspecialchars(print_r($rows[0], true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
//        print_r($row);
        
        foreach ($rows as $i => $row) {
            $projectId = $row['id'];
            
            Log::add('ProjectsModel->getListQuery->row result with projectId ('. $projectId.')= ' . htmlspecialchars(print_r($row, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
            
            
            $query = $db->getQuery(true);
            $query->select('b.*')
				  ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'b'))
				  ->where($db->quoteName('project_id') . ' = :project_id')
				  ->order($db->quoteName('id') . ' ASC')
				  ->bind(':project_id', $projectId);
            $db->setQuery($query);
            $rows[$i]['subprojects'] = $db->loadAssocList();

//            Log::add('ProjectsModel->getListQuery->row result= ' . htmlspecialchars(print_r($row, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
        }

//        Log::add('ProjectsModel->getListQuery modified result= ' . htmlspecialchars(print_r($rows, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
        return $rows;      
        
    }
}

