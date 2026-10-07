<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

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
}
