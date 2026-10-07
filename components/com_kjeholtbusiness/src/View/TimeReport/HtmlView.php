<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\TimeReport;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $task = $app->input->getCmd('task');
        
        // För felsökning/loggning
        Log::add('Aktiv task i TimeReport/HtmlView: ' . $task, Log::DEBUG, 'com_kjeholtbusiness');

        if ($task === 'timereport.start') {
            // Hantera logiken för att spara tidrapporten
            // Detta kan inkludera att validera data, spara till databasen, etc.
            Log::add('TimeReport view: Registrera en ny tidrapport', Log::DEBUG, 'com_kjeholtbusiness');
            
            
        }
        
        
        
        
        
        
        // Om du vill visa på sidan:
        // echo '<pre>Aktiv task: ' . htmlspecialchars($task) . '</pre>';
        
        
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        $db      = Factory::getDbo();
        $userId  = (int) Factory::getUser()->id;
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('id'),
                $db->quoteName('name'),
                $db->quoteName('start_time'),
                $db->quoteName('subproject_id'),
            ])
            ->from($db->quoteName('#__kjeholtbusiness_timecards'))
            ->where($db->quoteName('created_by') . ' = ' . (int) $userId)
            ->where($db->quoteName('status') . ' = ' . $db->quote('ongoing'))
            ->order($db->quoteName('start_time') . ' DESC');
        $this->ongoing = $db->setQuery($query)->loadObjectList();

        /* return */ parent::display($tpl);
    }
}

