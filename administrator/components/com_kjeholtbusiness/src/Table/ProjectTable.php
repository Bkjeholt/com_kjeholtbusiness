<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Table;
defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;

class ProjectTable extends Table
{
    public function __construct(\Joomla\Database\DatabaseDriver $db)
    {
        parent::__construct('#__kjeholtbusiness_projects', 'id', $db);
    }
}

