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
<div class="kjeholtbusiness-subproject">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECT_TITLE'); ?></h1>

    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=subproject.save'); ?>"
          method="post" name="adminForm" id="subproject-form" class="form-validate">
        <?php echo $this->form->getInput('id'); ?>
        <?php echo $this->form->getInput('project_id'); ?>
        <div class="form-horizontal">
            <?php foreach ([
                'name',
                'description',
                'start_date',
                'hourly_rate',
                'estimated_amount_of_hours',
                'rot',
                'rut',
                'vat',
            ] as $field) : ?>
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
        <a class="btn btn-secondary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subproject&id=' . (int) $this->item->id); ?>">
            <?php echo Text::_('JCANCEL'); ?>
        </a>
    </form>
</div>
