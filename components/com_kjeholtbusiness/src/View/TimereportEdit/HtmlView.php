<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\TimereportEdit;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;

class HtmlView extends BaseHtmlView
{
    protected $item;
    protected $form;
    protected $isFrozen;

    public function display($tpl = null)
    {
        if (!BssAcl::hasAccessAny('timereport:edit')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $model = $this->getModel();

        $this->item    = $model->getItem();
        $this->isFrozen = $model->isLocked($this->item);
        $this->form    = $model->getForm();

        if (!$this->item) {
            Factory::getApplication()->enqueueMessage(
                Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_ERROR_NOT_FOUND'),
                'error'
            );

            return;
        }

        return parent::display($tpl);
    }
}
