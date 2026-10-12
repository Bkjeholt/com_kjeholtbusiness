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


    /**
     * Show the form for a new subproject under the given project.
     */
    public function add()
    {
        $app       = Factory::getApplication();
        $projectId = (int) $app->input->getInt('project_id', 0);

        if (!ProjectAcl::canEditProject((object) ['id' => $projectId])) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=subproject&layout=edit&project_id=' . $projectId, false));
        return true;
    }

    /**
     * Change the status of a subproject.
     */
    public function status()
    {
        $this->checkToken();

        $app     = Factory::getApplication();
        $model   = $this->getModel('Subproject');
        $id      = (int) $app->input->getInt('id', 0);
        $status  = (string) $app->input->getCmd('status', '');
        $item    = $id ? $model->getItem($id) : null;

        if (!ProjectAcl::canEditSubproject($item)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        if (!$model->setStatus($id, $status)) {
            $app->enqueueMessage($model->getError(), 'error');
        } else {
            Logbook::log('subproject.status', sprintf('Subproject #%d status set to %s.', $id, $status));
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_STATUS_SAVED'), 'message');
        }

        $projectId = (int) ($item->project_id ?? 0);
        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=projectsummary&id=' . $projectId, false));
        return true;
    }

    public function save($key = null, $urlVar = null)
    {
        $this->checkToken();

        $app   = Factory::getApplication();
        $model = $this->getModel('Subproject');

        $jform = $this->input->post->get('jform', [], 'array');
        $id    = (int) ($jform['id'] ?? 0);

        $item = $id > 0 ? $model->getItem($id) : null;

        if ($id && !ProjectAcl::canEditSubproject($item)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        if (!$id) {
            $projectId = (int) ($jform['project_id'] ?? 0);

            if (!$projectId || !ProjectAcl::canEditProject((object) ['id' => $projectId])) {
                throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
            }
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
            $id ? 'subproject.updated' : 'subproject.created',
            sprintf('Subproject #%d (%s) was %s.', $id, $validData['name'] ?? '', $id ? 'updated' : 'created')
        );

        $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_SAVED'), 'message');
        $projectId = (int) ($validData['project_id'] ?? ($item->project_id ?? 0));
        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=projectsummary&id=' . $projectId, false));
        return true;
    }
}
