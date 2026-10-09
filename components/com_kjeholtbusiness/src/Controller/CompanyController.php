<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\Logbook;

class CompanyController extends BaseController
{
    public function delete()
    {
        $this->checkToken();

        if (!CompanyAcl::isSuiteSuperAdmin()) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $app = Factory::getApplication();
        $id  = (int) $app->input->getInt('id', 0);

        $model = $this->getModel('Company');
        $item  = $model->getItem($id);

        if (!$item) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANY_NOT_FOUND'), 'warning');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=companies', false));
            return false;
        }

        if ($model->delete($id)) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANY_DELETED'), 'message');
        } else {
            $app->enqueueMessage($model->getError(), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=companies', false));
        return true;
    }

    public function save()
    {
        $this->checkToken();

        $app = Factory::getApplication();

        $model = $this->getModel('Company');

        $jform      = $this->input->post->get('jform', [], 'array');
        $companyId  = (int) ($jform['id'] ?? 0);

        $company = $companyId > 0 ? $model->getItem($companyId) : null;

        if (!CompanyAcl::canEditCompany($company) && !CompanyAcl::isSuiteSuperAdmin()) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $form = $model->getForm(null, false);

        $data = $jform;

        if (!$form) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=company', false));
            return false;
        }

        $validData = $model->validate($form, $data);

        if ($validData === false) {
            foreach ($model->getErrors() as $error) {
                $app->enqueueMessage(
                    $error instanceof \Exception ? $error->getMessage() : $error,
                    'warning'
                );
            }
            $app->setUserState('com_kjeholtbusiness.company.edit.data', $data);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=company&layout=edit', false));
            return false;
        }

        if (!$model->save($validData)) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=company&layout=edit', false));
            return false;
        }

        $app->setUserState('com_kjeholtbusiness.company.edit.data', null);

        $isNew = ((int) ($validData['id'] ?? 0)) === 0;

        Logbook::log(
            $isNew ? 'company.created' : 'company.updated',
            sprintf(
                'Company %s (%s) was %s.',
                $isNew ? 'new' : '#' . (int) $validData['id'],
                $validData['name'] ?? '',
                $isNew ? 'created' : 'updated'
            )
        );

        $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_COMPANY_SAVED'), 'message');
        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=company', false));
        return true;
    }
}
