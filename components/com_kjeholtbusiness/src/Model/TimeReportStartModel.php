<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Log\Log;

class TimeReportStartModel extends FormModel
{
    public function getForm($data = array(), $loadData = true) {
        Log::add('TimeReportStartModel->getForm ', Log::DEBUG, 'com_kjeholtbusiness');
 
        $projectId = 0;
        $companyId = 1;
        
        /*
         * Create a temporary table to be used as source for the dropdown menu to choose project/subproject for the time report. 
         * This is a workaround to avoid having to do a join in the XML form definition, which is not supported by Joomla. 
         * The temporary table will be created with the following structure: 
         * id | title 
         */
        $user = Factory::getUser();
        $userId = $user->id;
        
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query = $db->setQuery("DROP TEMPORARY TABLE IF EXISTS kjeholtbusiness_TempSubProjects");
        $db->execute();
        
        $query = $db->setQuery("CREATE TEMPORARY TABLE  kjeholtbusiness_TempSubProjects (
                                    id INT PRIMARY KEY, 
                                    project_name VARCHAR(255) NOT NULL,
                                    subproject_name VARCHAR(255) NOT NULL,
                                    title VARCHAR(255) NOT NULL
                                    )");  
//        $db->setQuery($query);
        $db->execute();
        
        $query = $db->getQuery(true);
        $query = $db->setQuery("INSERT INTO kjeholtbusiness_TempSubProjects (id, project_name, subproject_name, title) VALUES ('0', 'Icke uppdragsspecifikt', '---', 'Icke uppdragsspecifikt')");
//        $db->setQuery($query);
        $db->execute();
  
        $queryStr = "INSERT INTO kjeholtbusiness_TempSubProjects (id, project_name, subproject_name, title)
                                  SELECT spr.id AS id,
                                         pr.name AS project_name,
                                         spr.name AS subproject_name,
                                         CONCAT(pr.name, ' : ', spr.name) AS title
                                  FROM #__kjeholtbusiness_projects AS pr,
                                       #__kjeholtbusiness_subprojects AS spr
                                  WHERE pr.id = spr.project_id AND 
                                        pr.status = 'ongoing' AND 
                                        (spr.status = 'ongoing' OR spr.status = 'not_started') AND
                                        pr.company_id = " . $companyId; 

//        Log::add('TRS: insert query: ' . $queryStr, Log::DEBUG, 'com_kjeholtbusiness');
        
        
        $query = $db->getQuery(true);
        $query = $db->setQuery($queryStr); 
//        $db->setQuery($query);
        $db->execute();
        
        $form = $this->loadForm( 
            'com_kjeholtbusiness.time',   // just a unique name to identify the form
            'timereporting_start',              // the filename of the XML form definition
                                    // Joomla will look in the site/forms folder for this file
            array(
                'control' => 'jform',    // the name of the array for the POST parameters
                'load_data' => $loadData // if set to true, then there will be a callback to
                                         // loadFormData to supply the data
            )
            );
        
        if (empty($form))
        {
            $errors = $this->getErrors();
            throw new \Exception(implode("\n", $errors), 500);
        }
        
        return $form;
    }
    
    protected function loadFormData()
    {
        Log::add('TimeReportModel->loadFormData ', Log::DEBUG, 'com_kjeholtbusiness');     
        
        $projectId = 0;
        $companyId = 1;

        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_kjeholtbusiness.time',  // a unique name to identify the data in the session
            array("timereporting_name" => "Tidkort: " . Factory::getDate()->format('Y-m-d'),
                "timereporting_date" => Factory::getDate()->format('Y-m-d'),
                "timereporting_start" => Factory::getDate()->format('H:i')
                  )
             // prefill data if no data found in session
            );
        
        return $data;
    }
    
    public function storeTimeReport($commonData, $detailedData) {
        Log::add('TimeReportModel->storeTimeReport ', Log::DEBUG, 'com_kjeholtbusiness');
        
        $data = new \stdClass();
        $data->name = isset($commonData['name']) ? $commonData['name'] : 'odefinierat uppdrag';
        $data->description = 'Tidrapporten är kopplad till deluppdraget med id=' . $commonData['subproject_id'] . '.';
        $data->subproject_id = isset($commonData['subproject_id']) ? (int)$commonData['subproject_id'] : 0;
        
        if ($commonData->detail_selection == '1') {
            $data->start_time = Factory::getDate()->format('Y-m-d H:i');
        }
        else {
            $data->start_time = isset($detailedData['timereport_date']) && isset($detailedData['timereport_start_time']) ? 
                                    $detailedData['timereport_date'] . ' ' . $detailedData['timereport_start_time'] : 
                                    Factory::getDate()->format('Y-m-d H:i');
            
            if ($detailedData['timereport_finalize_selection'] > '1') {
                $data->end_time = isset($detailedData['timereport_date']) && isset($detailedData['timereport_end_time']) ? 
                                        $detailedData['timereport_date'] . ' ' . $detailedData['timereport_end_time'] : 
                                        Factory::getDate()->format('Y-m-d H:i');
            }
        }
        $db = Factory::getDbo();
        
        $db->insertObject('#__kjeholtbusiness_timecards', $data);
        
        return($db->insertid());
    }
    
    public function getTimeReports($allReports=TRUE) {
        Log::add('TimeReportModel->getTimeReports ', Log::DEBUG, 'com_kjeholtbusiness');
        
        return(null);
    }
}