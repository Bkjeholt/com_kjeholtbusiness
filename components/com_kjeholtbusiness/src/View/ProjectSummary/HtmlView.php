<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\ProjectSummary;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;


class HtmlView extends BaseHtmlView
{
    protected $items;

    public function display($tpl = null)
    {
        Log::add('View/ProjectSummary/HtmlView->display()', Log::DEBUG, 'com_kjeholtbusiness');
        
        $this->items = $this->get('Items');


        
        return parent::display($tpl);
    }
}

