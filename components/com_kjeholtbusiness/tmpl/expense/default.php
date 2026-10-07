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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var exclField  = document.getElementById('jform_amount_excl_vat');
    var vatField   = document.getElementById('jform_vat');
    var applyField  = document.querySelector('input[name="jform[apply_rounding]"]:checked');
    var applyFields = document.querySelectorAll('input[name="jform[apply_rounding]"]');
    var roundField = document.getElementById('jform_rounding');
    var inclField  = document.getElementById('jform_amount');

    function applyRounding() {
        var checked = document.querySelector('input[name="jform[apply_rounding]"]:checked');
        return checked && checked.value === '1';
    }

    function recalc() {
        var excl = parseFloat(exclField.value.replace(',', '.'));
        var vat  = parseFloat(vatField.value.replace(',', '.'));

        if (isNaN(excl)) excl = 0;
        if (isNaN(vat))  vat  = 0;

        var raw  = Math.round((excl + vat) * 100) / 100;
        var incl, rnd;

        if (applyRounding()) {
            incl = Math.round(raw);
            rnd  = Math.round((incl - raw) * 100) / 100;
            roundField.value = (incl ? (rnd >= 0 ? '+' : '') + rnd.toFixed(2) : '');
        } else {
            incl = raw;
            roundField.value = '';
        }

        inclField.value = incl ? incl.toFixed(2) : '';
    }

    exclField.addEventListener('input', recalc);
    vatField.addEventListener('input', recalc);
    applyFields.forEach(function (f) { f.addEventListener('change', recalc); });
    recalc();
});
</script>
