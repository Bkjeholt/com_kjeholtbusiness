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
<!DOCTYPE html>
<html lang="<?php echo $this->escape(Factory::getLanguage()->getTag()); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_TITLE') . ' ' . $this->escape($this->item->invoice_number); ?></title>
    <style>
        body { font-family: Georgia, 'Times New Roman', serif; margin: 2cm; color: #000; }
        h1 { border-bottom: 2px solid #000; padding-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 1em; }
        th, td { padding: 6px 8px; text-align: left; border-bottom: 1px solid #999; }
        th { border-bottom: 2px solid #000; }
        .text-end { text-align: right; }
        .header-block { display: flex; justify-content: space-between; margin-bottom: 2em; }
        .totals { margin-top: 1.5em; width: 45%; margin-left: auto; }
        .totals td { border: none; padding: 3px 8px; }
        .totals .grand td { font-weight: bold; border-top: 2px solid #000; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header-block">
        <div>
            <strong><?php echo $this->escape(Factory::getApplication()->get('sitename')); ?></strong><br/>
        </div>
        <div class="text-end">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_NUMBER'); ?>:
            <strong><?php echo $this->escape($this->item->invoice_number ?: ('#' . $this->item->id)); ?></strong><br/>
            <?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_DATE'); ?>:
            <?php echo $this->escape($this->item->date); ?><br/>
            <?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_DUE_DATE'); ?>:
            <?php echo $this->escape($this->item->due_date); ?>
        </div>
    </div>

    <p><strong><?php echo $this->escape($this->item->customer_name ?? ''); ?></strong></p>

    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_TITLE'); ?></h1>

    <table>
        <thead>
            <tr>
                <th><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_SUBPROJECTS'); ?></th>
                <th><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_ROW_TYPE'); ?></th>
                <th class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_SPENT_HOURS'); ?></th>
                <th class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_FIELD_HOURLY_RATE_LABEL'); ?></th>
                <th class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_AMOUNT'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->lines as $line) : ?>
                <?php if ((float) $line->time_cost != 0.0) : ?>
                <tr>
                    <td><?php echo $this->escape($line->name); ?></td>
                    <td><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_ROW_TIME'); ?></td>
                    <td class="text-end"><?php echo number_format((float) $line->hours, 2, ',', ' '); ?></td>
                    <td class="text-end"><?php echo number_format((float) $line->hourly_rate, 2, ',', ' '); ?></td>
                    <td class="text-end"><?php echo number_format((float) $line->time_cost, 2, ',', ' '); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ((float) $line->expense_amount != 0.0) : ?>
                <tr>
                    <td><?php echo $this->escape($line->name); ?></td>
                    <td><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_ROW_EXPENSES'); ?></td>
                    <td class="text-end"></td>
                    <td class="text-end"></td>
                    <td class="text-end"><?php echo number_format((float) $line->expense_amount, 2, ',', ' '); ?></td>
                </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_TOTAL'); ?></td>
            <td class="text-end"><?php echo number_format((float) $this->item->total_amount, 2, ',', ' '); ?></td>
        </tr>
        <?php if ((float) $this->item->rot_amount > 0) : ?>
        <tr>
            <td><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_ROT'); ?> (<?php echo number_format((float) $this->item->rot_percentage, 0, ',', ' '); ?>%)</td>
            <td class="text-end">- <?php echo number_format((float) $this->item->rot_amount, 2, ',', ' '); ?></td>
        </tr>
        <?php endif; ?>
        <?php if ((float) $this->item->rut_amount > 0) : ?>
        <tr>
            <td><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_RUT'); ?> (<?php echo number_format((float) $this->item->rut_percentage, 0, ',', ' '); ?>%)</td>
            <td class="text-end">- <?php echo number_format((float) $this->item->rut_amount, 2, ',', ' '); ?></td>
        </tr>
        <?php endif; ?>
        <tr class="grand">
            <td><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_GRAND_TOTAL'); ?></td>
            <td class="text-end">
                <?php
                $toPay = (float) $this->item->total_amount - (float) $this->item->rot_amount - (float) $this->item->rut_amount;
                echo number_format($toPay, 2, ',', ' ');
                ?>
            </td>
        </tr>
    </table>

    <p class="no-print" style="margin-top: 2em;">
        <button type="button" onclick="window.print();">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_PRINT'); ?>
        </button>
        <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=invoices'); ?>">
            <?php echo Text::_('JCANCEL'); ?>
        </a>
    </p>
</body>
</html>
