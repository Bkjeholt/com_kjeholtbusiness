<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Projects;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;


class HtmlView extends BaseHtmlView
{
    protected $items;

    public function display($tpl = null)
    {
        Log::add('View/Projects/HtmlView->display()', Log::DEBUG, 'com_kjeholtbusiness');
        
        $this->items = $this->get('Items');

        Log::add('ProjectsView->item result= ' . htmlspecialchars(print_r($this->items, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
        
//        var_dump($this->items);
        
        return parent::display($tpl);
    }
}

