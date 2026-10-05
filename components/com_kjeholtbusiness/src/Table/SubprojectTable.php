<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Table;

defined('_JEXEC') or die;

use Joomla\Database\DatabaseDriver;

class SubprojectTable extends \Joomla\CMS\Table\Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__kjeholtbusiness_subprojects', 'id', $db);
    }
}
