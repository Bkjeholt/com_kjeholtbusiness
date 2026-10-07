<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

Factory::getApplication()->getDocument()->getWebAssetManager()->useScript('form.validate');
?>
<div class="com-kjeholtbusiness-timereport-stop">
    <h2><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORT_STOP_HEADING'); ?></h2>

    <?php if (empty($this->ongoing)) : ?>
        <p><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORT_NONE_ONGOING'); ?></p>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_START'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_SUBPROJECT'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->ongoing as $report) : ?>
                    <tr>
                        <td><?php echo HTMLHelper::_('date', $report->start_time, 'Y-m-d H:i'); ?></td>
                        <td><?php echo (int) $report->subproject_id; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereport'); ?>"
              class="form-validate" method="post" name="stopForm" id="stopForm">
            <?php echo $this->form->renderFieldset('stopTimeReportFieldset'); ?>
            <button type="submit" class="btn btn-danger">
                <?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORT_STOP'); ?>
            </button>
            <input type="hidden" name="task" value="timereport.endSession" />
            <?php echo HTMLHelper::_('form.token'); ?>
        </form>
    <?php endif; ?>
</div>
