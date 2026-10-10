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
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\BssAcl;
use Joomla\CMS\Factory;
?>
<div class="kjeholtbusiness-customers">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_TITLE'); ?></h1>

    <?php if (BssAcl::hasAccessAny('project:edit')) : ?>
        <a class="btn btn-primary mb-3" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=customer&layout=edit'); ?>">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_NEW'); ?>
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_NAME'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_SSN'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_PROPERTY_NAME'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_CITY'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_PHONE'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_EMAIL'); ?></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <td><?php echo $this->escape($item->name); ?></td>
                        <td><?php echo $this->escape($item->ssn ?? ''); ?></td>
                        <td><?php echo $this->escape($item->property_name ?? ''); ?></td>
                        <td><?php echo $this->escape($item->city ?? ''); ?></td>
                        <td><?php echo $this->escape($item->phone ?? ''); ?></td>
                        <td><?php echo $this->escape($item->email ?? ''); ?></td>
                        <td>
                            <?php if ((int) $item->created_by === (int) Factory::getUser()->id
                                || BssAcl::hasAccessAny('project:edit')) : ?>
                                <a class="btn btn-sm btn-outline-secondary"
                                   href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=customer&layout=edit&id=' . (int) $item->id); ?>">
                                    <?php echo Text::_('JACTION_EDIT'); ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($this->items)) : ?>
                    <tr><td colspan="7"><?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_EMPTY'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
