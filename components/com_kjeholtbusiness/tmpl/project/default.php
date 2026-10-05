<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
?>

<div class="kjeholtbusiness-project">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_TITLE'); ?></h1>

    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=project.save&id=' . (int) $this->item->id); ?>"
          method="post" name="adminForm" id="project-form" class="form-validate">

        <div class="form-horizontal">
            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('name'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('name'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('description'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('description'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('property_name'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('property_name'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('start_date'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('start_date'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('status'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('status'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('customer_id'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('customer_id'); ?>
                </div>
            </div>

            <div class="control-group">
                <div class="control-label">
                    <?php echo $this->form->getLabel('company_id'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('company_id'); ?>
                </div>
            </div>
        </div>

        <input type="hidden" name="task" value="">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>
