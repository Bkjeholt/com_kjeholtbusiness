<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Log\Log;

class ProjectCreateModel extends FormModel
{
    public function getForm($data = array(), $loadData = true) {
        Log::add('ProjectCreateModel->getForm', Log::DEBUG, 'com_kjeholtbusiness');
        
        $formPath = JPATH_ROOT . '/components/com_kjeholtbusiness/forms/project.xml';
        if (!file_exists($formPath)) {
            \Joomla\CMS\Log\Log::add('project.xml not found at: ' . $formPath, \Joomla\CMS\Log\Log::ERROR, 'com_kjeholtbusiness');
        } else {
            \Joomla\CMS\Log\Log::add('project.xml found at: ' . $formPath, \Joomla\CMS\Log\Log::DEBUG, 'com_kjeholtbusiness');
        }
        
        $form = $this->loadForm(
            'com_kjeholtbusiness.project',   // just a unique name to identify the form
            'project',              // the filename of the XML form definition
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
        
        Log::add('ProjectCreateModel->loadFormData ', Log::DEBUG, 'com_kjeholtbusiness');
        
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_kjeholtbusiness.project',  // a unique name to identify the data in the session
            array("name" => "Uppdrag för att testa formuläret",
                  "property_name" => "Värmdö Södersunda 1:74",
                  "project_status" => "preliminary",
                  "start_date" => "2026-04-01",
                  "customer_subfields" => 
                      array("customer_name" => "Testa Testsson", "customer_number" => "12345678"),
                  "subproject_subfields" =>
                      array("name" => "Initial projektering",
                            "start_date" => "2026-04-01",
                            "hourly_rate" => "900",
                            "subproject_status" => "preliminary" )
                  )
             // prefill data if no data found in session
            );
        
        return $data;
    }
}