<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
?>
<div class="kjeholtbusiness-invoices">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_TITLE'); ?></h1>

    <?php if (Factory::getUser()->authorise('invoice.create', 'com_kjeholtbusiness')) : ?>
        <a class="btn btn-primary mb-3" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=invoice&layout=edit'); ?>">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_NEW'); ?>
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_NUMBER'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_CUSTOMER'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_PROJECT'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_DATE'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_DUE_DATE'); ?></th>
                    <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_TOTAL'); ?></th>
                    <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_ROT'); ?></th>
                    <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_RUT'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <td>
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=invoice&layout=edit&id=' . (int) $item->id); ?>">
                                <?php echo $this->escape($item->invoice_number ?: ('#' . $item->id)); ?>
                            </a>
                        </td>
                        <td><?php echo $this->escape($item->customer_name ?? ''); ?></td>
                        <td><?php echo $this->escape($item->project_name ?? ''); ?></td>
                        <td><?php echo $this->escape($item->date); ?></td>
                        <td><?php echo $this->escape($item->due_date); ?></td>
                        <td class="text-end"><?php echo number_format((float) $item->total_amount, 2, ',', ' '); ?></td>
                        <td class="text-end"><?php echo number_format((float) $item->rot_amount, 2, ',', ' '); ?></td>
                        <td class="text-end"><?php echo number_format((float) $item->rut_amount, 2, ',', ' '); ?></td>
                        <td><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_STATUS_' . strtoupper($item->status)); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($this->items)) : ?>
                    <tr><td colspan="9"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_EMPTY'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
