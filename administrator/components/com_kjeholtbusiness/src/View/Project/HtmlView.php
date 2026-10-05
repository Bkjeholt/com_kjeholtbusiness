<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\View\Project;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;

    public function display($tpl = null)
    {
        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        return parent::display($tpl);
    }
}
