<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\TimeReport;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
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

        $layout = $app->input->getCmd('layout');
        if ($layout === 'stop') {
            Form::addFormPath(JPATH_SITE . '/components/com_kjeholtbusiness/forms');
            $this->form = Form::getInstance(
                'com_kjeholtbusiness.timereportstop',
                'timereport_stop',
                ['control' => 'jform']
            );

            $prefill = ['end_time' => Factory::getDate()->toSql()];

            if (!empty($this->ongoing)) {
                $prefill['description'] = $this->ongoing[0]->description ?? '';
            }

            $this->form->bind($prefill);
        }

        $db      = Factory::getDbo();
        $userId  = (int) Factory::getUser()->id;
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('tc.id'),
                $db->quoteName('tc.name'),
                $db->quoteName('tc.description'),
                $db->quoteName('tc.start_time'),
                $db->quoteName('tc.subproject_id'),
                $db->quoteName('sp.name', 'subproject_name'),
                $db->quoteName('p.name', 'project_name'),
            ])
            ->from($db->quoteName('#__kjeholtbusiness_timecards', 'tc'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_subprojects', 'sp'),
                $db->quoteName('sp.id') . ' = ' . $db->quoteName('tc.subproject_id'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_projects', 'p'),
                $db->quoteName('p.id') . ' = ' . $db->quoteName('sp.project_id'))
            ->where($db->quoteName('tc.created_by') . ' = ' . (int) $userId)
            ->where($db->quoteName('tc.status') . ' = ' . $db->quote('ongoing'))
            ->order($db->quoteName('tc.start_time') . ' DESC');
        $this->ongoing = $db->setQuery($query)->loadObjectList();

        /* return */ parent::display($tpl);
    }
}

