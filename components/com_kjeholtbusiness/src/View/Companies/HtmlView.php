<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Companies;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;

class HtmlView extends BaseHtmlView
{
    protected $items;

    public function display($tpl = null)
    {
        if (!CompanyAcl::mayListAllCompanies()) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->items = $this->get('Items');

        return parent::display($tpl);
    }
}
