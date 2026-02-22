<?php
namespace KjeholtEngineering\Plugin\Content\KjeholtBusiness;
defined('_JEXEC') or die;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Log\Log;

class KjeholtBusiness extends CMSPlugin
{
    public function onContentAfterSave($context, $article, $isNew)
    {
        if ($context === 'com_content.article')
        {
            $input = Factory::getApplication()->input;
            $projectId = $input->get('kjeholt_project_id', 0, 'INT');

            if ($projectId > 0 && $isNew)
            {
                $db = Factory::getDbo();
                $query = $db->getQuery(true)
                    ->update($db->quoteName('#__kjeholtbusiness_projects'))
                    ->set($db->quoteName('article_id') . ' = ' . $article->id)
                    ->where($db->quoteName('id') . ' = ' . $projectId);

                try
                {
                    $db->setQuery($query)->execute();
                    Factory::getApplication()->enqueueMessage('Project article linked successfully.', 'message');
                }
                catch (\Exception $e)
                {
                    Log::add('Error linking project article: ' . $e->getMessage(), Log::ERROR, 'com_kjeholtbusiness');
                }
            }
        }
        return true;
    }

    public function onContentPrepareForm($form, $data)
    {
        if ($form->getName() === 'com_content.article')
        {
            $form->loadFile(dirname(__DIR__) . '/forms/article.xml');
        }
        return true;
    }
}

