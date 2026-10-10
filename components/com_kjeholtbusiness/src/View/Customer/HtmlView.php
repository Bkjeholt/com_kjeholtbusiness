<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Customer;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        if (!BssAcl::hasAccessAny('project:edit')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        return parent::display($tpl);
    }
}
