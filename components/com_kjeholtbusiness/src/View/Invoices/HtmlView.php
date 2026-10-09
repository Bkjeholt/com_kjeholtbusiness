<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Invoices;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $items;

    public function display($tpl = null)
    {
        if (!Factory::getUser()->authorise('invoice.view', 'com_kjeholtbusiness')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->items = $this->get('Items');

        return parent::display($tpl);
    }
}
