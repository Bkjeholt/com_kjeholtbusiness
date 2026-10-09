<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Factory;

/**
 * Multi-select list of subprojects, optionally filtered by project id
 * passed via the form data (project_id).
 */
class SubprojectsField extends ListField
{
    protected $type = 'Subprojects';

    protected function getOptions()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('sp.id', 'value'),
                'CONCAT(COALESCE(' . $db->quoteName('p.name') . ', ' . $db->quote('') . '), ' . $db->quote(' : ') . ', ' . $db->quoteName('sp.name') . ') AS ' . $db->quoteName('text'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_subprojects', 'sp'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_projects', 'p'), $db->quoteName('p.id') . ' = ' . $db->quoteName('sp.project_id'))
            ->order($db->quoteName('p.name') . ' ASC, ' . $db->quoteName('sp.name') . ' ASC');

        try {
            $rows = $db->setQuery($query)->loadObjectList() ?: [];
        } catch (\Exception $e) {
            return parent::getOptions();
        }

        $options = [];

        foreach ($rows as $row) {
            $options[] = (object) ['value' => (int) $row->value, 'text' => $row->text];
        }

        return \array_merge(parent::getOptions(), $options);
    }
}
