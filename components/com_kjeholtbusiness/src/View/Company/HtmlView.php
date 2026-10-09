<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Company;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;

    public function display($tpl = null)
    {
        $this->item = $this->get('Item');

        return parent::display($tpl);
    }
}
