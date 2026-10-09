<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\LogbookAcl;

class LogbookModel extends BaseDatabaseModel
{
    public function getItems(): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('l.id'),
                $db->quoteName('l.event_time'),
                $db->quoteName('l.user_id'),
                $db->quoteName('l.event'),
                $db->quoteName('l.event_text'),
                $db->quoteName('l.comment'),
                $db->quoteName('u.name', 'user_name'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_logbook', 'l'))
            ->join('LEFT', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('l.user_id'))
            ->order($db->quoteName('l.event_time') . ' DESC');

        if (!LogbookAcl::isCompanyAdmin()) {
            $userId = (int) Factory::getUser()->id;
            $query->where($db->quoteName('l.user_id') . ' = :user_id')
                ->bind(':user_id', $userId, ParameterType::INTEGER);
        }

        $db->setQuery($query, 0, 200);

        return $db->loadObjectList() ?: [];
    }

    public function getItem(int $id = 0): ?object
    {
        $id = $id ?: (int) Factory::getApplication()->input->getInt('id', 0);

        if (!$id) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('l.id'),
                $db->quoteName('l.event_time'),
                $db->quoteName('l.user_id'),
                $db->quoteName('l.event'),
                $db->quoteName('l.event_text'),
                $db->quoteName('l.comment'),
                $db->quoteName('u.name', 'user_name'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_logbook', 'l'))
            ->join('LEFT', $db->quoteName('#__users', 'u'), $db->quoteName('u.id') . ' = ' . $db->quoteName('l.user_id'))
            ->where($db->quoteName('l.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        return $db->loadObject();
    }
}
