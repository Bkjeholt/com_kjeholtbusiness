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
<div class="kjeholtbusiness-projectsummary">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TITLE'); ?></h1>

    <?php if (empty($this->item)) : ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_NOT_FOUND'); ?>
        </div>
    <?php else : ?>
        <div class="project-info">
            <h2><?php echo $this->escape($this->item->name); ?></h2>
            <dl class="dl-horizontal">
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_NAME'); ?></dt>
                <dd><?php echo $this->escape($this->item->name); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_DESCRIPTION'); ?></dt>
                <dd><?php echo $this->escape($this->item->description); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_PROPERTY_NAME'); ?></dt>
                <dd><?php echo $this->escape($this->item->property_name); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_START_DATE'); ?></dt>
                <dd><?php echo HTMLHelper::_('date', $this->item->start_date, Text::_('DATE_FORMAT_LC4')); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_STATUS'); ?></dt>
                <dd><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_STATUS_' . strtoupper($this->item->status)); ?></dd>
            </dl>
        </div>

        <div class="project-subprojects">
            <h3><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_TITLE'); ?></h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_NAME'); ?></th>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_STATUS'); ?></th>
                            <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_START_DATE'); ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_HOURLY_RATE'); ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_SPENT_HOURS'); ?></th>
                            <th scope="col" class="text-end"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_SPENT_COSTS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->subprojects as $subproject) : ?>
                            <tr>
                                <td>
                                    <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subproject&id=' . (int) $subproject->id); ?>">
                                        <?php echo $this->escape($subproject->name); ?>
                                    </a>
                                </td>
                                <td><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_STATUS_' . strtoupper($subproject->status)); ?></td>
                                <td><?php echo $subproject->start_date; ?></td>
                                <td class="text-end"><?php echo number_format((float) $subproject->hourly_rate, 2, ',', ' '); ?></td>
                                <td class="text-end"><?php echo number_format((float) $subproject->spent_hours, 2, ',', ' '); ?></td>
                                <td class="text-end"><?php echo number_format((float) $subproject->spent_costs, 2, ',', ' '); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="project-totals">
            <h3><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTALS'); ?></h3>
            <dl class="dl-horizontal">
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTAL_HOURS'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->total_hours, 2, ',', ' '); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTAL_TIME_COST'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->total_time_cost, 2, ',', ' '); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TOTAL_COSTS'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->total_costs, 2, ',', ' '); ?></dd>
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_GRAND_TOTAL'); ?></dt>
                <dd><?php echo number_format((float) $this->totals->total_time_cost + (float) $this->totals->total_costs, 2, ',', ' '); ?></dd>
            </dl>
        </div>

        <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=projects'); ?>">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_BACK_TO_PROJECTS'); ?>
        </a>
    <?php endif; ?>
</div>
