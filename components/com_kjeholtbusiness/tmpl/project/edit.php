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
        <?php echo $this->form->getInput('id'); ?>
        <div class="form-horizontal">
            <?php echo $this->form->renderField('name'); ?>
            <?php echo $this->form->renderField('description'); ?>
            <?php echo $this->form->renderField('property_name'); ?>
            <?php echo $this->form->renderField('start_date'); ?>
            <?php echo $this->form->renderField('status'); ?>
            <?php echo $this->form->renderField('customer_id'); ?>
            <?php echo $this->form->renderField('company_id'); ?>
        </div>
        <input type="hidden" name="task" value="project.save">
        <?php echo HTMLHelper::_('form.token'); ?>
        <button type="submit" class="btn btn-primary">
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=projectsummary&id=' . (int) $this->item->id); ?>">
            <?php echo Text::_('JCANCEL'); ?>
        </a>
    </form>
</div>

