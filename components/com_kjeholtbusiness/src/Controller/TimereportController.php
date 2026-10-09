<?php
namespace KjeholtEngineering\Component\KjeholtBusiness\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use KjeholtEngineering\Component\KjeholtBusiness\Site\Helper\TimecardService;
use Joomla\CMS\Log\Log;


class TimereportController extends BaseController
{

    private function safeDateTime($value) {
        $value = trim((string)$value);
        if ($value === '' || $value === '0000-00-00 00:00:00') {
            return Factory::getDate()->format('Y-m-d H:i:s');
        }
        return $value;
    }

    private function storeTimeReport($commonData, $detailedData) {
        Log::add('TimeReportController->storeTimeReport ', Log::DEBUG, 'com_kjeholtbusiness');
        Log::add('TimeReportController->storeTimeReport commondata='.htmlspecialchars(print_r($commonData, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
        Log::add('TimeReportController->storeTimeReport detaileddata='.htmlspecialchars(print_r($detailedData, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
        
        $data = new \stdClass();
        $data->name = isset($commonData['name']) ? $commonData['name'] : 'odefinierat uppdrag';
        $data->created_by = (int) Factory::getUser()->id;
        $data->created_at = Factory::getDate()->toSql();
        $data->description = isset($commonData['description']) ? $commonData['description'] : 'Tidrapporten är kopplad till deluppdraget med id=' . $commonData['subproject_id'] . '.';
        $data->subproject_id = isset($commonData['subproject_id']) ? (int)$commonData['subproject_id'] : 0;
        if ($commonData->detail_selection == '1') {
            $data->start_time = $this->safeDateTime(Factory::getDate()->format('Y-m-d H:i:s'));
            $data->status = 'ongoing';
        }
        else {
            $data->start_time = $this->safeDateTime(
                (isset($detailedData['timereport_date']) && isset($detailedData['timereport_start_time']))
                    ? $detailedData['timereport_date'] . ' ' . $detailedData['timereport_start_time']
                    : null );
            $data->status = 'ongoing';

            if ($detailedData['timereport_finalize_selection'] > '1') {
                $data->end_time = $this->safeDateTime(
                    (isset($detailedData['timereport_date']) && isset($detailedData['timereport_end_time']))
                        ? $detailedData['timereport_date'] . ' ' . $detailedData['timereport_end_time']
                        : null
                );
                $data->status = 'ended';
            }
        }
        
        $db = Factory::getDbo();
        $db->insertObject('#__kjeholtbusiness_timecards', $data);
        return($db->insertid());
    }
    
    
    public function submitStartTimeReport($key = null, $urlVar = null)
    {
        // Check that this HTTP POST has come from our form
        // checkToken checks the token is valid and exits if it's not right
        $this->checkToken();
        
        Log::add('TRS: submit: ' . $urlVar, Log::DEBUG, 'com_kjeholtbusiness');
        
        
        $app   = Factory::getApplication();
        $model = $this->getModel('Timereport');
        $form = $model->getForm(null, false);
        if (!$form)
        {
            $app->enqueueMessage($model->getError(), 'error');
            return false;
        }
        
        // name of array 'jform' must match 'control' => 'jform' line in the model code
        $data  = $this->input->post->get('jform', array(), 'array');

        
        // This is validate() from the FormModel class, not the Form class
        // FormModel::validate() calls both Form::filter() and Form::validate() methods
        $validData = $model->validate($form, $data);
        
        if ($validData === false)
        {
            $errors = $model->getErrors();
            foreach ($errors as $error)
            {
                if ($error instanceof \Exception)
                {
                    $app->enqueueMessage($error->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($error, 'warning');
                }
            }
            $app->setUserState('com_kjeholtbusiness.timereport.postdata', $data);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereport', false));
        }
        else
        {
            $userId = (int) Factory::getUser()->id;

            // End any previous ongoing report before starting a new one
            TimecardService::endOngoing($userId);

            // Auto-close reports still ongoing after 24 hours
            TimecardService::autoCloseAfter24h($userId);

            Log::add('TimereportController: submitStartTimeReport: '. $validData['name'], Log::DEBUG, 'com_kjeholtbusiness');
//            var_dump($validData);

            $db = Factory::getDbo();

            Log::add('TimereportController: submitStartTimeReport: ValidData=' . htmlspecialchars(print_r($validData, true), ENT_QUOTES)
                , Log::DEBUG, 'com_kjeholtbusiness');
            
            foreach ($validData['details_subform'] as $validDetailedData) {
                Log::add('TimereportController: submitStartTimeReport: ValidDetailedData ' . htmlspecialchars(print_r($validDetailedData, true), ENT_QUOTES), Log::DEBUG, 'com_kjeholtbusiness');
                
                $dbInsertId = $this->storeTimeReport($validData, $validDetailedData);

                Log::add('TimereportController: submitStartTimeReport: Timereport created. id= '. $dbInsertId, Log::DEBUG, 'com_kjeholtbusiness');

                $app->enqueueMessage("Timecard is created with id=" . $dbInsertId, 'notice');
                
            }

            $app->setUserState('com_kjeholtbusiness.timereport.postdata', $validData);
            $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereport&layout=listofopentimereports', false));
        }


    }

    public function endSession($key = null, $urlVar = null)
    {
        $this->checkToken();

        $app   = Factory::getApplication();
        $db    = Factory::getDbo();
        $user  = Factory::getUser();

        $jform = $app->input->post->get('jform', [], 'array');

        $endTime = trim((string) ($jform['end_time'] ?? '')) ?: Factory::getDate()->toSql();
        $description = trim((string) ($jform['description'] ?? ''));

        // Apply the editable description to the ongoing report(s) before ending
        if ($description !== '') {
            $query = $db->getQuery(true)
                ->update($db->quoteName('#__kjeholtbusiness_timecards'))
                ->set($db->quoteName('description') . ' = ' . $db->quote($description))
                ->where($db->quoteName('created_by') . ' = ' . (int) $user->id)
                ->where($db->quoteName('status') . ' = ' . $db->quote('ongoing'));
            $db->setQuery($query)->execute();
        }

        $ended = TimecardService::endOngoing((int) $user->id, $endTime);

        if ($ended > 0) {
            $app->enqueueMessage(
                Text::plural('COM_KJEHOLTBUSINESS_TIMEREPORT_N_ENDED', $ended),
                'message'
            );
        } else {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORT_NONE_ONGOING'), 'notice');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereports', false));
    }

    public function stop($key = null, $urlVar = null)
    {
        $this->checkToken();

        $app    = Factory::getApplication();
        $userId = (int) Factory::getUser()->id;

        $ended = TimecardService::endOngoing($userId);

        if ($ended > 0) {
            $app->enqueueMessage(
                Text::plural('COM_KJEHOLTBUSINESS_TIMEREPORT_N_ENDED', $ended),
                'message'
            );
        } else {
            $app->enqueueMessage(Text::_('COM_KJEHOLTBUSINESS_TIMEREPORT_NONE_ONGOING'), 'notice');
        }

        $this->setRedirect(Route::_('index.php?option=com_kjeholtbusiness&view=timereport', false));
    }
}