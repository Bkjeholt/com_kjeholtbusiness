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

        <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subprojects'); ?>">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_TITLE'); ?>
        </a>
    <?php endif; ?>
</div>
