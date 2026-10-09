<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Expense;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;
    protected $isNew;

    public function display($tpl = null)
    {
        $model = $this->getModel();

        $this->item  = $model->getItem();
        $this->form  = $model->getForm();
        $this->isNew = empty($this->item);

        return parent::display($tpl);
    }
}
