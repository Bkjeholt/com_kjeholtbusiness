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
<div class="kjeholtbusiness-company">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_EDIT_TITLE'); ?></h1>

    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=company.save'); ?>"
          method="post" name="adminForm" id="company-form" class="form-validate">
        <?php echo $this->form->getInput('id'); ?>
        <div class="form-horizontal">
            <?php foreach (['name', 'org_number', 'address', 'postal_code', 'city', 'phone', 'website', 'email', 'bank_name', 'bankgiro', 'iban'] as $field) : ?>
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
        <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=company'); ?>">
            <?php echo Text::_('JCANCEL'); ?>
        </a>
    </form>
</div>
