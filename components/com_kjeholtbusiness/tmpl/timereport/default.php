<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Log\Log;


Log::add('tmpl/timereport/default.php: ', Log::DEBUG, 'com_kjeholtbusiness');
// set up client-side validation - we also need class="form-validate" on the <form> below and
// will also need to set the js validation rule in the class attribute of the field in the form XML file

//$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
// $wa->useScript('form.validate');

// include the no-uppercase.js script for client validation
// (This will pull in form.validate as a dependency, so the above line isn't really necessary in this case)
// $wa->useScript('com_exampleform.validate-no-uppercase');

$postdata = Factory::getApplication()->getUserState('com_kjeholtbusiness.timereport.postdata');
Factory::getApplication()->setUserState('com_kjeholtbusiness.timereport.postdata', null); // Töm efter visning

if ($postdata) {
    echo '<h3>Senast postade data</h3>';
    echo '<pre>' . htmlspecialchars(print_r($postdata, true), ENT_QUOTES) . '</pre>';
}


?>
<h1>COM_KJEHOLTBUSINESS_TIMEREPORT_LIST_OF_ONGOING_HEADING</h1>

    <?php // echo '<pre>ABC: <br/>' . htmlspecialchars($post, ENT_QUOTES) . '</pre>'; ?>


<?php
Factory::getApplication()->getDocument()->getWebAssetManager()->useScript('form.validate');

// Display the data from the last form submission (sent in the last HTTP POST request)
if ($this->postdata) {
    ob_start();
    var_dump($this->postdata);
    $post = ob_get_contents();
    ob_end_clean();
}

?>

<?php if ($this->postdata) : ?>
    <h3>Unvalidated data received from last form submission</h3>
    <?php echo '<pre>' . htmlspecialchars($post, ENT_QUOTES) . '</pre>'; ?>
<?php endif; ?>

<?php
$postdata = Factory::getApplication()->getUserState('com_kjeholtbusiness.timereport.postdata');
Factory::getApplication()->setUserState('com_kjeholtbusiness.timereport.postdata', null); // Töm efter visning
if ($postdata) {
    echo '<h3>Senast postade data</h3>';
    echo '<pre>' . htmlspecialchars(print_r($postdata, true), ENT_QUOTES) . '</pre>';
}
