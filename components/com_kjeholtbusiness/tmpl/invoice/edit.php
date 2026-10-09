<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
?>
<div class="kjeholtbusiness-invoice">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICE_EDIT_TITLE'); ?></h1>

    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=invoice.save'); ?>"
          method="post" name="adminForm" id="invoice-form" class="form-validate">
        <?php echo $this->form->getInput('id'); ?>
        <div class="form-horizontal">
            <?php foreach (['invoice_number', 'customer_id', 'project_id', 'date', 'due_date', 'subprojects', 'rot_reduction', 'rot_percentage', 'rut_reduction', 'rut_percentage'] as $field) : ?>
                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel($field); ?>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput($field); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php echo HTMLHelper::_('form.token'); ?>
        <button type="submit" class="btn btn-primary">
            <?php echo Text::_('JSAVE'); ?>
        </button>
        <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=invoices'); ?>">
            <?php echo Text::_('JCANCEL'); ?>
        </a>
    </form>
</div>
