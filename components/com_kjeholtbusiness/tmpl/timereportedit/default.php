<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="com-kjeholtbusiness-timereportedit">
    <h2><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTEDIT_TITLE'); ?></h2>

    <?php if ($this->isFrozen) : ?>
        <div class="alert alert-info">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_ERROR_FROZEN'); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereports'); ?>"
          method="post" name="adminForm" id="timereportEditForm" class="form-validate">
        <fieldset>
            <?php echo $this->form->renderFieldset('timereportEditFieldset'); ?>
        </fieldset>

        <?php if (!$this->isFrozen) : ?>
            <button type="submit" class="btn btn-primary">
                <?php echo Text::_('JSAVE'); ?>
            </button>
        <?php endif; ?>
        <a class="btn btn-secondary"
           href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereports'); ?>">
            <?php echo Text::_('JCANCEL'); ?>
        </a>

        <input type="hidden" name="task" value="timereportedit.save" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
