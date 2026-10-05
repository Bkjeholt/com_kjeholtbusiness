<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=projects'); ?>"
      method="post" name="adminForm" id="adminForm">
    <div class="table-responsive">
        <table class="table table-striped" id="projectList">
            <thead>
                <tr>
                    <th scope="col" style="width:1%"><?php echo Text::_('JGRID_HEADING_ID'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_NAME'); ?></th>
                    <th scope="col" style="width:10%"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_STATUS'); ?></th>
                    <th scope="col" style="width:15%"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_START_DATE'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $i => $item) : ?>
                    <tr>
                        <th scope="row"><?php echo (int) $item->id; ?></th>
                        <td>
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=project.edit&id=' . (int) $item->id); ?>">
                                <?php echo $this->escape($item->name); ?>
                            </a>
                        </td>
                        <td><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_STATUS_' . strtoupper($item->status)); ?></td>
                        <td><?php echo HTMLHelper::_('date', $item->start_date, 'Y-m-d'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="task" value="">
    <input type="hidden" name="boxchecked" value="0">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
