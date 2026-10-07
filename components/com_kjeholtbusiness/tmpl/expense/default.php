<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="com-kjeholtbusiness-expense">
    <h2><?php echo Text::_($this->isNew ? 'COM_KJEHOLTBUSINESS_EXPENSE_NEW_TITLE' : 'COM_KJEHOLTBUSINESS_EXPENSE_EDIT_TITLE'); ?></h2>

    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=expense'); ?>"
          method="post" name="expenseForm" id="expenseForm" class="form-validate">
        <fieldset>
            <?php echo $this->form->renderFieldset('expenseFieldset'); ?>
        </fieldset>

        <button type="submit" class="btn btn-primary">
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <a class="btn btn-secondary"
           href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=expenses'); ?>">
            <?php echo Text::_('JCANCEL'); ?>
        </a>

        <input type="hidden" name="task" value="expense.save" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
