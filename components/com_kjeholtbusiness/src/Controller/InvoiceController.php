<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\Logbook;

class InvoiceController extends BaseController
{
    public function save()
    {
        $this->checkToken();

        $app   = Factory::getApplication();
        $model = $this->getModel('Invoice');

        $jform = $this->input->post->get('jform', [], 'array');

        if (!Factory::getUser()->authorise('invoice.create', 'com_kjeholtbusiness')
            && !Factory::getUser()->authorise('invoice.edit', 'com_kjeholtbusiness')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $form = $model->getForm(null, false);

        if (!$form) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=invoices', false));
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
            $app->setUserState('com_kjeholtbusiness.invoice.edit.data', $jform);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=invoice&layout=edit', false));
            return false;
        }

        if (empty($validData['subprojects']) || !\is_array($validData['subprojects'])) {
            $validData['subprojects'] = (array) ($jform['subprojects'] ?? []);
        }

        $id = (int) ($validData['id'] ?? 0);

        if (!$model->save($validData)) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=invoice&layout=edit', false));
            return false;
        }

        $app->setUserState('com_kjeholtbusiness.invoice.edit.data', null);

        $isNew = $id === 0;

        Logbook::log(
            $isNew ? 'invoice.created' : 'invoice.updated',
            sprintf('Invoice #%d (%s) was %s.', $id ?: 0, $validData['invoice_number'] ?? '', $isNew ? 'created' : 'updated')
        );

        $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_INVOICES_SAVED'), 'message');
        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=invoices', false));
        return true;
    }
}
