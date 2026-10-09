<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Projectshow;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;


class HtmlView extends BaseHtmlView
{
    protected $item;

    public function display($tpl = null)
    {
        Log::add('View/ProjectShow/HtmlView->display()', Log::DEBUG, 'com_kjeholtbusiness');
        
        $this->item = $this->get('getProjectInfo');

//        var_dump($this->items);
        
        return parent::display($tpl);
    }
}

