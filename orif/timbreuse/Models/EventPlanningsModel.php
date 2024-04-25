<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class EventPlanningsModel extends Model
{
    protected $table            = 'event_planning';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'fk_event_series_id',
        'fk_user_group_id',
        'fk_user_sync_id',
        'fk_event_type_id',
        'event_date',
        'start_time',
        'end_time',
        'is_work_time'
    ];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        $this->validationRules = [
            'id' =>
            [
                'rules' => 'permit_empty|numeric'
            ],
            'fk_event_series_id' =>
            [
                'label' => lang('tim_lang.field_user_group_id'),
                'rules' => 'permit_empty|numeric'
            ],
            'fk_user_group_id' =>
            [
                'label' => lang('tim_lang.field_user_group_id'),
                'rules' => 'required_without[fk_user_sync_id]|permit_empty|numeric'
            ],
            'fk_user_sync_id' =>
            [
                'label' => lang('tim_lang.field_user_sync_id'),
                'rules' => 'required_without[fk_user_group_id]|permit_empty|numeric'
            ],
            'fk_event_type_id' =>
            [
                'label' => lang('tim_lang.field_event_type_id'),
                'rules' => 'required|numeric'
            ],
            'event_date' =>
            [
                'label' => lang('tim_lang.field_event_date'),
                'rules' => 'required|valid_date'
            ],
            'start_time' =>
            [
                'label' => lang('tim_lang.field_event_start_time'),
                'rules' => 'required|valid_date'
            ],
            'end_time' =>
            [
                'label' => lang('tim_lang.field_event_end_time'),
                'rules' => 'required|valid_date'
            ],
            'is_work_time' =>
            [
                'label' => lang('tim_lang.field_is_work_time'),
                'rules' => 'is_bool'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }
    
    /**
     * Get all offered time for the corresponding day and user
     *
     * @param  int $timUserId
     * @param  string $date
     * @return array
     */
    public function getOfferedTimeForDay(int $timUserId, string $date) : ?array {
        $groupEvents = [];

        $userGroupModel = model(UserGroupsModel::class);

        $linkedUserGroupIds = $userGroupModel->getAllLinkedUserGroupIds($timUserId);

        // Fetch events directly linked to the user
        $userEvents = $this->getEventsByFilters($date, $timUserId);

        // Fetch events linked to groups the user is associated with
        foreach ($linkedUserGroupIds as $linkedUserGroupId) {
            array_push($groupEvents, ...$this->getEventsByFilters($date, null, $linkedUserGroupId));
        }

        // Combine both user and group events
        $planningTime = [];
        foreach ($userEvents as $event) {
            $duration = strtotime($event['end_time']) - strtotime($event['start_time']);
            $planningTime[] = $duration;
        }

        foreach ($groupEvents as $event) {
            $duration = strtotime($event['end_time']) - strtotime($event['start_time']);
            $planningTime[] = $duration;
        }

        return $planningTime;
    }
    
    /**
     * Get personal or group events corresponding to parameters
     *
     * @param  mixed $date
     * @param  mixed $timUserId
     * @param  mixed $groupId
     * @return array
     */
    private function getEventsByFilters(string $date, ?int $timUserId = null, ?int $groupId = null) : array {
        $baseSelect = 'event_planning.start_time, event_planning.end_time';

        $events = $this->select($baseSelect, true);

        if (is_null($timUserId)) {
            $events = $events->where('fk_user_group_id', $groupId);
        } else {
            $events = $events->where('fk_user_sync_id', $timUserId);       
        }

        return $events
            ->where('event_date', $date)
            ->where('is_work_time', true)
            ->findAll();
    }
        
    /**
     * Get event planning with linked data
     *
     * @param  int $id
     * @return ?array
     */
    public function getWithLinkedData(int $id) : ?array {
        return $this
            ->select('
                event_planning.id,
                fk_user_sync_id,
                fk_event_series_id,
                event_type.name AS event_type_name, 
                user_sync.name AS user_firstname, 
                user_sync.surname AS user_lastname, 
                user_group.name AS user_group_name')
            ->join('event_type', 'event_type.id = fk_event_type_id', 'left')
            ->join('user_sync', 'user_sync.id_user = fk_user_sync_id', 'left')
            ->join('user_group', 'user_group.id = fk_user_group_id', 'left')
            ->find($id);
    }

    /**
     * Get all event plannings linked to a serie
     *
     * @param  int $eventSerieId
     * @return array
     */
    public function getAllBySerieId(int $eventSerieId) : array {
        return $this
            ->where('fk_event_series_id', $eventSerieId)
            ->findAll();
    }
    
    /**
     * Get a single event planning by serie's id and a date
     *
     * @param  int $eventSerieId
     * @param  string $date
     * @return ?array
     */
    public function getByDate(int $eventSerieId, string $date) : ?array {
        return $this
            ->where('fk_event_series_id', $eventSerieId)
            ->where('event_date', $date)
            ->find();
    }
}
