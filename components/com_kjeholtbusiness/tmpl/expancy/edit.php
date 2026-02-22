<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="kjeholtbusiness-expency">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENCY_TITLE'); ?></h1>
    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=expency.save'); ?>" method="post" name="adminForm" id="expency-form">
        <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        <div class="form-horizontal">
            <?php echo $this->form->renderField('description'); ?>
            <?php echo $this->form->renderField('subproject_id'); ?>
            <?php echo $this->form->renderField('date'); ?>
            <?php echo $this->form->renderField('type'); ?>
            <?php echo $this->form->renderField('value'); ?>
        </div>
        <input type="hidden" name="task" value="expency.edit">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>

