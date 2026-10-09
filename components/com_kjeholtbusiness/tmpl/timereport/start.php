<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Log\Log;


// set up client-side validation - we also need class="form-validate" on the <form> below and
// will also need to set the js validation rule in the class attribute of the field in the form XML file

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('form.validate');

// include the no-uppercase.js script for client validation
// (This will pull in form.validate as a dependency, so the above line isn't really necessary in this case)
// $wa->useScript('com_exampleform.validate-no-uppercase');


?>
<h3>COM_KJEHOLTBUSINESS_TIMEREPORT_START_HEADING</h3>
<form action="<?php echo Route::_('index.php?option=com_kjeholtbusiness&view=timereport_started'); ?>"
    class="form-validate" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <?php echo $this->form->renderFieldset('startTimeReportingFieldset');  ?>

    <button type="submit" class="btn btn-primary">Submit</button>

    <input type="hidden" name="task" value="timereport.submitStartTimeReport" />
    <?php echo HtmlHelper::_('form.token'); ?>
</form>