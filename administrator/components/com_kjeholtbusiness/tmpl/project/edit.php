<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=project.save'); ?>"
      method="post" name="adminForm" id="project-form" class="form-validate">
    <div class="form-horizontal">
        <?php echo $this->form->renderField('name'); ?>
        <?php echo $this->form->renderField('description'); ?>
        <?php echo $this->form->renderField('property_name'); ?>
        <?php echo $this->form->renderField('start_date'); ?>
        <?php echo $this->form->renderField('status'); ?>
        <?php echo $this->form->renderField('customer_id'); ?>
        <?php echo $this->form->renderField('company_id'); ?>
        <?php echo $this->form->renderField('article_id'); ?>
    </div>
    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
