<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\TimeReportStart;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        $app = \Joomla\CMS\Factory::getApplication();
        $task = $app->input->getCmd('task');
        // För felsökning/loggning
        \Joomla\CMS\Log\Log::add('Aktiv task i TimeReportStart/HtmlView: ' . $task, \Joomla\CMS\Log\Log::DEBUG, 'com_kjeholtbusiness');
        // Om du vill visa på sidan:
        // echo '<pre>Aktiv task: ' . htmlspecialchars($task) . '</pre>';
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');
        /* return */ parent::display($tpl);
    }
}