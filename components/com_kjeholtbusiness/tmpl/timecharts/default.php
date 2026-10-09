<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="com-kjeholtbusiness-timecharts">
    <h2><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_TITLE'); ?></h2>

    <?php if (empty($this->items)) : ?>
        <p><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_NO_ITEMS'); ?></p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-striped" id="timechartsList">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_NAME'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_PROJECT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_SUBPROJECT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_START'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_END'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_ADJUSTMENT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_STATUS'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <tr>
                            <td><?php echo $this->escape($item->name); ?></td>
                            <td><?php echo $this->escape($item->project_name ?? '-'); ?></td>
                            <td><?php echo $this->escape($item->subproject_name ?? '-'); ?></td>
                            <td><?php echo $this->escape($item->start_time); ?></td>
                            <td><?php echo $this->escape($item->end_time ?? '-'); ?></td>
                            <td><?php echo (int) $item->adjustment; ?></td>
                            <td><?php echo $this->escape($item->status); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
