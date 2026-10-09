<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="kjeholtbusiness-subprojects">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_TITLE'); ?></h1>
    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subprojects'); ?>" method="post" name="adminForm" id="adminForm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_NAME'); ?></th>
                        <th><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_STATUS'); ?></th>
                        <th><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_START_DATE'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subproject&id=' . $item->id); ?>">
                                    <?php echo $this->escape($item->name); ?>
                                </a>
                            </td>
                            <td><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_STATUS_' . strtoupper($item->status)); ?></td>
                            <td><?php echo $this->escape($item->start_date); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="task" value="">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>

