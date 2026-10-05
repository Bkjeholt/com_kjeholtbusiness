<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

class ExpencyController extends FormController
{
    protected function allowAdd($data = [])
    {
        return $this->app->getIdentity()->authorise('expency.create', 'com_kjeholtbusiness');
    }

    protected function allowEdit($data = [], $key = 'id')
    {
        $recordId = isset($data[$key]) ? (int) $data[$key] : 0;

        if ($recordId === 0) {
            return false;
        }

        return $this->app->getIdentity()->authorise('expency.edit', 'com_kjeholtbusiness.expency.' . $recordId);
    }
}
