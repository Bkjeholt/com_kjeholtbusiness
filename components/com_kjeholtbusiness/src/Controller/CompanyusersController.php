<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\Logbook;

class CompanyusersController extends BaseController
{
    private function assertCompanyAdmin(): int
    {
        $user = Factory::getUser();

        if ($user->guest) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $companyId = (int) Factory::getApplication()->input->getInt('company_id', 0)
            ?: (int) (CompanyUser::companyId() ?? 0);

        // Suite SuperAdmin or a member of this company's Admin level may manage users
        $companyName = CompanyUser::companyNameForId($companyId);

        $allowed = \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl::isSuiteSuperAdmin()
            || ($companyName !== null
                && \KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl::hasAccess($companyName, 'Admin'));

        if (!$allowed) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        return $companyId;
    }

    public function connect()
    {
        $this->checkToken();

        $app      = Factory::getApplication();
        $companyId = $this->assertCompanyAdmin();
        $userId   = (int) $app->input->getInt('user_id', 0);

        if (!$companyId || !$userId) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_ERROR_PARAMS'), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=companyusers&company_id=' . $companyId, false));
            return false;
        }

        if (CompanyUser::connect($userId, $companyId)) {
            Logbook::log(
                'companyuser.connected',
                sprintf('User #%d was connected to company #%d.', $userId, $companyId)
            );
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_CONNECTED'), 'message');
        } else {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_ERROR_CONNECT'), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=companyusers&company_id=' . $companyId, false));
        return true;
    }

    public function disconnect()
    {
        $this->checkToken();

        $app      = Factory::getApplication();
        $companyId = $this->assertCompanyAdmin();
        $userId   = (int) $app->input->getInt('user_id', 0);

        if (!$userId) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_ERROR_PARAMS'), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=companyusers&company_id=' . $companyId, false));
            return false;
        }

        if (CompanyUser::disconnect($userId)) {
            Logbook::log(
                'companyuser.disconnected',
                sprintf('User #%d was disconnected from company #%d.', $userId, $companyId)
            );
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_DISCONNECTED'), 'message');
        } else {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_ERROR_DISCONNECT'), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=companyusers&company_id=' . $companyId, false));
        return true;
    }
}
