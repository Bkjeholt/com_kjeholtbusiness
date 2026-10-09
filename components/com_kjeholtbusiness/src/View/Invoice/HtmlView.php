<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Invoice;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;
    protected $lines;

    public function display($tpl = null)
    {
        $user = Factory::getUser();

        if (!$user->authorise('invoice.view', 'com_kjeholtbusiness')
            && !$user->authorise('invoice.create', 'com_kjeholtbusiness')
            && !$user->authorise('invoice.edit', 'com_kjeholtbusiness')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->item = $this->get('Item');
        $this->form = $this->get('Form');

        if ($this->getLayout() === 'print') {
            $this->lines = $this->get('SubprojectLines');
        }

        return parent::display($tpl);
    }
}
