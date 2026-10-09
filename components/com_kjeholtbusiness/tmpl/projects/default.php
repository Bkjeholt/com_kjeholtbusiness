<?php
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Log\Log;

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
                                    <th scope="col" style="width:1%"><?php echo Text::_('JGRID_HEADING_ID'); ?></th>
                                    <th scope="col"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_NAME'); ?></th>
                                    <th scope="col" style="width:10%"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_STATUS'); ?></th>
                                    <th scope="col" style="width:15%"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTS_START_DATE'); ?></th>
                                    <th scope="col" style="width:10%"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TITLE'); ?></th>
<!--                                   <th scope="col" style="width:1%"><?php echo Text::_('JGRID_HEADING_ID'); ?></th>  -->
                                    <th scope="col"><?php echo Text::_('Uppdrag'); ?></th>
                                   <th scope="col"><?php echo Text::_('Fastighet'); ?></th>
                                    <th scope="col" style="width:10%"><?php echo Text::_('Status'); ?></th>
                                    <th scope="col" style="width:15%"><?php echo Text::_('Startdag'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php Log::add('ProjectsTmpl->default->item result= ' . htmlspecialchars(print_r($this->items, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
                            ?>
                                <?php foreach ($this->items as $i => $item) : ?>
                                    <tr>
                                        <th scope="row"><?php echo $item->id; ?></th>
                                        <td><a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&task=project.edit&id=' . $item->id); ?>"><?php echo $this->escape($item->name); ?></a></td>
                                        <td><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECT_STATUS_' . strtoupper($item->status)); ?></td>
                                        <td><?php echo $item->start_date; ?></td>
                                        <td><a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=projectsummary&id=' . $item->id); ?>"><?php echo Text::_('COM_KJEHOLTBUSINESS_PROJECTSUMMARY_TITLE'); ?></a></td>
<!--                                         <th scope="row"><?php echo $item->id; ?></th>  -->
                                        <td><a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=project&layout=show&projectid=' . $item["id"]); ?>"><?php echo $this->escape($item["name"]); ?></a></td>
                                        <td><?php echo $item["property_name"]; ?></td>
                                        <td><?php echo Text::_(/*'COM_KJEHOLTBUSINESS_PROJECT_STATUS_' .*/ strtoupper($item["status"])); ?></td>
                                        <td><?php echo $item["start_date"]; ?></td>
                                    </tr>
                                    <tr>
                                    	<td> Beskrivning </td>
										<td colspan="3"><?php echo $item["description"]; ?></td>
									</tr>
									<?php foreach ($item['subprojects'] as $subProjectItem) : ?>
									<tr>
									    
										<td> Delprojekt </td>
										<td colspan="3">
											<a href="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=subproject&layout=show&subprojectid=' . $subProjectItem["id"]); ?>"><?php echo $this->escape($subProjectItem["name"]); ?></a><br/>
											<?php echo $subProjectItem["description"]; ?><br/>
											<?php echo $subProjectItem["status"]?>
									    </td>
									</tr>
									<?php endforeach; ?>
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

