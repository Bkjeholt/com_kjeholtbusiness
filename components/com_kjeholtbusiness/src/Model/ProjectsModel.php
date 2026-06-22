<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Log\Log;

class ProjectsModel extends ListModel
{
    public function getListQuery() {
        $app = Factory::getApplication();
        
        $companyId = $app->getUserState('com_kjeholtbusiness.company.id', null);

        Log::add('ProjectsModel->getListQuery: companyId='.$companyId, Log::DEBUG, 'com_kjeholtbusiness');
        
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        $query->select('a.*')
              ->from($db->quoteName('#__kjeholtbusiness_projects', 'a'))
              ->where($db->quoteName('company_id') . ' LIKE :company_id')
              ->order($db->quoteName('id') . ' ASC')
              ->bind(':company_id', $companyId);
        
        $db->setQuery($query);
//        $row = $db->loadAssocList();
        Log::add('ProjectsModel->getListQuery result= ' , Log::DEBUG, 'com_kjeholtbusiness');
//        print_r($row);
        
//        return $row;
        return $query;      
        
    }
}

