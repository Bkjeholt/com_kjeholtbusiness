<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Log\Log;


Log::add('tmpl/project/show.php: ', Log::DEBUG, 'com_kjeholtbusiness');
$app = Factory::getApplication();


// set up client-side validation - we also need class="form-validate" on the <form> below and
// will also need to set the js validation rule in the class attribute of the field in the form XML file

//$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
// $wa->useScript('form.validate');

// include the no-uppercase.js script for client validation
// (This will pull in form.validate as a dependency, so the above line isn't really necessary in this case)
// $wa->useScript('com_exampleform.validate-no-uppercase');

/*
$postdata = $app->getUserState('com_kjeholtbusiness.project.postdata');
if ($postdata) {
    echo '<h3>Senast postade data</h3>';
    echo '<pre>' . htmlspecialchars(print_r($postdata, true), ENT_QUOTES) . '</pre>';
}
*/
?>
<?php Log::add('ProjectsTmpl->default->items result= ' . htmlspecialchars(print_r($this->items, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');?>
<?php Log::add('ProjectsTmpl->default->item result= ' . htmlspecialchars(print_r($this->item, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');?>

<h1>Sammanfattning av uppdraget</h1>

<?php 
$view = $app->input->getCmd('view');
$layout = $app->input->getCmd('layout', 'default');
$task = $app->input->getCmd('task');
$projectId = (int) $app->input->getCmd('projectid');
?>
<p>
view   = <?php echo htmlspecialchars($view, ENT_QUOTES); ?> <br/>
layout = <?php echo htmlspecialchars($layout, ENT_QUOTES); ?> 	<br/>
task   = <?php echo htmlspecialchars($task, ENT_QUOTES); ?>	<br/>
id     = <?php echo htmlspecialchars($projectId, ENT_QUOTES); ?> </p>	

<table>
	<thead>
	<tr>
        <th>Namn</th>
        <th>Beskrivning</th>
        <th>Fastighetsbeteckning</th>
    </tr>
    </thead>
	<tbody>
    <tr>
        <td><?php echo htmlspecialchars($this->item->name, ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($this->item->description, ENT_QUOTES); ?></td>
        <td><?php echo htmlspecialchars($this->item->property_name, ENT_QUOTES); ?></td>
    </tr>
    </tbody>
    
</table>
<?php 

if ($this->item) {
    echo '<h3>Aktuell data</h3>';
    echo '<pre>' . htmlspecialchars(print_r($this->item, true), ENT_QUOTES) . '</pre>';
}
$subProjects = $this->item->subproject_subforms;

if (!empty($subProjects)) { ?>
<h2>Deluppdrag</h2>
<table>
	<thead>
		<tr>
			<th>Namn</th>
			<th>Beskrivning</th>
			<th>Startdag</th>
			<th>Status</th>
		</tr>
	</thead>
    <?php foreach ($subProjects as $subProject) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($subProject->name, ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($subProject->description, ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($subProject->start_date, ENT_QUOTES) . '</td>';
        echo '<td>' . htmlspecialchars($subProject->status, ENT_QUOTES) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo '<p>Inga delprojekt.</p>';
}
?>