<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;

class ProjectController extends FormController
{
    private $companyId;
    
    private function storeNewSubproject($projectId, $subprojectData) {
        Log::add('ProjectController->storeNewSubproject projectId='. $projectId . ' subprojectData='.htmlspecialchars(print_r($subprojectData, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');

        $user = Factory::getUser();
        
        $data = new \stdClass();
        $data->name = isset($subprojectData['name']) ? $subprojectData['name'] : 'Automatgenererat deluppdrag';
        $data->description = isset($subprojectData['description']) ? $subprojectData['description'] : 'Deluppdraget är kopplat till uppdraget med id=' . $projectId . '.';
        $data->hourly_rate = isset($subprojectData['hourly_rate']) ? (float)$subprojectData['hourly_rate'] : 900.0;
        $data->esimated_amount_of_hours = isset($subprojectData['esimated_amount_of_hours']) ? (float)$subprojectData['esimated_amount_of_hours'] : 0;    
//        $data->status = isset($subprojectData['status']) ? $subprojectData['status'] : 'not_started';
        $data->start_date = isset($subprojectData['start_date']) ? $subprojectData['start_date'] : Factory::getDate()->format('Y-m-d');
        $data->end_date = isset($subprojectData['end_date']) ? $subprojectData['end_date'] : null;
        $data->project_id = (int)$projectId;

        // ACL
        
        $data->created_by = $user->id;
        $data->modified_by = $user->id;
        
        $db = Factory::getDbo();
        $db->insertObject('#__kjeholtbusiness_subprojects', $data);
        return($db->insertid());
    }
    
    
    private function storeNewProject($projectData) {
        Log::add('ProjectController->storeNewProject projectData='.htmlspecialchars(print_r($projectData, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');

        $user = Factory::getUser();
        
        $data = new \stdClass();
        $data->name = isset($projectData['name']) ? $projectData['name'] : 'odefinierat uppdrag';
        $data->description = isset($projectData['description']) ? $projectData['description'] : 'Tidrapporten är kopplad till deluppdraget med id=' . $commonData['subproject_id'] . '.';
        $data->property_name = isset($projectData['property_name']) ? $projectData['property_name'] : 'xxxxx xxxxx 1:xxx';
        $data->start_date = isset($projectData['start_date']) ? $projectData['start_date'] : Factory::getDate()->format('Y-m-d');
        $data->created_by = $user->id;
        $data->modified_by = $user->id;
        
        
        $db = Factory::getDbo();
        $db->insertObject('#__kjeholtbusiness_projects', $data);
        $projectId = $db->insertid();
        
        return($projectId);
    }
    
    public function submit($key = null, $urlVar = null)
    {
        Log::add('ProjectController: submit() called', Log::DEBUG, 'com_kjeholtbusiness');
        
        $this->checkToken();
        
        $app = Factory::getApplication();
        
        // die('ProjectController submit hit'); // Avkommentera för att testa direkt
//      $this->companyId = $app->getUserState('com_kjeholtbusiness.company.id', null);
        $companyId = $app->getUserState('com_kjeholtbusiness.company.id', null);
        
//        Log::add('ProjectController: submit: CompanyId='. $this->companyId, Log::DEBUG, 'com_kjeholtbusiness');
        Log::add('ProjectController: submit: CompanyId='. $companyId, Log::DEBUG, 'com_kjeholtbusiness');
        
        $model = $this->getModel('Project');
        $form = $model->getForm(null, false);
        if (!$form)
        {
            $app->enqueueMessage($model->getError(), 'error');
            return false;
        }

        // name of array 'jform' must match 'control' => 'jform' line in the model code
        $data  = $this->input->post->get('jform', array(), 'array');

        // This is validate() from the FormModel class, not the Form class
        // FormModel::validate() calls both Form::filter() and Form::validate() methods
        $validData = $model->validate($form, $data);

        if ($validData === false)
        {
            $errors = $model->getErrors();

            foreach ($errors as $error)
            {
                if ($error instanceof \Exception)
                {
                    $app->enqueueMessage($error->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($error, 'warning');
                }
            }
        }
        else
        {
            // do something with the valid data here, such as save it to the database
//            $db = Factory::getDbo();
            
            Log::add('ProjectController: submitS: ValidData=' . htmlspecialchars(print_r($validData, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
            
            $projectId = $this->storeNewProject($validData);
            $app->enqueueMessage("Uppdrag lagrat i databas med id=" . $projectId, 'notice');
            
            Log::add('ProjectController: submit: New project created with id= '. $projectId, Log::DEBUG, 'com_kjeholtbusiness');
            
            if ($validData['subproject_selector'] == '0') {

                $subProjectId = $this->storeNewSubproject($projectId, array());
                $app->enqueueMessage("Deluppdrag lagrat i databas med id=" . $subProjectId, 'notice');
                
                Log::add('ProjectController: submit: New autogenerated subproject created with id= '. $subProjectId, Log::DEBUG, 'com_kjeholtbusiness');

            } else {

                foreach ($validData['subproject_subforms'] as $subprojectData) {
                    $subProjectId = $this->storeNewSubproject($projectId, $subprojectData);
                    Log::add('ProjectController: submit: New subproject created with id= '. $subProjectId, Log::DEBUG, 'com_kjeholtbusiness');

                    $app->enqueueMessage("Deluppdrag lagrat i databas med id=" . $subProjectId, 'notice');
                    
                }
            }
            
            $app->setUserState('com_kjeholtbusiness.project.postdata', $validData);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=projectshow&layout=show&projectid='.$projectId, false));
            
        }

    }
    /*
    public function __construct($config = [])
    {
        $app = Factory::getApplication();
        
        $this->companyId = $app->getUserState('com_kjeholtbusiness.company.id', null);
        
        parent::__construct($config);
    }
    
    */
}