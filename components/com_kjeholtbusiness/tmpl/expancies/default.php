<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="kjeholtbusiness-expencies">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENCIES_TITLE'); ?></h1>
    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=expencies'); ?>" method="post" name="adminForm" id="adminForm">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENCIES_NAME'); ?></th>
                        <th><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENCIES_TYPE'); ?></th>
                        <th><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENCIES_VALUE'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=expency&id=' . $item->id); ?>">
                                    <?php echo $this->escape($item->name); ?>
                                </a>
                            </td>
                            <td><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENCY_TYPE_' . strtoupper($item->type)); ?></td>
                            <td><?php echo $item->value; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <input type="hidden" name="task" value="">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>

