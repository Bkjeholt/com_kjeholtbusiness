<?php
defined('_JEXEC') or die;

class ACLandCategoriesHandler
{
    /**
     * Skapar ACL-grupperna om de inte redan finns.
     * Returnerar ID för "ACL: Företag 1".
     */
    public static function createUserGroups()
    {
        $db = JFactory::getDbo();
        
        // Kontrollera om "ACL: Kjeholt Business Support Suite" redan finns
        $query = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__usergroups'))
        ->where($db->quoteName('title') . ' = ' . $db->quote('UG: Kjeholt Business Support Suite'));
        $db->setQuery($query);
        $parentGroupId = $db->loadResult();
        
        // Skapa "ACL: Kjeholt Business Support Suite" om den inte finns
        if (!$parentGroupId)
        {
            $parentGroup = new stdClass();
            $parentGroup->title = 'UG: Kjeholt Business Support Suite';
            $parentGroup->parent_id = 1; // Public-gruppen är förälder
            $db->insertObject('#__usergroups', $parentGroup);
            $parentGroupId = $db->insertid();
        }
        
        // Kontrollera om "ACL: Företag 1" redan finns
        $query = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__usergroups'))
        ->where($db->quoteName('title') . ' = ' . $db->quote('ACL: Företag 1'));
        $db->setQuery($query);
        $childGroupId = $db->loadResult();
        
        // Skapa "ACL: Företag 1" om den inte finns
        if (!$childGroupId)
        {
            $childGroup = new stdClass();
            $childGroup->title = 'ACL: Företag 1';
            $childGroup->parent_id = $parentGroupId;
            $db->insertObject('#__usergroups', $childGroup);
            $childGroupId = $db->insertid();
        }
        
        return $childGroupId;
    }
    
    /**
     * Skapar kategorin "Företag 1" om den inte redan finns.
     * Returnerar ID för kategorin.
     */
    public static function createCategory($accessGroupId = null)
    {
        $db = JFactory::getDbo();
        
        // Kontrollera om kategorin "Företag 1" redan finns
        $query = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__categories'))
        ->where($db->quoteName('title') . ' = ' . $db->quote('Företag 1'))
        ->where($db->quoteName('extension') . ' = ' . $db->quote('com_kjeholtbusiness'));
        $db->setQuery($query);
        $categoryId = $db->loadResult();
        
        // Om kategorin redan finns, returnera dess ID
        if ($categoryId)
        {
            return $categoryId;
        }
        
        // Om ingen accessGroupId anges, skapa ACL-grupperna
        if ($accessGroupId === null)
        {
            $accessGroupId = self::createACLGroups();
        }
        
        // Skapa kategorin
        $categoryData = [
            'title' => 'Företag 1',
            'parent_id' => 1, // Skapar en kategori på första nivån
            'description' => 'Kategori för Företag 1',
            'extension' => 'com_kjeholtbusiness',
            'published' => 1,
            'language' => '*',
            'access' => $accessGroupId,
        ];
        
        $table = JTableNested::getInstance('Category');
        $table->setLocation(1, 'last-child');
        $table->bind($categoryData);
        $table->check();
        $table->store();
        
        return $table->id;
    }
}
