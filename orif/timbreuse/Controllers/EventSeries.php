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

class EventSeries extends BaseController
{

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
    }

    public function getDaysOfWeek() : array{
        return [
            'monday' => lang('tim_lang.monday'),
            'tuesday' => lang('tim_lang.tuesday'),
            'wednesday' => lang('tim_lang.wednesday'),
            'thursday' => lang('tim_lang.thursday'),
            'friday' => lang('tim_lang.friday')
        ];
    }

    public function getCreateSeriesHTML() : string {
        $data = [
            'daysOfWeek' => $this->getDaysOfWeek(),
            'eventSerie' => null,
            'recurrenceFrequencies' => $this->getEnumValues()
        ];

        return json_encode(view('\Timbreuse\Views\eventSeries\create_series.php', $data));
    }

    private function getEnumValues() : array|bool {
        $model = model(EventSeriesModel::class);

        $query = $model->query("SHOW COLUMNS FROM event_series WHERE Field = 'recurrence_frequency'");
        $row = $query->getRow();

        if ($row !== null && preg_match('/^enum\((.*)\)$/', $row->Type, $matches)) {
            $enumValues = array();
            foreach (explode(',', $matches[1]) as $value) {
                $enumValue = trim($value, "'");
                $enumValues[$enumValue] = lang("tim_lang.{$enumValue}");
            }
            return $enumValues;
        } else {
            return false;
        }
    }

    public function create(array $eventSerie, array $eventPlanning) {
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
    
            $this->createEventPlannings($newEventSerie);
        }

        return $errors;
    }

    private function createEventPlannings(array $eventSerie) {
        $planningErrors = [];

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
                    // todo: create planning
                    d($interval, $nextOccurrence);
                }
            }

            $currentDate->add($interval);
        }
        dd($eventSerie);

        return $planningErrors;
    }

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
