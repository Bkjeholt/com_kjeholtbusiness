<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

class CompanyModel extends BaseDatabaseModel
{
    protected $item;

    public function getItem(int $id = 0): ?object
    {
        if ($this->item) {
            return $this->item;
        }

        $app = Factory::getApplication();

        if (!$id) {
            $id = (int) $app->input->getInt('id', 0);
        }

        if (!$id) {
            $id = (int) $app->getUserState('com_kjeholtbusiness.company.id', 0);
        }

        if (!$id) {
            return null;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select('a.*')
            ->from($db->quoteName('#__kjeholtbusiness_companies', 'a'))
            ->where($db->quoteName('a.id') . ' = :id')
            ->bind(':id', $id, ParameterType::INTEGER);

        $db->setQuery($query);

        $this->item = $db->loadObject();

        return $this->item;
    }
}
