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
    protected $adminUser;
    protected $availableAdminUsers;

    public function display($tpl = null)
    {
        $this->item = $this->get('Item');

        if ($this->item && !CompanyAcl::canViewCompany($this->item)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        // Responsible company admin (suite admins may assign it)
        if ($this->item && CompanyAcl::isSuiteAdmin()) {
            $model = $this->getModel();
            $this->adminUser           = $model->getAdmin((int) $this->item->id);
            $this->availableAdminUsers = $model->getAvailableAdminUsers();
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
