<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyUser;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\Logbook;

class CustomerController extends BaseController
{
    public function save()
    {
        $this->checkToken();

        $app   = Factory::getApplication();
        $model = $this->getModel('Customer');

        $jform = $this->input->post->get('jform', [], 'array');
        $id    = (int) ($jform['id'] ?? 0);
        $isNew = $id === 0;

        if (!BssAcl::hasAccessAny('project:edit')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        if (!$isNew) {
            $item = $model->getItem($id);

            // Owner or within the same company may edit
            $companyId = (int) (CompanyUser::companyId() ?? 0);

            if ($item
                && (int) $item->created_by !== (int) Factory::getUser()->id
                && (!BssAcl::hasAccessAny('Admin') || ((int) ($item->company_id ?? 0) !== $companyId))) {
                throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
            }
        }

        $form = $model->getForm(null, false);

        if (!$form) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=customers', false));
            return false;
        }

        $validData = $model->validate($form, $jform);

        if ($validData === false) {
            foreach ($model->getErrors() as $error) {
                $app->enqueueMessage(
                    $error instanceof \Exception ? $error->getMessage() : $error,
                    'warning'
                );
            }
            $app->setUserState('com_kjeholtbusiness.customer.edit.data', $jform);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=customer&layout=edit' . ($id ? '&id=' . $id : ''), false));
            return false;
        }

        if (!$model->save($validData)) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=customer&layout=edit' . ($id ? '&id=' . $id : ''), false));
            return false;
        }

        $app->setUserState('com_kjeholtbusiness.customer.edit.data', null);

        Logbook::log(
            $isNew ? 'customer.created' : 'customer.updated',
            sprintf('Customer #%d (%s) was %s.', $id ?: 0, $validData['name'] ?? '', $isNew ? 'created' : 'updated')
        );

        $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_SAVED'), 'message');
        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=customers', false));
        return true;
    }
}
