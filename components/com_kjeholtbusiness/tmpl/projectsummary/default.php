<?php
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Log\Log;
Log::add('Template: projectsummary/default.php', Log::DEBUG, 'com_kjeholtbusiness');

//-----------------------------------------------------------
// Present status of these project.
//-----------------------------------------------------------
?>
<div class="com-kjeholtbusiness-projectsummary">
    <h1><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_SUMMARY'); ?></h1>
    <p><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_SUMMARY_DESC'); ?></p>
</div>
<?php 
// ----------------------------------------------------------
// Status for all related subprojects and related expenses and timereports.
// ----------------------------------------------------------


?>
<div class="com-kjeholtbusiness-projects">
    <form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=projects'); ?>" method="post" name="adminForm" id="adminForm">
        <div class="row">
            <div class="col-md-12">
                <div id="j-main-container" class="j-main-container">
                    <div class="table-responsive">
                        <table class="table table-striped" id="projectList">
                            <thead>
                                <tr>
<!--                                   <th scope="col" style="width:1%"><?php echo Text::_('JGRID_HEADING_ID'); ?></th>  -->
                                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_NAME'); ?></th>
                                   <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROPERTY_NAME'); ?></th>
                                    <th scope="col" style="width:10%"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_STATUS'); ?></th>
                                    <th scope="col" style="width:15%"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_START_DATE'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($this->items as $i => $item) : ?>
                                    <tr>
<!--                                         <th scope="row"><?php echo $item->id; ?></th>  -->
                                        <td><a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=projectsummary&id=' . $item->id); ?>"><?php echo $this->escape($item->name); ?></a></td>
                                        <td><?php echo $item->property_name; ?></td>
                                        <td><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_STATUS_' . strtoupper($item->status)); ?></td>
                                        <td><?php echo $item->start_date; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>

