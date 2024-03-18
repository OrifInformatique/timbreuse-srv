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

        $interval = new DateInterval(
            'P' 
            . $eventSerie['recurrence_interval'] 
            . strtoupper(substr($eventSerie['recurrence_frequency'], 0, 1))
        );

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

    public function update(int $id) {
        $eventSeriesModel = model(EventSeriesModel::class);

        $eventSerie = $eventSeriesModel->find($id);

        $route = $this->personalEventPlanningController->getPreviousRoute(url_is('*admin*'));

        if (is_null($eventSerie)) {
            return redirect()->to(base_url($route));
        }

        // Todo: replace the title on the update page

        $data = [
            'daysOfWeek' => $this->getDaysOfWeek(),
            'recurrenceFrequencies' => $eventSeriesModel->getReccurrenceFrequencyEnumValues(),
            'eventSerie' => $eventSerie,
            'route' => $route
        ];

        if (isset($_POST) && !empty($_POST)) {
            $errors = $this->updateSerieAndGetErrors($id, $_POST);

            if (empty($errors)) {
                $newEventSerie = $eventSeriesModel->find($id);
                dd($eventSerie, $newEventSerie);
                $this->updateEventSeriesAndPlannings($eventSerie, $newEventSerie);
            }
        }

        return $this->display_view('\Timbreuse\Views\eventSeries\update_form', $data);
    }

    public function updateSerieAndGetErrors(int $id, array $eventSerie) {
        $eventSeriesModel = model(EventSeriesModel::class);

        $eventSeriesModel->update($id, $eventSerie);

        return $eventSeriesModel->errors();
    }

    public function updateEventSeriesAndPlannings(array $existingEventSeries, array $modifiedEventSeries) {  
        // Todo: implement and test this method
        // Update event plannings
        $planningErrors = [];
    
        foreach ($existingEventSeries['plannings'] as $eventPlanning) {
            $eventDate = new DateTime($eventPlanning['event_date']);
    
            if ($eventDate < $modifiedEventSeries['start_date'] || $eventDate > $modifiedEventSeries['end_date']) {
                // Deletion logic goes here
            } else {
                $dayOfWeek = strtolower($eventDate->format('l'));
                if (!in_array($dayOfWeek, $modifiedEventSeries['days_of_week'])) {
                    // Deletion logic goes here
                } else {
                    // Update logic goes here
                }
            }
        }
    
        // Creation logic goes here
    
        return $planningErrors;
    }

    /**
     * Display the delete form and delete the corresponding event planning
     *
     * @param  int $id
     * @param  int $action
     * @return string|RedirectResponse
     */
    public function delete(int $id, int $action = 0) : string|RedirectResponse {
        $eventSeriesModel = model(EventSeriesModel::class);
        $eventPlanningModel = model(EventPlanningsModel::class);
        $eventSerie = $eventSeriesModel->findAllSeries($id);
        $route = 'admin/event-series';

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

        return redirect()->to(base_url($route));
    }
    
    /**
     * Get the next date when an event will happen
     *
     * @param  DateTime $startDate
     * @param  DateTime $endDate
     * @param  string $dayOfWeek
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
}
