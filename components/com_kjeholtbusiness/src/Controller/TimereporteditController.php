<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\Database\ParameterType;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\TimereportAcl;

class TimereporteditController extends BaseController
{

    public function save($key = null, $urlVar = null)
    {
        $this->checkToken();

        $app   = Factory::getApplication();
        $model = $this->getModel('TimereportEdit');
        $form  = $model->getForm(null, false);
        $data  = $this->input->post->get('jform', [], 'array');

        $validData = $model->validate($form, $data);

        if ($validData === false) {
            foreach ($model->getErrors() as $error) {
                $app->enqueueMessage(
                    $error instanceof \Exception ? $error->getMessage() : $error,
                    'warning'
                );
            }

            $app->setUserState('com_kjeholtbusiness.timereportedit.data', $data);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereportedit&id=' . (int) ($data['id'] ?? 0), false));

            return false;
        }

        if (!$model->save($validData)) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereportedit&id=' . (int) ($validData['id'] ?? 0), false));

            return false;
        }

        $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_SAVED'), 'message');
        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereports', false));

        return true;
    }

    public function validate()
    {
        $this->checkToken();

        $app    = Factory::getApplication();
        $userId = (int) Factory::getUser()->id;
        $id     = (int) $app->input->getInt('id', 0);

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__kjeholtbusiness_timecards'))
            ->set($db->quoteName('status') . ' = ' . $db->quote('validated'))
            ->set($db->quoteName('modified_by') . ' = :user_id')
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('status') . ' = ' . $db->quote('ended'));

        if (!TimereportAcl::seesAll()) {
            $query->where($db->quoteName('created_by') . ' = :owner')
                ->bind(':owner', $userId, ParameterType::INTEGER);
        }

        $query->bind(':user_id', $userId, ParameterType::INTEGER)
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query)->execute();

        if ($db->getAffectedRows() > 0) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_VALIDATED'), 'message');
        } else {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_ERROR_VALIDATE'), 'warning');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereports', false));
    }

    public function delete()
    {
        $this->checkToken();

        $app    = Factory::getApplication();
        $userId = (int) Factory::getUser()->id;
        $id     = (int) $app->input->getInt('id', 0);

        $db     = Factory::getDbo();
        $query  = $db->getQuery(true)
            ->delete($db->quoteName('#__kjeholtbusiness_timecards'))
            ->where($db->quoteName('id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        if (!TimereportAcl::mayDeleteAny()) {
            $query->where($db->quoteName('created_by') . ' = :owner')
                ->bind(':owner', $userId, ParameterType::INTEGER);
        }

        try {
            $db->setQuery($query)->execute();

            if ($db->getAffectedRows() > 0) {
                $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_DELETED'), 'message');
            } else {
                $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_ERROR_NOT_FOUND'), 'warning');
            }
        } catch (\Throwable $e) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_ERROR_DELETE'), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereports', false));
    }
}