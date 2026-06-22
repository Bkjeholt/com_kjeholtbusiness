<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

class DisplayController extends BaseController
{
    public function display($cachable = false, $urlparams = array())
    {
        $app = Factory::getApplication();
        $viewParam = $app->input->getCmd('view');
        $layoutParam = $app->input->getCmd('layout');
        $taskParam = $app->input->getCmd('task','undefined');
        
        // Cookie handling
        
        $companyId = $app->input->cookie->get('kjeholtbusiness_company_id', null);

        if ($companyId === null) {
            Log::add('DisplayController: No company_id found in cookies.', Log::DEBUG, 'com_kjeholtbusiness');
            setcookie('kjeholtbusiness_company_id', '1', time() + 30, '/');
            $companyId = '1'; // Använd värdet direkt i denna request
        } else {
            Log::add('DisplayController: Found company_id in cookies: ' . $companyId, Log::DEBUG, 'com_kjeholtbusiness');
        }
        $app->setUserState('com_kjeholtbusiness.company.id', $companyId);
        
        Log::add('DisplayController: View:Layout:Task=> '.$viewParam.' : '.$layoutParam.' : '.$taskParam . ' <= CompanyId='.$companyId, Log::DEBUG, 'com_kjeholtbusiness');
        // Beroende på vilken vy och layout som efterfrågas, så hämtas motsvarande modell och kopplas till vyn. 
        // Detta görs i DisplayController för att undvika att behöva göra det i varje enskild vy, vilket skulle leda till mycket duplicerad kod. 

        switch ($viewParam) {
/*            case 'company':
                switch ($layoutParam) {
                    case 'create':
                        $model = $this->getModel('CompanyCreate');
                        $viewObj = $this->getView($view, 'html');
                        $viewObj->setModel($model, true);
                        break;
                    case 'select':
                        $model = $this->getModel('CompanySelect');
                        $viewObj = $this->getView($view, 'html');
                        $viewObj->setModel($model, true);
                        break;
                    case 'edit':
                        $model = $this->getModel('CompanyCreate');
                        $viewObj = $this->getView($view, 'html');
                        $viewObj->setModel($model, true);
                        break;
                    default:
                        $model = $this->getModel('Company');
                        $viewObj = $this->getView($view, 'html');
                        $viewObj->setModel($model, true);
                        break;
                }
                break; */
 /*           case 'project':
                switch (strtolower($layoutParam)) {
                    case 'create':  
                        Log::add('DisplayController->display: ProjectCreate', Log::DEBUG, 'com_kjeholtbusiness');
                        $model = $this->getModel('Project');
                        $view = $this->getView($viewParam, 'html');
                        $view->setModel($model, true); // Attach ProjectModel to Project view
                        break;
                    case 'edit':
                        $model = $this->getModel('ProjectEdit');
                        $viewObj = $this->getView($viewParam, 'html');
                        $viewObj->setModel($model, true);
                        break;
                    case 'list':
                        $model = $this->getModel('ProjectList');
                        $viewObj = $this->getView($viewParam, 'html');
                        $viewObj->setModel($model, true);
                        break;
                    case 'show':
                        Log::add('DisplayController->display: ProjectShow', Log::DEBUG, 'com_kjeholtbusiness');
                        
                        $show_model = $this->getModel('ProjectShow');
                        $view = $this->getView('Project', 'html');
                        $view->setModel($show_model, true);
                        break;
                    default:
                        $model = $this->getModel('Project');
                        $viewObj = $this->getView($viewParam, 'html');
                        $viewObj->setModel($model, true);
                        break;
                }
                break;*/
  /*          case 'timerecord':
                switch ($layoutParam) {
                    case 'create':  
                        $model = $this->getModel('TimeReportCreate');
                        $viewObj = $this->getView($view, 'html');
                        $viewObj->setModel($model, true);
                        break;
                    case 'edit':
                        $model = $this->getModel('TimeReportCreate');
                        $viewObj = $this->getView($view, 'html');
                        $viewObj->setModel($model, true);
                        break;
                    default:
                        $model = $this->getModel('TimeReportCreate');
                        $viewObj = $this->getView($view, 'html');
                        $viewObj->setModel($model, true);
                        break;
                }
                break;
                // fler case... */
            default:
                // valfritt: standardhantering
                break;
        }

        return parent::display($cachable, $urlparams);
    }
    
    public function setViewModels(object $view)
    {
        parent::setViewModels($view);

        Log::add('DisplayController->setViewModel', Log::DEBUG, 'com_kjeholtbusiness');
        
        $viewName = $view->getName();
        
        // Push the Second model into the Foos view
        if ($viewName == strtolower("Project") && ($model = $this->getModel('ProjectShow')) ){
            $view->setModel($model);
        }
        
    }
}