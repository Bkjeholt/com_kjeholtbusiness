<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\Database\ParameterType;

class TimereportEditModel extends FormModel
{
    protected $item;

    public function getItem(int $id = 0): ?object
    {
        if ($this->item) {
            return $this->item;
        }

        $app = Factory::getApplication();
        $id  = $id ?: (int) $app->input->getInt('id');

        if (!$id) {
            return null;
        }

        $db     = $this->getDatabase();
        $userId = (int) Factory::getUser()->id;

        $query = $db->getQuery(true)
            ->select('*')
            ->from($db->quoteName('#__kjeholtbusiness_timecards'))
            ->where($db->quoteName('id') . ' = :id')
            ->where($db->quoteName('created_by') . ' = :user_id')
            ->bind(':id', $id, ParameterType::INTEGER)
            ->bind(':user_id', $userId, ParameterType::INTEGER);

        $this->item = $db->setQuery($query)->loadObject();

        return $this->item;
    }

    public function isFrozen(object $item): bool
    {
        return isset($item->status) && $item->status === 'froozen';
    }

    /**
     * A report is locked when it can no longer be modified:
     * frozen or already validated.
     */
    public function isLocked(object $item): bool
    {
        return $this->isFrozen($item) || (isset($item->status) && $item->status === 'validated');
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm(
            'com_kjeholtbusiness.timereportedit',
            'timereport_edit',
            ['control' => 'jform', 'load_data' => $loadData]
        );

        if (empty($form)) {
            return false;
        }

        $item = $this->getItem();

        if ($item && $this->isLocked($item)) {
            $form->setFieldAttribute('description', 'readonly', 'true');
            $form->setFieldAttribute('start_time', 'readonly', 'true');
            $form->setFieldAttribute('end_time', 'readonly', 'true');
            $form->setFieldAttribute('adjustment', 'readonly', 'true');
            $form->setFieldAttribute('status', 'readonly', 'true');
        }

        return $form;
    }

    protected function loadFormData()
    {
        return (array) $this->getItem();
    }

    public function save(array $data): bool
    {
        $item = $this->getItem((int) ($data['id'] ?? 0));

        if (!$item) {
            $this->setError('Time report not found or not owned by the current user.');

            return false;
        }

        if ($this->isLocked($item)) {
            $this->setError('COM_KJEHOLTBUSINESS_TIMEREPORTS_ERROR_LOCKED');

            return false;
        }

        $db = $this->getDatabase();

        $updated = new \stdClass();
        $updated->id          = (int) $item->id;
        $updated->description = (string) ($data['description'] ?? $item->description);
        $updated->start_time  = (string) ($data['start_time'] ?? $item->start_time);
        $updated->end_time    = isset($data['end_time']) && $data['end_time'] !== '' ? (string) $data['end_time'] : $item->end_time;
        $updated->subproject_id = (int) ($data['subproject_id'] ?? $item->subproject_id);
        $updated->adjustment  = (int) ($data['adjustment'] ?? $item->adjustment);
        $updated->status      = (string) ($data['status'] ?? $item->status);
        $updated->modified_by = (int) Factory::getUser()->id;

        if (!$db->updateObject('#__kjeholtbusiness_timecards', $updated, 'id')) {
            $this->setError($db->getError());

            return false;
        }

        return true;
    }
}
