<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use DateInterval;
use DateTime;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\EventPlanningsModel;
use Timbreuse\Models\EventSeriesModel;
use Timbreuse\Controllers\PersonalEventPlannings;

class EventSeries extends BaseController
{
    public PersonalEventPlannings $personalEventPlanningController;

    /**
     * Constructor
     */
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ): void {
        // Set Access level before calling parent constructor
        // Accessibility reserved to registered users
        $this->access_level = config('\User\Config\UserConfig')->access_lvl_registered;
        parent::initController($request, $response, $logger);

        // Load required helpers
        helper('form');

        // Load required helpers
        $this->personalEventPlanningController = new PersonalEventPlannings();
    }

    public function getDaysOfWeek() : array {
        return [
            'monday' => lang('tim_lang.monday'),
            'tuesday' => lang('tim_lang.tuesday'),
            'wednesday' => lang('tim_lang.wednesday'),
            'thursday' => lang('tim_lang.thursday'),
            'friday' => lang('tim_lang.friday')
        ];
    }

    public function getCreateSeriesHTML() : string {
        $eventSeriesModel = model(EventSeriesModel::class);

        $data = [
            'daysOfWeek' => $this->getDaysOfWeek(),
            'eventSerie' => null,
            'recurrenceFrequencies' => $eventSeriesModel->getReccurrenceFrequencyEnumValues()
        ];

        return json_encode(view('\Timbreuse\Views\eventSeries\create_series.php', $data));
    }
    
    /**
     * Get event planning and event serie errors.
     * Return an empty array if successfull
     *
     * @param  array $eventSerie
     * @param  array $eventPlanning
     * @return array
     */
    public function create(array $eventSerie, array $eventPlanning) : array {
        $errors = [];

        $eventSeriesModel = model(EventSeriesModel::class);
        $eventPlanningModel = model(EventPlanningsModel::class);

        $isEventSerieValid = $eventSeriesModel->validate($eventSerie);
        $iseventPlanningValid = $eventPlanningModel->validate($eventPlanning);

        if (!$isEventSerieValid || !$iseventPlanningValid) {
            $errors += $eventSeriesModel->errors();
            $errors += $eventPlanningModel->errors();
        } else {
            $id = $eventSeriesModel->insert($eventSerie, true);
    
            $newEventSerie = $eventSeriesModel->find($id);
    
            $errors = $this->createEventPlannings($newEventSerie, $eventPlanning);
        }

        return $errors;
    }
    
    /**
     * Create the event plannings corresponding to an event serie.
     * Return remaining errors
     *
     * @param  array $eventSerie
     * @param  array $eventPlanning
     * @return array
     */
    private function createEventPlannings(array $eventSerie, array $eventPlanning) : array {
        $planningErrors = [];
        $eventPlanningModel = model(EventPlanningsModel::class);

        $startDate = new DateTime($eventSerie['start_date']);
        $endDate = new DateTime($eventSerie['end_date']);

        $interval = $this->getInterval($eventSerie['recurrence_interval'], $eventSerie['recurrence_frequency']);

        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            foreach($eventSerie['days_of_week'] as $dayOfWeek) {
                $nextOccurrence = $this->getNextOccurrence($currentDate, $endDate, $dayOfWeek);
    
                if (!is_null($nextOccurrence)) {
                    $eventPlanningModel->insert([
                        'fk_event_series_id' => $eventSerie['id'],
                        'fk_user_group_id' => $eventPlanning['fk_user_group_id'],
                        'fk_user_sync_id' => $eventPlanning['fk_user_sync_id'],
                        'fk_event_type_id' => $eventPlanning['fk_event_type_id'],
                        'event_date' => $nextOccurrence->format('Y-m-d'),
                        'start_time' => $eventPlanning['start_time'],
                        'end_time' => $eventPlanning['end_time'],
                        'is_work_time' => $eventPlanning['is_work_time'],
                    ]);

                    $planningErrors += $eventPlanningModel->errors();
                }
            }

            $currentDate->add($interval);
        }

        return $planningErrors;
    }
    
    /**
     * Display update page and update an existing serie on POST
     *
     * @param  int $id
     * @return RedirectResponse|string
     */
    public function update(int $id) : RedirectResponse|string {
        $eventSeriesModel = model(EventSeriesModel::class);
        $eventPlanningModel = model(EventPlanningsModel::class);

        $eventSerie = $eventSeriesModel->findAllSeries($id);

        $route = $this->personalEventPlanningController->getPreviousRoute(url_is('*admin*'));

        if (is_null($eventSerie) || $this->checkPermissionBeforeDelete($eventSerie)) {
            return redirect()->to($route);
        }

        // Todo: replace the title on the update page

        $data = [
            'daysOfWeek' => $this->getDaysOfWeek(),
            'recurrenceFrequencies' => $eventSeriesModel->getReccurrenceFrequencyEnumValues(),
            'eventSerie' => $eventSerie,
            'route' => $route
        ];

        if (isset($_POST) && !empty($_POST)) {
            $eventSerieUpdate = [
                'start_date' =>$this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'recurrence_frequency' => $this->request->getPost('recurrence_frequency'),
                'recurrence_interval' => $this->request->getPost('recurrence_interval'),
                'days_of_week' => $this->request->getPost('days')
            ];
            $errors = $this->updateSerieAndGetErrors($id, $eventSerieUpdate);

            if (empty($errors)) {
                $eventPlannings = $eventPlanningModel->getAllBySerieId($id);
                $newEventSerie = $eventSeriesModel->find($id);
                $errors += $this->updateLinkedEventPlannings($eventPlannings, $newEventSerie);

                if (empty($errors)) {
                    return redirect()->to($route);
                }
            }
        }

        return $this->display_view('\Timbreuse\Views\eventSeries\update_form', $data);
    }
    
    /**
     * Update serie and return errors if any
     *
     * @param  int $id
     * @param  array $eventSerie
     * @return array
     */
    public function updateSerieAndGetErrors(int $id, array $eventSerie) : array {
        $eventSeriesModel = model(EventSeriesModel::class);

        $eventSeriesModel->update($id, $eventSerie);

        return $eventSeriesModel->errors();
    }
    
    /**
     * Update or Add event plannings linked to the updated event serie 
     *
     * @param  array $existingEventPlannings
     * @param  array $modifiedEventSeries
     * 
     * @return array
     */
    public function updateLinkedEventPlannings(array $existingEventPlannings, array $modifiedEventSeries) : array {  
        // Todo: implement and test this method
        $eventPlanningModel = model(EventPlanningsModel::class);

        // Update event plannings
        $planningErrors = [];
        $eventPlanningCopy = [];
        $interval = $this->getInterval($modifiedEventSeries['recurrence_interval'], $modifiedEventSeries['recurrence_frequency']);
        $serieStartDate = new DateTime($modifiedEventSeries['start_date']);
        $serieEndDate = new DateTime($modifiedEventSeries['end_date']);

        if (!empty($existingEventPlannings)) {
            $eventPlanningCopy = $existingEventPlannings[0];
        }

        foreach ($existingEventPlannings as $eventPlanning) {
            $eventDate = new DateTime($eventPlanning['event_date']);
    
            if ($eventDate < $serieStartDate || $eventDate > $serieEndDate) {
                // Not in the serie's range
                $eventPlanningModel->delete($eventPlanning['id'], true);
            } else {
                $dayOfWeek = strtolower($eventDate->format('l'));

                if (!in_array($dayOfWeek, $modifiedEventSeries['days_of_week'])) {
                    // Day does not match
                    $eventPlanningModel->delete($eventPlanning['id'], true);
                } else {
                    // Event already exists
                    // Possible improvements => update all events with newer serie's data
                }
            }
        }

        $planningErrors += $this->createEventIfNotExists(
            $modifiedEventSeries['id'],
            $serieStartDate,
            $serieEndDate,
            $interval,
            $modifiedEventSeries['days_of_week'],
            $eventPlanningCopy
        );
    
        return $planningErrors;
    }
    
        
    /**
     * Create newer event plannings with the corresponding data
     * Only create if does not already exists
     *
     * @param  int $id
     * @param  DateTime $startDate
     * @param  DateTime $endDate
     * @param  DateInterval $interval
     * @param  array $daysOfWeek
     * @param  array $eventPlanning
     * 
     * @return array
     */
    public function createEventIfNotExists(
        int $id,
        DateTime $startDate,
        DateTime $endDate,
        DateInterval $interval,
        array $daysOfWeek,
        array $eventPlanning) : array
    {
        $eventPlanningModel = model(EventPlanningsModel::class);
        $planningErrors = [];
        $eventPlanningUpdate = [];
        $currentDate = clone $startDate;

        while ($currentDate <= $endDate) {
            foreach($daysOfWeek as $dayOfWeek) {
                $nextOccurrence = $this->getNextOccurrence($currentDate, $endDate, $dayOfWeek);
    
                if (!is_null($nextOccurrence)) {
                    $existingEventPlanning = $eventPlanningModel->getByDate($id, $nextOccurrence->format('Y-m-d'));

                    $eventPlanningUpdate = [
                        'fk_event_series_id' => $id,
                        'fk_user_group_id' => $eventPlanning['fk_user_group_id'],
                        'fk_user_sync_id' => $eventPlanning['fk_user_sync_id'],
                        'fk_event_type_id' => $eventPlanning['fk_event_type_id'],
                        'event_date' => $nextOccurrence->format('Y-m-d'),
                        'start_time' => $eventPlanning['start_time'],
                        'end_time' => $eventPlanning['end_time'],
                        'is_work_time' => (bool)$eventPlanning['is_work_time'],
                    ];

                    if (!empty($existingEventPlanning)) {
                        // Event already exists
                        // Possible improvements => update event with newer serie's data
                    } else {
                        unset($eventPlanningUpdate['id']);
                        $eventPlanningModel->insert($eventPlanningUpdate);
                        $planningErrors += $eventPlanningModel->errors();
                    }
                }
            }

            $currentDate->add($interval);
        }

        return $planningErrors;
    }

    /**
     * Display the delete form and delete the corresponding event planning
     *
     * @param  int $id
     * @param  int $action
     * 
     * @return string|RedirectResponse
     */
    public function delete(int $id, int $action = 0) : string|RedirectResponse {
        $eventSeriesModel = model(EventSeriesModel::class);
        $eventPlanningModel = model(EventPlanningsModel::class);

        $eventSerie = $eventSeriesModel->findAllSeries($id);

        $isAdminView = url_is('*admin*');
        $route = ($isAdminView ? 'admin/': '') . 'event-plannings';

        if (is_null($eventSerie) || $this->checkPermissionBeforeDelete($eventSerie)) {
            return redirect()->to($route);
        }

        $of_group_or_user = '';

        if (!is_null($eventSerie['user_group_name'])) {
            $of_group_or_user .= lang('tim_lang.of_group');
        } else {
            $of_group_or_user .= lang('tim_lang.of_user');
        }

        $data = [
            'title' => lang('tim_lang.delete_event_serie'),
            'eventSerie' => $eventSerie,
            'titleParameters' => [
                'event_type_name' => $eventSerie['event_type_name'],
                'of_group_or_user' => $of_group_or_user,
                'group_or_user' => $eventSerie['user_group_name'] ?? 
                    "{$eventSerie['user_firstname']} {$eventSerie['user_lastname']}",
            ],
            'route' => $route
        ];

        switch ($action) {
            case 0:
                return $this->display_view('Timbreuse\Views\eventSeries\confirm_delete', $data);

            case 1:
                // In case soft delete is implemented
                break;
            
            case 2:
                if (isset($_POST) && !empty($_POST) && !is_null($_POST['confirmation'])) {
                    $eventPlanningModel->where('fk_event_series_id', $id)->delete(null, true);
                    $eventSeriesModel->delete($id, true);
                }
                break;
        }

        return redirect()->to($route);
    }
    
    /**
     * Check user permission before accessing the delete page
     *
     * @param  mixed $eventSerie
     * @return bool
     */
    private function checkPermissionBeforeDelete(array $eventSerie) : bool {
        $userAccess = $_SESSION['user_access'];
        $userConfig = config('\User\Config\UserConfig');

        if (!is_null($eventSerie['fk_user_group_id']) && $userAccess >= $userConfig->access_lvl_admin) {
            return false;
        } else if (!is_null($eventSerie['fk_user_sync_id']) && $userAccess >= $userConfig->access_lvl_registered) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Get the next date when an event will happen
     *
     * @param  DateTime $startDate
     * @param  DateTime $endDate
     * @param  string $dayOfWeek
     * 
     * @return DateTime
     */
    private function getNextOccurrence(DateTime $startDate, DateTime $endDate, string $dayOfWeek) : ?DateTime {
        $currentDate = clone $startDate;

        while (true) {
            $currentDayOfWeek = strtolower($currentDate->format('l'));

            if ($currentDayOfWeek === $dayOfWeek && $currentDate <= $endDate) {
                // Found the next occurrence
                return clone $currentDate;
            }
    
            // Move to the next day
            $currentDate->add(new DateInterval('P1D'));
    
            if ($currentDate > $endDate) {
                break;
            }
        }
    
        return null;
    }
    
    /**
     * Get interval from the corresponding data
     *
     * @param  string $recurrenceInterval
     * @param  string $recurrenceFrequency
     * 
     * @return DateInterval
     */
    private function getInterval(string $recurrenceInterval, string $recurrenceFrequency) : DateInterval {
        return new DateInterval(
            'P'
            . $recurrenceInterval
            . strtoupper(substr($recurrenceFrequency, 0, 1))
        );
    }
}
