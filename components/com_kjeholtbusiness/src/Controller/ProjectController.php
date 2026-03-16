<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

class ProjectController extends BaseController
{
    public function submit($key = null, $urlVar = null)
    {
        $this->checkToken();

        $app = Factory::getApplication();
        $model = $this->getModel('project');
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

            $app->enqueueMessage("Data successfully validated", 'notice');
        }

    }
    public function __construct($config = [])
    {
        parent::__construct($config);
    }
}

