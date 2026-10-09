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
    public function markSent()
    {
        $this->checkToken();

        $app = Factory::getApplication();

        if (!Factory::getUser()->authorise('invoice.edit', 'com_kjeholtbusiness')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $id = (int) $app->input->getInt('id', 0);
        $db = Factory::getDbo();

        // Only draft invoices can be marked as sent
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__kjeholtbusiness_invoices'))
            ->set($db->quoteName('status') . ' = ' . $db->quote('sent'))
            ->set($db->quoteName('modified_by') . ' = :user_id')
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('status') . ' = ' . $db->quote('draft'))
            ->bind(':user_id', (int) Factory::getUser()->id, \Joomla\Database\ParameterType::INTEGER)
            ->bind(':id', $id, \Joomla\Database\ParameterType::INTEGER);

        try {
            $db->setQuery($query)->execute();

            if ($db->getAffectedRows() === 0) {
                $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_INVOICES_ERROR_SEND'), 'warning');
                $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=invoices', false));
                return false;
            }

            $subprojectIds = $this->getInvoiceSubprojectIds($id);

            // Subprojects -> waiting for payment
            if ($subprojectIds) {
                $sub = $db->getQuery(true)
                    ->update($db->quoteName('#__kjeholtbusiness_subprojects'))
                    ->set($db->quoteName('status') . ' = ' . $db->quote('waiting_for_payment'))
                    ->where($db->quoteName('id') . ' IN (' . \implode(',', \array_map('intval', $subprojectIds)) . ')');
                $db->setQuery($sub)->execute();
            }

            // Related expenses -> froozen
            $exp = $db->getQuery(true)
                ->update($db->quoteName('#__kjeholtbusiness_expenses'))
                ->set($db->quoteName('status') . ' = ' . $db->quote('froozen'))
                ->where($db->quoteName('subproject_id') . ' IN (' . \implode(',', \array_map('intval', $subprojectIds ?: [0])) . ')')
                ->where($db->quoteName('status') . ' = ' . $db->quote('validated'));
            $db->setQuery($exp)->execute();

            // Related timecards -> froozen
            $tc = $db->getQuery(true)
                ->update($db->quoteName('#__kjeholtbusiness_timecards'))
                ->set($db->quoteName('status') . ' = ' . $db->quote('froozen'))
                ->where($db->quoteName('subproject_id') . ' IN (' . \implode(',', \array_map('intval', $subprojectIds ?: [0])) . ')')
                ->where($db->quoteName('status') . ' = ' . $db->quote('validated'));
            $db->setQuery($tc)->execute();

            Logbook::log(
                'invoice.sent',
                sprintf(
                    'Invoice #%d changed state from draft to sent; %d subproject(s) set to waiting_for_payment, related costs frozen.',
                    $id,
                    \count($subprojectIds)
                )
            );

            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_INVOICES_SENT'), 'message');
        } catch (\Throwable $e) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_INVOICES_ERROR_SEND'), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=invoices', false));
        return true;
    }

    private function getInvoiceSubprojectIds(int $invoiceId): array
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('subproject_id'))
            ->from($db->quoteName('#__kjeholtbusiness_invoice_subprojects'))
            ->where($db->quoteName('invoice_id') . ' = :invoice_id')
            ->bind(':invoice_id', $invoiceId, \Joomla\Database\ParameterType::INTEGER);

        return \array_map('intval', $db->setQuery($query)->loadColumn() ?: []);
    }

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
