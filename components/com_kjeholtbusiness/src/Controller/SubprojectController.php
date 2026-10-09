<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\Logbook;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\ProjectAcl;

class SubprojectController extends FormController
{
    protected function allowAdd($data = [])
    {
        return BssAcl::hasAccessAny('project:edit');
    }

    protected function allowEdit($data = [], $key = 'id')
    {
        return BssAcl::hasAccessAny('project:edit');
    }

    public function save($key = null, $urlVar = null)
    {
        $this->checkToken();

        $app   = Factory::getApplication();
        $model = $this->getModel('Subproject');

        $jform = $this->input->post->get('jform', [], 'array');
        $id    = (int) ($jform['id'] ?? 0);

        $item = $id > 0 ? $model->getItem($id) : null;

        if (!ProjectAcl::canEditSubproject($item)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $form = $model->getForm(null, false);

        if (!$form) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=subprojects', false));
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
            $app->setUserState('com_kjeholtbusiness.subproject.edit.data', $jform);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=subproject&layout=edit&id=' . $id, false));
            return false;
        }

        if (!$model->save($validData)) {
            $app->enqueueMessage($model->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=subproject&layout=edit&id=' . $id, false));
            return false;
        }

        $app->setUserState('com_kjeholtbusiness.subproject.edit.data', null);

        Logbook::log(
            'subproject.updated',
            sprintf('Subproject #%d (%s) was updated.', $id, $validData['name'] ?? '')
        );

        $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_SAVED'), 'message');
        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=subproject&id=' . $id, false));
        return true;
    }
}
