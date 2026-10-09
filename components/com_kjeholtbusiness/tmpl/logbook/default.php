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
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\LogbookAcl;

/**
 * Make #<id> references in event texts clickable.
 * timereport -> view=timereportedit, expense -> view=expense
 */
function renderLogText(string $text, string $event): string
{
    if (strpos($event, 'expense') === 0) {
        $view = 'expense';
    } elseif (strpos($event, 'timereport') === 0) {
        $view = 'timereportedit';
    } else {
        return $text;
    }

    return preg_replace_callback(
        '/#(\d+)/',
        function ($m) use ($view) {
            return '<a href="' . Route::_('index.php?option=com_kjeholtbusiness&view=' . $view . '&id=' . (int) $m[1]) . '">#' . (int) $m[1] . '</a>';
        },
        $text
    );
}
?>
<div class="kjeholtbusiness-logbook">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_TITLE'); ?></h1>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_TIME'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_USER'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_EVENT'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_DESCRIPTION'); ?></th>
                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_COMMENT'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($this->items as $item) : ?>
                    <tr>
                        <td><?php echo HTMLHelper::_('date', $item->event_time, Text::_('DATE_FORMAT_LC4')); ?></td>
                        <td><?php echo $this->escape($item->user_name ?? $item->user_id); ?></td>
                        <td><?php echo $this->escape($item->event); ?></td>
                        <td><?php echo renderLogText($this->escape($item->event_text), $this->escape($item->event)); ?></td>
                        <td>
                            <?php if (LogbookAcl::canUpdateComment($item)) : ?>
                                <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=logbook'); ?>"
                                      method="post" class="d-flex gap-1">
                                    <input type="hidden" name="task" value="logbook.updateComment" />
                                    <input type="hidden" name="id" value="<?php echo (int) $item->id; ?>" />
                                    <input type="text" name="comment" class="form-control form-control-sm"
                                           value="<?php echo $this->escape($item->comment ?? ''); ?>" />
                                    <?php echo HTMLHelper::_('form.token'); ?>
                                    <button type="submit" class="btn btn-secondary btn-sm">
                                        <?php echo Text::_('JSAVE'); ?>
                                    </button>
                                </form>
                            <?php else : ?>
                                <?php echo $this->escape($item->comment ?? ''); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($this->items)) : ?>
                    <tr><td colspan="5"><?php echo Text::_('COM_KJEHOLTBUSINESS_LOGBOOK_EMPTY'); ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
