<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Project;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;
    
    protected $projectId;

    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $this->projectId = $app->input->getCmd('projectid','0');
        
        $layout = $app->input->getCmd('layout');
        Log::add('ProjectView layout='. $layout, Log::DEBUG, 'com_kjeholtbusiness');
        
        
        switch ($app->input->getCmd('layout', 'default')) {
            case 'show':
                Log::add('ProjectView->show ProjectId='.$projectId, Log::DEBUG, 'com_kjeholtbusiness');
                // Ensure the model is loaded and set for the view
/*                $model = $this->getModel();
                if (!$model) {
                    Log::add('ProjectView->show failed to load model', Log::ERROR, 'com_kjeholtbusiness');
                    throw new \Exception('Could not load Project model');
                } else {
                    Log::add('ProjectView->show model loaded successfully', Log::DEBUG, 'com_kjeholtbusiness');
                } 
                $this->setModel($model, true);
 */               $this->item = $this->get('ProjectInfo');
                Log::add('ProjectView->show item='. print_r($this->item, true), Log::DEBUG, 'com_kjeholtbusiness');
            break;
            
            default:
                Log::add('ProjectView->default', Log::DEBUG, 'com_kjeholtbusiness');
                $this->item = $this->get('Item');
                $this->form = $this->get('Form');
                if ($layout === 'edit'
                    && !\KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\ProjectAcl::canEditProject($this->item)) {
                    throw new \Exception('JERROR_ALERTNOAUTHOR', 403);
                }

                if ($layout === 'edit'
                    && !\KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\ProjectAcl::canEditProject($this->item)) {
                    throw new \Exception('JERROR_ALERTNOAUTHOR', 403);
                }

                ;
            break;
        }
        
        /*return*/ parent::display($tpl);
    }
}