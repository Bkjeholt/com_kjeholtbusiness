<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\View\Companyusers;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Factory;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;

class HtmlView extends BaseHtmlView
{
    protected $items;
    protected $availableUsers;
    protected $companyId;
    protected $companyName;

    public function display($tpl = null)
    {
        $companyId = (int) Factory::getApplication()->input->getInt('company_id', 0)
            ?: (int) (CompanyUser::companyId() ?? 0);

        $companyName = CompanyUser::companyNameForId($companyId);

        $allowed = CompanyAcl::isSuiteSuperAdmin()
            || ($companyName !== null && BssAcl::hasAccess($companyName, 'Admin'));

        if (!$allowed) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->items          = $this->get('Items');
        $this->availableUsers = $this->get('AvailableUsers');
        $this->companyId      = $companyId;
        $this->companyName    = $companyName ?? '';

        return parent::display($tpl);
    }
}
