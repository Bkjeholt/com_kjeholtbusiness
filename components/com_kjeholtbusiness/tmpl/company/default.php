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
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;
?>
<div class="kjeholtbusiness-company">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_TITLE'); ?></h1>

    <?php if (empty($this->item)) : ?>
        <div class="alert alert-warning">
            <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_NOT_FOUND'); ?>
        </div>
    <?php else : ?>
        <div class="company-info">
            <h2><?php echo $this->escape($this->item->name); ?></h2>
            <dl class="dl-horizontal">
                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_NAME'); ?></dt>
                <dd><?php echo $this->escape($this->item->name); ?></dd>

                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ADDRESS'); ?></dt>
                <dd>
                    <?php echo $this->escape($this->item->address ?? ''); ?><br/>
                    <?php echo $this->escape($this->item->postal_code ?? ''); ?>
                    <?php echo $this->escape($this->item->city ?? ''); ?>
                </dd>

                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_PHONE'); ?></dt>
                <dd><?php echo $this->escape($this->item->phone ?? ''); ?></dd>

                <?php if (!empty($this->item->website)) : ?>
                    <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_WEBSITE'); ?></dt>
                    <dd>
                        <a href="<?php echo $this->escape($this->item->website); ?>" target="_blank" rel="noopener">
                            <?php echo $this->escape($this->item->website); ?>
                        </a>
                    </dd>
                <?php endif; ?>

                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_EMAIL'); ?></dt>
                <dd><?php echo $this->escape($this->item->email ?? ''); ?></dd>

                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_BANK_NAME'); ?></dt>
                <dd><?php echo $this->escape($this->item->bank_name ?? ''); ?></dd>

                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_BANKGIRO'); ?></dt>
                <dd><?php echo $this->escape($this->item->bankgiro ?? ''); ?></dd>

                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_IBAN'); ?></dt>
                <dd><?php echo $this->escape($this->item->iban ?? ''); ?></dd>

                <dt><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ORG_NUMBER'); ?></dt>
                <dd><?php echo $this->escape($this->item->org_number ?? ''); ?></dd>
            </dl>
        </div>
    <?php endif; ?>


    <?php if (!empty($this->item) && CompanyAcl::isSuiteAdmin()) : ?>
        <div class="company-admin mt-4">
            <h3><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ADMIN'); ?></h3>
            <?php if (!empty($this->adminUser)) : ?>
                <p>
                    <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ADMIN_CURRENT'); ?>:
                    <strong><?php echo $this->escape($this->adminUser->name); ?></strong>
                    (<?php echo $this->escape($this->adminUser->username); ?>)
                </p>
            <?php else : ?>
                <p><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ADMIN_NONE'); ?></p>
            <?php endif; ?>
            <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=company&id=' . (int) $this->item->id); ?>"
                  method="post" class="d-flex gap-2">
                <input type="hidden" name="task" value="company.admin" />
                <select name="user_id" class="form-select">
                    <option value=""><?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ADMIN_SELECT'); ?></option>
                    <?php foreach ($this->availableAdminUsers as $user) : ?>
                        <option value="<?php echo (int) $user->id; ?>">
                            <?php echo $this->escape($user->name . ' (' . $user->username . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php echo HTMLHelper::_('form.token'); ?>
                <button type="submit" class="btn btn-primary">
                    <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ADMIN_ASSIGN'); ?>
                </button>
            </form>
            <p class="small text-muted mt-1">
                <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_ADMIN_HINT'); ?>
            </p>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->item) && CompanyAcl::canEditCompany($this->item)) : ?>
        <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=company&layout=edit&id=' . (int) $this->item->id); ?>">
            <?php echo Text::_('JACTION_EDIT'); ?>
        </a>
    <?php endif; ?>
</div>
