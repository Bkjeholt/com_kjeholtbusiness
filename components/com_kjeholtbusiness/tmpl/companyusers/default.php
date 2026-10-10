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
<div class="kjeholtbusiness-companyusers">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_TITLE'); ?>: <?php echo $this->escape($this->companyName); ?></h1>

    <?php if (!empty($this->availableUsers)) : ?>
    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=companyusers&company_id=' . (int) $this->companyId); ?>"
          method="post" class="mb-3 d-flex gap-2">
        <input type="hidden" name="task" value="companyusers.connect" />
        <select name="user_id" class="form-select">
            <?php foreach ($this->availableUsers as $user) : ?>
                <option value="<?php echo (int) $user->id; ?>">
                    <?php echo $this->escape($user->name . ' (' . $user->username . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php echo HTMLHelper::_('form.token'); ?>
        <button type="submit" class="btn btn-primary">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_CONNECT'); ?>
        </button>
    </form>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_NAME'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_USERNAME'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_EMAIL'); ?></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <td><?php echo $this->escape($item->name); ?></td>
                        <td><?php echo $this->escape($item->username); ?></td>
                        <td><?php echo $this->escape($item->email); ?></td>
                        <td>
                            <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=companyusers&company_id=' . (int) $this->companyId); ?>"
                                  method="post" class="d-inline"
                                  onsubmit="return confirm('<?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_CONFIRM_DISCONNECT'); ?>');">
                                <input type="hidden" name="task" value="companyusers.disconnect" />
                                <input type="hidden" name="user_id" value="<?php echo (int) $item->id; ?>" />
                                <?php echo HTMLHelper::_('form.token'); ?>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_DISCONNECT'); ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($this->items)) : ?>
                    <tr><td colspan="4"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANYUSERS_EMPTY'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
