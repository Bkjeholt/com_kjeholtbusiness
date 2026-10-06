<?php

namespace KjeholtEngineering\Component\KjeholtBusiness\Administrator\Service;

use Joomla\CMS\Categories\Categories;

\defined('_JEXEC') or die;

class Category extends Categories
{
    
    public function __construct($options = array())
    {
        $options['table']     = '#__kjeholtbusiness';
        $options['extension'] = 'com_kjeholtbusiness';
        
        parent::__construct($options);
    }
}