<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Table;

class Com_KjeholtbusinessInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param  Installer  $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function install($parent)
    {
        $parent->getParent()->setRedirectURL('index.php?option=com_kjeholtbusiness');
        return true;
    }

    /**
     * Method to uninstall the component.
     *
     * @param  Installer  $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function uninstall($parent)
    {
        return true;
    }

    /**
     * Method to update the component.
     *
     * @param  Installer  $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function update($parent)
    {
        return true;
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param   string    $type    The type of change (install, update, discover_install, uninstall).
     * @param   Installer $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function preflight($type, $parent)
    {
        return true;
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param   string    $type    The type of change (install, update, discover_install, uninstall).
     * @param   Installer $parent  The class calling this method.
     *
     * @return boolean True on success.
     */
    public function postflight($type, $parent)
    {
        if ($type === 'install' || $type === 'update')
        {
            // Enable the plugin after installation or update
            $this->enablePlugin();

            if ($type === 'install')
            {
                $this->seedDemoData();
            }
        }
        return true;
    }

    /**
     * Seed demo data on a fresh install, only when the projects table is empty.
     */
    private function seedDemoData()
    {
        $db = Factory::getDbo();

        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__kjeholtbusiness_projects'));
        $db->setQuery($query);

        try
        {
            if ((int) $db->loadResult() > 0)
            {
                return;
            }

            $seedFile = __DIR__ . '/administrator/components/com_kjeholtbusiness/sql/seed_data.sql';

            if (!is_file($seedFile))
            {
                return;
            }

            $db->setQuery(file_get_contents($seedFile));
            $db->execute();
        }
        catch (Exception $e)
        {
            Log::add('Error seeding demo data: ' . $e->getMessage(), Log::ERROR, 'com_kjeholtbusiness');
        }
    }

    /**
     * Enable the content plugin after installation.
     */
    private function enablePlugin()
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('enabled') . ' = 1')
            ->where($db->quoteName('element') . ' = ' . $db->quote('kjeholtbusiness'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('plugin'))
            ->where($db->quoteName('folder') . ' = ' . $db->quote('content'));

        try
        {
            $db->setQuery($query);
            $db->execute();
        }
        catch (Exception $e)
        {
            Log::add(Text::_('COM_KJEHOLTBUSINESS_ERROR_ENABLE_PLUGIN') . ': ' . $e->getMessage(), Log::ERROR, 'com_kjeholtbusiness');
        }
    }
}
