<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\ParameterType;

class InvoicesModel extends BaseDatabaseModel
{
    public function getItems(): array
    {
        $status = (string) Factory::getApplication()->input->getCmd('status', '');
        $valid  = ['draft', 'sent', 'paid', 'overdue'];

        $statusFilter = (\in_array($status, $valid, true)) ? $status : '';
        Factory::getApplication()->setUserState('com_kjeholtbusiness.invoices.status', $statusFilter);

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(
            [
                $db->quoteName('i.id'),
                $db->quoteName('i.invoice_number'),
                $db->quoteName('i.date'),
                $db->quoteName('i.due_date'),
                $db->quoteName('i.total_amount'),
                $db->quoteName('i.rot_amount'),
                $db->quoteName('i.rut_amount'),
                $db->quoteName('i.status'),
                $db->quoteName('c.name', 'customer_name'),
                $db->quoteName('p.name', 'project_name'),
            ]
        )
            ->from($db->quoteName('#__kjeholtbusiness_invoices', 'i'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_customers', 'c'), $db->quoteName('c.id') . ' = ' . $db->quoteName('i.customer_id'))
            ->join('LEFT', $db->quoteName('#__kjeholtbusiness_projects', 'p'), $db->quoteName('p.id') . ' = ' . $db->quoteName('i.project_id'))
            ->order($db->quoteName('i.date') . ' DESC, i.id DESC');

        if ($statusFilter !== '') {
            $query->where($db->quoteName('i.status') . ' = :status')
                ->bind(':status', $statusFilter);
        }

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }
}
