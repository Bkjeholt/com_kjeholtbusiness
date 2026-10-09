<?php
/**
 * @package     KjeholtEngineering.Component.KjeholtBusiness
 * @subpackage  com_kjeholtbusiness
 *
 * @copyright   Copyright (C) 2026 Kjeholt Engineering and Services AB. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
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

    <?php if (!empty($this->item) && Factory::getUser()->authorise('core.edit', 'com_kjeholtbusiness')) : ?>
        <a class="btn btn-primary" href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=company&layout=edit&id=' . (int) $this->item->id); ?>">
            <?php echo Text::_('JACTION_EDIT'); ?>
        </a>
    <?php endif; ?>
</div>
