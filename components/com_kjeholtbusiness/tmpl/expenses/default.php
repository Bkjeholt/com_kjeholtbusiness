<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="com-kjeholtbusiness-expenses">
    <h2><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_TITLE'); ?></h2>

    <p>
        <a class="btn btn-primary"
           href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=expense'); ?>">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_NEW'); ?>
        </a>
    </p>

    <?php if (empty($this->items)) : ?>
        <p><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_NO_ITEMS'); ?></p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-striped" id="expensesList">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_DATE'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_NAME'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_PROJECT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_SUBPROJECT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_AMOUNT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_SUPPLIER'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_STATUS'); ?></th>
                        <th scope="col"><?php echo Text::_('JACTION_EDIT'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $item) : ?>
                        <tr>
                            <td><?php echo $this->escape($item->date); ?></td>
                            <td><?php echo $this->escape($item->name); ?></td>
                            <td><?php echo $this->escape($item->project_name ?? '---'); ?></td>
                            <td><?php echo $this->escape($item->subproject_name ?? '---'); ?></td>
                            <td><?php echo number_format((float) $item->amount, 2, ',', ' '); ?></td>
                            <td><?php echo $this->escape($item->supplier ?? '-'); ?></td>
                            <td><?php echo $this->escape($item->status ?? 'new'); ?></td>
                            <td>
                                <a class="btn btn-secondary btn-sm"
                                   href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=expense&id=' . (int) $item->id); ?>">
                                    <?php echo Text::_('JACTION_EDIT'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
