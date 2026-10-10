<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Timereports;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\TimecardService;

class HtmlView extends BaseHtmlView
{
    protected $items;

    public function display($tpl = null)
    {
        if (!BssAcl::hasAccessAny('timereport:view')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        TimecardService::autoCloseAfter24h((int) Factory::getUser()->id);

        $this->items = $this->get('Items');

        return parent::display($tpl);
    }
}
