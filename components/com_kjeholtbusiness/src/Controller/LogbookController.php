<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\Logbook;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\LogbookAcl;

class LogbookController extends BaseController
{
    public function updateComment()
    {
        $this->checkToken();

        $app     = Factory::getApplication();
        $id      = (int) $app->input->getInt('id', 0);
        $comment = (string) $app->input->post->getString('comment', '');

        $model = $this->getModel('Logbook');
        $entry = $model->getItem($id);

        if (!$entry) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        if (!LogbookAcl::canUpdateComment($entry)) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        if (Logbook::updateComment($id, $comment)) {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_COMMENT_SAVED'), 'message');
        } else {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_COMMENT_ERROR'), 'warning');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=logbook', false));
    }
}
