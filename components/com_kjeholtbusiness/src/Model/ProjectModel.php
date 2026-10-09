<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Log\Log;

class ProjectModel extends FormModel
{
    private $companyId;
    private $db;
    
    function __construct($config = array())
    {
        Log::add('ProjectModel->__construct', Log::DEBUG, 'com_kjeholtbusiness');
        
        $this->db = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        
        parent::__construct($config);
    }
    public function getForm($data = array(), $loadData = true) {
        Log::add('ProjectModel->getForm', Log::DEBUG, 'com_kjeholtbusiness');
        
//        $view = Factory::getApplication()->input->getCmd('view', 'default');
//        $layout = Factory::getApplication()->input->getCmd('layout', 'default');
//        Log::add('Current layout: ' . $view . ':' . $layout, Log::DEBUG, 'com_kjeholtbusiness');
        
        $formPath = JPATH_ROOT . '/components/com_kjeholtbusiness/forms/project.xml';
        
        Log::add('ProjectModel->getForm Forms:'. $formPath, Log::DEBUG, 'com_kjeholtbusiness');
        
        if (!file_exists($formPath)) {
            Log::add('project.xml not found at: ' . $formPath, Log::ERROR, 'com_kjeholtbusiness');
        } else {
            Log::add('project.xml found at: ' . $formPath, Log::DEBUG, 'com_kjeholtbusiness');
        }

        Log::add('ProjectModel->getForm->loadForm', Log::DEBUG, 'com_kjeholtbusiness');
        
        $form = $this->loadForm(
            'com_kjeholtbusiness.project_create',   // just a unique name to identify the form
            'project',              // the filename of the XML form definition
            array(
                'control' => 'jform',    // the name of the array for the POST parameters
                'load_data' => $loadData // if set to true, then there will be a callback to
                                         // loadFormData to supply the data
            )
        );
        if (empty($form))
        {
            $errors = $this->getErrors();
            Log::add('loadForm failed for project.xml. Errors: ' . print_r($errors, true), Log::ERROR, 'com_kjeholtbusiness');
            throw new \Exception(implode("\n", $errors), 500);
        }
        return $form;
    }
    
    protected function loadFormData()
    {
        Log::add('ProjectModel->loadFormData ', Log::DEBUG, 'com_kjeholtbusiness');
        
        $projectId = 0;
        $companyId = 1;
        
        
        // Check the session for previously entered form data.
        
        $data = Factory::getApplication()->getUserState(
            'com_kjeholtbusiness.project_create',
            [
                "id" => null,
                "name" => null,
                "property_name" => null,
                "project_status" => "preliminary",
                "start_date" => Factory::getDate()->format('Y-m-d'),
                "company_id" => $companyId,
                "subproject_selector" => "1",
                "subproject_subforms" => [],
            ]
        );

        return $data;
    }

    private function getTimeReportsForSubProject($subProjectId,$costPerHour = 1) {
        $db = $this->db;
        $query = $db->getQuery(true);
        
        $result = new \stdClass();
        $summary = new \stdClass();
        
        $summaryHours = 0;
        
        $query
        ->select($db->quoteName('a.*'))
        ->from($db->quoteName('#__kjeholtbusiness_timecards', 'a'))
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
            
            Log::add('ProjectShowModel->getTimeReportsForSubProject timeReport=' . htmlspecialchars(print_r($timeReport) . ' Accumulated hours=' . $summaryHours, true), Log::DEBUG, 'com_kjeholtbusiness');
            
            $summaryHours += $timeReport->end_time - $timeReport->start_time + ($timeReport->adjustment);
        }
        
        $summary->hours = $summaryHours;
        $summary->cost = $summaryHours * $costPerHour;
        
        $result->summary = $summary;
        Log::add('ProjectShowModel->getTimeReportsForSubProject result=' . htmlspecialchars(print_r($result), true), Log::DEBUG, 'com_kjeholtbusiness');
        
        return $result;
    }
    
    private function getExpensesForSubProject($subProjectId) {
        $db = $this->db;
        $query = $db->getQuery(true);
        
        $query
        ->select($db->quoteName(['type', 'value']))
        ->from($db->quoteName('#__kjeholtbusiness_expenses', 'a'))
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
        $db = $this->db;
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
            
            return null;
        }
        
        foreach ($subProjectResultList as $subProject) {
            Log::add('ProjectShowModel->getSubProjects subproject=' . htmlspecialchars(print_r($subProject, true)), Log::DEBUG, 'com_kjeholtbusiness');
            
            $subProject->expenses = $this->getExpensesForSubProject($subProject->id);
            $subProject->timeReports = $this->getTimeReportsForSubProject($subProject->id, $subProject->hourly_rate);
        }
        
        return $subProjectResultList;
    }
    
    public function getQQProjectInfo() {
        Log::add('ProjectModel->getProjectInfo ', Log::DEBUG, 'com_kjeholtbusiness');

        $projectId = Factory::getApplication()->input->getInt('projectid');
        
        if(!$projectId) {
            Log::add('ProjectModel->getProjectInfo no projectId provided', Log::DEBUG, 'com_kjeholtbusiness');
            
            return null;
        }
        
        Log::add('ProjectModel->getProjectInfo projectId=' . $projectId, Log::DEBUG, 'com_kjeholtbusiness');
        
        
//        $db = $this->getDatabase(); 
        $db =$this->db;
        $query = $db->getQuery(true);
        
        $query
        ->select('a.*')
        ->from($db->quoteName('#__kjeholtbusiness_projects','a'))
        ->where($db->quoteName('id') . ' LIKE :project_id')
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
                
        $projectResultObject->subprojects = $this->getSubProjects($projectId);

        Log::add('ProjectShowModel->getProjectInfo projectInfo (2)=' . htmlspecialchars(print_r($projectResultObject, true)), Log::DEBUG, 'com_kjeholtbusiness');
        
        return $projectResultObject;
        
    }
    public function getItem() { 
        Log::add('ProjectModel->item ', Log::DEBUG, 'com_kjeholtbusiness');

        $db = $this->getDatabase();
        
        switch (Factory::getApplication()->input->getCmd('layout', 'default')) {
            case 'show':
                $projectId = Factory::getApplication()->input->getInt('projectid');
                
                if(!$projectId) {
                    Log::add('ProjectModel->item show no projectId provided', Log::DEBUG, 'com_kjeholtbusiness');
                    
                    return null;
                }
                
                Log::add('ProjectModel->item show projectId=' . $projectId, Log::DEBUG, 'com_kjeholtbusiness');
                
                return $this->getProjectInfo($projectId);
                                
                break;
            case 'list' :
                Log::add('ProjectModel->item list', Log::DEBUG, 'com_kjeholtbusiness');
                
                $query = $db->getQuery(true);
                
                $query
                    ->select($db->quoteName(['id', 'name', 'description', 'start_date', 'property_name', 'status']))
                    ->from($db->quoteName('#__kjeholtbusiness_projects', 'a'))
                    ->order($db->quoteName('id') . ' ASC');
                
                // Reset the query using our newly populated query object.
                $db->setQuery($query);
                
                // Load the results as a list of stdClass objects (see later for more options on retrieving data).
                $projectResultList = $db->loadObjectList();

                Log::add('ProjectModel->item projectList=' . htmlspecialchars(print_r($projectResultList)), Log::DEBUG, 'com_kjeholtbusiness');
                
                break;
            default:
                Log::add('ProjectModel->item default', Log::DEBUG, 'com_kjeholtbusiness');
                
                ;
            break;
        }

    }
}