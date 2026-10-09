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
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\CompanyAcl;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\LogbookAcl;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\TimereportAcl;

$user = Factory::getUser();
?>
<div class="kjeholtbusiness-dashboard">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_DASHBOARD_TITLE'); ?></h1>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header"><?php echo Text::_('COM_KJEHOLTBUSINESS_DASHBOARD_TIME'); ?></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereportstart'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTSTART_TITLE'); ?>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereports'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_TITLE'); ?>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timecharts'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_TIMECHARTS_TITLE'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header"><?php echo Text::_('COM_KJEHOLTBUSINESS_DASHBOARD_PROJECTS'); ?></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=projects'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_TITLE'); ?>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subprojects'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_SUBPROJECTS_TITLE'); ?>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=customers'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_CUSTOMERS_TITLE'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header"><?php echo Text::_('COM_KJEHOLTBUSINESS_DASHBOARD_ECONOMY'); ?></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=expenses'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_EXPENSES_TITLE'); ?>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=invoices'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_INVOICES_TITLE'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header"><?php echo Text::_('COM_KJEHOLTBUSINESS_DASHBOARD_COMPANY'); ?></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=company'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANY_TITLE'); ?>
                            </a>
                        </li>
                        <?php if (CompanyAcl::mayListAllCompanies()) : ?>
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=companies'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_COMPANIES_TITLE'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-header"><?php echo Text::_('COM_KJEHOLTBUSINESS_DASHBOARD_ADMIN'); ?></div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=logbook'); ?>">
                                <?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_TITLE'); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
