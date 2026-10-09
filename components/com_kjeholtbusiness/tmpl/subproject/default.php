<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="kjeholtbusiness-subproject">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_TITLE'); ?></h1>

    <?php if (empty($this->item)) : ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_NOT_FOUND'); ?>
        </div>
    <?php else : ?>
        <div class="subproject-info">
            <h2><?php echo $this->escape($this->item->name); ?></h2>
            <dl class="dl-horizontal">
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_NAME'); ?></dt>
                <dd><?php echo $this->escape($this->item->name); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_FIELD_DESCRIPTION_LABEL'); ?></dt>
                <dd><?php echo $this->escape($this->item->description); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_START_DATE'); ?></dt>
                <dd><?php echo $this->escape($this->item->start_date); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_FIELD_HOURLY_RATE_LABEL'); ?></dt>
                <dd><?php echo number_format((float) $this->item->hourly_rate, 2, ',', ' '); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_FIELD_ESTIMATED_HOURS_LABEL'); ?></dt>
                <dd><?php echo (int) $this->item->estimated_amount_of_hours; ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_STATUS'); ?></dt>
                <dd><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_STATUS_' . strtoupper($this->item->status)); ?></dd>
            </dl>
        </div>

        <div class="subproject-timecards">
            <h3><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_VALIDATED_TIME'); ?></h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_TIME_START'); ?></th>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_TIME_END'); ?></th>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_TIME_DESCRIPTION'); ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_SPENT_HOURS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->timecards as $timecard) : ?>
                            <tr>
                                <td><?php echo $this->escape($timecard->start_time); ?></td>
                                <td><?php echo $this->escape($timecard->end_time); ?></td>
                                <td><?php echo $this->escape($timecard->description); ?></td>
                                <td class="text-end"><?php echo number_format((float) $timecard->hours, 2, ',', ' '); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($this->timecards)) : ?>
                            <tr><td colspan="4"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_NO_TIME'); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="subproject-expenses">
            <h3><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_VALIDATED_EXPENSES'); ?></h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_DATE'); ?></th>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_NAME'); ?></th>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_DESCRIPTION'); ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_AMOUNT'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->expenses as $expense) : ?>
                            <tr>
                                <td><?php echo $this->escape($expense->date); ?></td>
                                <td><?php echo $this->escape($expense->name); ?></td>
                                <td><?php echo $this->escape($expense->description); ?></td>
                                <td class="text-end"><?php echo number_format((float) $expense->amount, 2, ',', ' '); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($this->expenses)) : ?>
                            <tr><td colspan="4"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_NO_EXPENSES'); ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="subproject-totals">
            <h3><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTALS'); ?></h3>
            <dl class="dl-horizontal">
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTAL_HOURS'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->total_hours, 2, ',', ' '); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTAL_TIME_COST'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->total_time_cost, 2, ',', ' '); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTAL_COSTS'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->total_costs, 2, ',', ' '); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_GRAND_TOTAL'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->grand_total, 2, ',', ' '); ?></dd>
            </dl>
        </div>

        <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subprojects'); ?>">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_TITLE'); ?>
        </a>
    <?php endif; ?>
</div>
