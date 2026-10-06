<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;


class TimereportstartController extends BaseController
{
    public function old_submit($key = null, $urlVar = null)
    {
        // Check that this HTTP POST has come from our form
        // checkToken checks the token is valid and exits if it's not right
        $this->checkToken();

        Log::add('TRS: submit: ' . $urlVar, Log::DEBUG, 'com_kjeholtbusiness');
        
        
        $app   = Factory::getApplication();
        $model = $this->getModel('Timereportstart');
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
            $app->setUserState('com_kjeholtbusiness.timereportstart', $data);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereportstart', false));
        }
        else
        {
            // Spara en ny rad i #__kjeholtbusiness_timecards
            $db = Factory::getDbo();
            $row = new \stdClass();
            $row->name = isset($validData['name']) ? $validData['name'] : '';
            $row->description = isset($validData['description']) ? $validData['description'] : '';
            $row->subproject_id = isset($validData['subproject_id']) ? (int)$validData['subproject_id'] : 0;
            $row->start_time = isset($validData['start_time']) ? $validData['start_time'] : date('Y-m-d H:i:s');
            $row->end_time = isset($validData['end_time']) ? $validData['end_time'] : null;
            $row->adjustment = isset($validData['adjustment']) ? (int)$validData['adjustment'] : 0;
            $row->status = isset($validData['status']) ? $validData['status'] : 'ongoing';
            $row->created_by = Factory::getUser()->id;
            $row->created_at = date('Y-m-d H:i:s');
            $row->params = '';
            $db->insertObject('#__kjeholtbusiness_timecards', $row);

            // Spara postad data i sessionen för visning i timereport-vyn
            $app->setUserState('com_kjeholtbusiness.timereport.postdata', $validData);
            $app->enqueueMessage("Data successfully validated and timecard created", 'notice');
            $app->setUserState('com_kjeholtbusiness.timereportstart', null);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereport', false));
        }
        
        
    }
}