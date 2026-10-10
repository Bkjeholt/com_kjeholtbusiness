<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Projectshow;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;
use Joomla\CMS\Log\Log;


class HtmlView extends BaseHtmlView
{
    protected $item;

    public function display($tpl = null)
    {
        if (!BssAcl::hasAccessAny('project:view')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        Log::add('View/ProjectShow/HtmlView->display()', Log::DEBUG, 'com_kjeholtbusiness');
        
        $this->item = $this->get('getProjectInfo');

//        var_dump($this->items);
        
        return parent::display($tpl);
    }
}

