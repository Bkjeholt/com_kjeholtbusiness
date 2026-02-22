<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\FormModel;

class ProjectModel extends FormModel
{
    public function getForm($data = array(), $loadData = true) {
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
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState(
            'com_kjeholtbusiness.project',  // a unique name to identify the data in the session
            array("name" => "Uppdrag för att testa formuläret",
                "property_name" => "Värmdö Södersunda 1:74",
                "status" => "preliminary"
            ) // prefill data if no data found in session
            );
        
        return $data;
    }
    
}

