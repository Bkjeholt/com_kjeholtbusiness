<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Log\Log;
use Couchbase\Exception\BucketExistsException;

class ProjectShowModel extends BaseDatabaseModel
{
    private $db;
    
    function __construct($config = array())
    {
        Log::add('ProjectSHOWModel->__construct', Log::DEBUG, 'com_kjeholtbusiness');
        
        $this->db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        
        parent::__construct($config);
    }
    
    private function getTimeReportsForSubProject($subProjectId,$costPerHour = 1) {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        $result = new \stdClass();
        $summary = new \stdClass();
        
        $summaryHours = 0;
        
        $query
            ->select($db->quoteName('a.*'))
            ->from($db->quoteName('#__kjeholtbusiness_time_reports', 'a'))
            ->where($db->quoteName('subproject_id') . ' = :subproject_id')
            ->order($db->quoteName('id') . ' ASC')
            ->bind(':subproject_id', $subProjectId);
        
        $timeReportResultList = $db->loadObjectList();
        
        $result->details = $timeReportResultList;
        
        if (!$timeReportResultList) {
            Log::add('ProjectShowModel->getTimeReportsForSubProject no time reports found for subProjectId=' . $subProjectId, Log::DEBUG, 'com_kjeholtbusiness');
            
            return [];
        }
        
        foreach ($timeReportResultList as $timeReport) {
//            Log::add('ProjectShowModel->getTimeReportsForSubProject timeReport=' . htmlspecialchars(print_r($timeReport) . ' Accumulated hours=' . $summaryHours, true), Log::DEBUG, 'com_kjeholtbusiness');
            
            $summaryHours += $timeReport->end_time - $timeReport->start_time + ($timeReport->adjustment);
        }
        
        $summary->hours = $summaryHours;
        $summary->cost = $summaryHours * $costPerHour;
        
        $result->summary = $summary;
        Log::add('ProjectShowModel->getTimeReportsForSubProject result=' . htmlspecialchars(print_r($result), true), Log::DEBUG, 'com_kjeholtbusiness');
        
        return $result;
    }
    
    private function getExpensesForSubProject($subProjectId) {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        $query
            ->select($db->quoteName(['type', 'value']))
            ->from($db->quoteName('#__kjeholtbusiness_expencies', 'a'))
            ->where($db->quoteName('subproject_id') . ' = :subproject_id')
            ->order($db->quoteName('id') . ' ASC')
            ->bind(':subproject_id', $subProjectId);
        
        $expenseResultList = $db->loadObjectList();
        
        if (!$expenseResultList) {
            Log::add('ProjectShowModel->getExpensesForSubProject no expenses found for subProjectId=' . $subProjectId, Log::DEBUG, 'com_kjeholtbusiness');
            
            return [];
        }
        
        foreach ($expenseResultList as $expense) {
            Log::add('ProjectShowModel->getExpensesForSubProject expense=' . htmlspecialchars(print_r($expense, true)), Log::DEBUG, 'com_kjeholtbusiness');
        }
        
        return $expenseResultList;
    }
    
    private function getSubProjects($projectId) {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        
        $query
            ->select($db->quoteName('a.*'))
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'a'))
            ->where($db->quoteName('project_id') . ' = :project_id')
            ->order($db->quoteName('id') . ' ASC')
            ->bind(':project_id', $projectId);
        
        $subProjectResultList = $db->loadObjectList();
        
        if (!$subProjectResultList) {
            Log::add('ProjectShowModel->getSubProjects no subprojects found for projectId=' . $projectId, Log::DEBUG, 'com_kjeholtbusiness');
            
            return [];
        }
        
        foreach ($subProjectResultList as $subProject) {
            Log::add('ProjectShowModel->getSubProjects subproject=' . htmlspecialchars(print_r($subProject, true)), Log::DEBUG, 'com_kjeholtbusiness');
        
            $subProject->expenses = $this->getExpensesForSubProject($subProject->id);
            $subProject->timeReports = $this->getTimeReportsForSubProject($subProject->id, $subProject->hourly_rate);
        }
        
        return $subProjectResultList;
    }
    
    public function getProjectInfo() { 
        Log::add('ProjectShowModel->getProjectInfo ', Log::DEBUG, 'com_kjeholtbusiness');

        $db = $this->getDatabase();
        $projectId = Factory::getApplication()->input->getInt('projectid', 0);
        
        if ($projectId) {
            Log::add('ProjectShowModel->getProjectInfo projectId=' . $projectId, Log::DEBUG, 'com_kjeholtbusiness');
        } else {
            Log::add('ProjectShowModel->getProjectInfo no projectId provided', Log::DEBUG, 'com_kjeholtbusiness');
        }
        
        $query = $db->getQuery(true);

        $query
            ->select($db->quoteName('a.*'))
            ->from($db->quoteName('#__kjeholtbusiness_projects', 'a'))
            ->where($db->quoteName('id') . ' = :project_id')
            ->order($db->quoteName('id') . ' ASC')
            ->bind(':project_id', $projectId);
        
        // Reset the query using our newly populated query object.
        $db->setQuery($query);
        
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $projectResultObject = $db->loadObject();
        
        if (!$projectResultObject) {
            Log::add('ProjectShowModel->getProjectInfo no project found for projectId=' . $projectId, Log::DEBUG, 'com_kjeholtbusiness');
            
            return null;
        }
            
        Log::add('ProjectShowModel->getProjectInfo projectInfo=' . htmlspecialchars(print_r($projectResultObject, true)), Log::DEBUG, 'com_kjeholtbusiness');
        
        $subprojectQuery = $db->getQuery(true);
        
        $subprojectQuery
        ->select($db->quoteName('a.*'))
        ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'a'))
        ->where($db->quoteName('project_id') . ' = :project_id')
        ->order($db->quoteName('id') . ' ASC')
        ->bind(':project_id', $projectId);
        
        $subProjectResultList = $db->loadObjectList();
        
        if (!$subProjectResultList) {
            Log::add('ProjectShowModel->getProjectInfo no subprojects found for projectId=' . $projectId, Log::DEBUG, 'com_kjeholtbusiness');
            
            return $projectResultObject; // Return the project info even if there are no subprojects
            
        }
                    
        $projectResultObject->subprojects = $this->getSubProjects($projectId);
        return $projectResultObject;
    }
}