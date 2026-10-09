<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\TimereportAcl;
use Joomla\CMS\HTML\HTMLHelper;
?>
<div class="com-kjeholtbusiness-timereports">
    <h2><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_TITLE'); ?></h2>

    <?php if (empty($this->items)) : ?>
        <p><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_NO_ITEMS'); ?></p>
    <?php else : ?>
        <div class="table-responsive">
            <table class="table table-striped" id="timereportsList">
                <thead>
                    <tr>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_PROJECT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_SUBPROJECT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_START'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_END'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_ADJUSTMENT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_HOURS'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_STATUS'); ?></th>
                        <th scope="col"><?php echo Text::_('JACTION_EDIT'); ?></th>
                        <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_VALIDATE'); ?></th>
                        <th scope="col"><?php echo Text::_('JACTION_DELETE'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <?php $isFrozen = ($item->status === 'froozen'); ?>
                        <tr>
                            <td><?php echo $this->escape($item->project_name ?? '-'); ?></td>
                            <td><?php echo $this->escape($item->subproject_name ?? '-'); ?></td>
                            <td><?php echo HTMLHelper::_('date', $item->start_time, 'Y-m-d H:i'); ?></td>
                            <td><?php echo $item->end_time ? HTMLHelper::_('date', $item->end_time, 'Y-m-d H:i') : '-'; ?></td>
                            <td><?php echo (int) $item->adjustment; ?></td>
                            <td><?php
                                $hours = \KjeholtEngineering\Component\KjeholtBusiness\Site\Model\TimereportsModel::calculateHours(
                                    $item->start_time,
                                    $item->end_time,
                                    (int) $item->adjustment
                                );
                                echo $hours === null ? '-' : number_format($hours, 2, ',', ' ');
                            ?></td>
                            <td><?php echo $this->escape($item->status); ?></td>
                            <td>
                                <?php if ($isFrozen || $item->status === 'validated') : ?>
                                    <?php if ($isFrozen) : ?>
                                        <span class="badge bg-secondary"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_FROZEN'); ?></span>
                                    <?php else : ?>
                                        <span class="badge bg-success"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_STATUS_VALIDATED'); ?></span>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <a class="btn btn-secondary btn-sm"
                                       href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereportedit&id=' . (int) $item->id); ?>">
                                        <?php echo Text::_('JACTION_EDIT'); ?>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($item->status === 'ended') : ?>
                                    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereports'); ?>"
                                          method="post" class="d-inline">
                                        <input type="hidden" name="task" value="timereportedit.validate" />
                                        <input type="hidden" name="id" value="<?php echo (int) $item->id; ?>" />
                                        <?php echo HTMLHelper::_('form.token'); ?>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_VALIDATE'); ?>
                                        </button>
                                    </form>
                                <?php elseif ($item->status === 'validated') : ?>
                                    <span class="badge bg-success"><?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_STATUS_VALIDATED'); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (TimereportAcl::canDelete($item)) : ?>
                                <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereports'); ?>"
                                      method="post" class="d-inline"
                                      onsubmit="return confirm('<?php echo Text::_('COM_KJEHOLTBUSINESS_TIMEREPORTS_CONFIRM_DELETE'); ?>');">
                                    <input type="hidden" name="task" value="timereportedit.delete" />
                                    <input type="hidden" name="id" value="<?php echo (int) $item->id; ?>" />
                                    <?php echo HTMLHelper::_('form.token'); ?>
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <?php echo Text::_('JACTION_DELETE'); ?>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
