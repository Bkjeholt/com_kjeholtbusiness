<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\Database\ParameterType;

class SubprojectModel extends ItemModel
{
    private $item;

    public function getItem($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('subproject.id') ?: Factory::getApplication()->input->getInt('id', 0));

        if (empty($pk)) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'a'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $pk, ParameterType::INTEGER);

        $db->setQuery($query);

        $this->item = $db->loadObject();
        $this->setState('subproject.id', $pk);

        return $this->item;
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_kjeholtbusiness.subproject',
            'subproject',
            [
                'control'   => 'jform',
                'load_data' => $loadData,
            ]
        );

        if (empty($form)) {
            throw new \Exception(implode("\n", $this->getErrors()), 500);
        }

        return $form;
    }

    protected function loadFormData()
    {
        return $this->item !== null
            ? (array) $this->item
            : Factory::getApplication()->getUserState('com_kjeholtbusiness.subproject.edit.data', []);
    }
}
