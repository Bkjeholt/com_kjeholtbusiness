<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Company;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        $this->item = $this->get('Item');

        if ($this->item && !CompanyAcl::canViewCompany($this->item)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $layout = $this->getLayout();

        if ($layout === 'edit') {
            if ($this->item && !CompanyAcl::canEditCompany($this->item)) {
                throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
            }

            $this->form = $this->get('Form');
        }

        return parent::display($tpl);
    }
}
