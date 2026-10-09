<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
?>
<div class="kjeholtbusiness-subproject">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_TITLE'); ?></h1>
    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=subproject.save'); ?>" method="post" name="adminForm" id="subproject-form">
        <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        <div class="form-horizontal">
            <?php echo $this->form->renderField('description'); ?>
            <?php echo $this->form->renderField('sequence_id'); ?>
            <?php echo $this->form->renderField('project_id'); ?>
            <?php echo $this->form->renderField('start_date'); ?>
            <?php echo $this->form->renderField('hourly_rate'); ?>
            <?php echo $this->form->renderField('status'); ?>
        </div>
        <input type="hidden" name="task" value="subproject.edit">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>

