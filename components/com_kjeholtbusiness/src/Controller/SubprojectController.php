<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Log\Log;

class SubprojectController extends FormController
{
    protected function allowAdd($data = [])
    {
        if ($data !== [] && !empty($data['project_id'])) {
            $projectId = (int) $data['project_id'];

            return Factory::getUser()->authorise('project.edit', 'com_kjeholtbusiness.project.' . $projectId)
                || Factory::getUser()->authorise('project.edit', 'com_kjeholtbusiness');
        }

        return (bool) Factory::getUser()->authorise('project.edit', 'com_kjeholtbusiness');
    }

    protected function allowEdit($data = [], $key = 'id')
    {
        $recordId = isset($data[$key]) ? (int) $data[$key] : 0;

        if ($recordId) {
            return Factory::getUser()->authorise('project.edit', 'com_kjeholtbusiness.subproject.' . $recordId)
                || Factory::getUser()->authorise('project.edit', 'com_kjeholtbusiness');
        }

        return (bool) Factory::getUser()->authorise('project.edit', 'com_kjeholtbusiness');
    }
}
