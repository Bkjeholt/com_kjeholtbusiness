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
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;
?>
<div class="kjeholtbusiness-companies">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANIES_TITLE'); ?></h1>

    <?php if (CompanyAcl::isSuiteSuperAdmin()) : ?>
        <a class="btn btn-primary mb-3" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=company&layout=edit'); ?>">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANIES_NEW'); ?>
        </a>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_NAME'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ORG_NUMBER'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_CITY'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_EMAIL'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_PHONE'); ?></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <td>
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=company&id=' . (int) $item->id); ?>">
                                <?php echo $this->escape($item->name); ?>
                            </a>
                        </td>
                        <td><?php echo $this->escape($item->org_number ?? ''); ?></td>
                        <td><?php echo $this->escape($item->city ?? ''); ?></td>
                        <td><?php echo $this->escape($item->email ?? ''); ?></td>
                        <td><?php echo $this->escape($item->phone ?? ''); ?></td>
                        <td>
                            <?php if (CompanyAcl::isSuiteSuperAdmin()) : ?>
                                <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=companies'); ?>"
                                      method="post" class="d-inline"
                                      onsubmit="return confirm('<?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_CONFIRM_DELETE'); ?>');">
                                    <input type="hidden" name="task" value="company.delete" />
                                    <input type="hidden" name="id" value="<?php echo (int) $item->id; ?>" />
                                    <?php echo HTMLHelper::_('form.token'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <?php echo Text::_('JACTION_DELETE'); ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($this->items)) : ?>
                    <tr><td colspan="6"><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANIES_EMPTY'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
