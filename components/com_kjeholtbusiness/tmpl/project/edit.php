<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
?>
<div class="kjeholtbusiness-project">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_TITLE'); ?></h1>
    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=project.save'); ?>" method="post" name="adminForm" id="project-form">
        <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>
        <div class="form-horizontal">
            <?php echo $this->form->renderField('description'); ?>
            <?php echo $this->form->renderField('property_name'); ?>
            <?php echo $this->form->renderField('start_date'); ?>
            <?php echo $this->form->renderField('status'); ?>
            <?php echo $this->form->renderField('customer_id'); ?>
            <?php echo $this->form->renderField('company_id'); ?>
        </div>
        <input type="hidden" name="task" value="project.edit">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>

