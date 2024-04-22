<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class EventSeriesModel extends Model
{
    protected $table            = 'event_series';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['start_date', 'end_date', 'recurrence_frequency', 'recurrence_interval', 'days_of_week'];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['encodeDays'];
    protected $beforeUpdate = ['encodeDays'];
    protected $afterFind = ['decodeDays'];

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        $this->validationRules = [
            'id' =>
            [
                'rules' => 'permit_empty|numeric'
            ],
            'start_date' =>
            [
                'label' => lang('tim_lang.field_start_date'),
                'rules' => 'required|valid_date'
            ],
            'end_date' =>
            [
                'label' => lang('tim_lang.field_end_date'),
                'rules' => 'required|valid_date'
            ],
            'recurrence_frequency' =>
            [
                'label' => lang('tim_lang.field_recurrence_frequency'),
                'rules' => 'required'
            ],
            'recurrence_interval' =>
            [
                'label' => lang('tim_lang.field_recurrence_interval'),
                'rules' => 'required|numeric'
            ],
            'days_of_week' =>
            [
                'label' => lang('tim_lang.field_days_of_week'),
                'rules' => 'required|cb_valid_array|cb_array_not_empty'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }

    protected function encodeDays(array $data) {
        if (isset($data['data']['days_of_week'])) {
            $data['data']['days_of_week'] = json_encode($data['data']['days_of_week']);
        }
        return $data;
    }

    protected function decodeDays(array $data) {
        if (isset($data) && !empty($data['data'])) {
            if (is_array($data['data']) && array_key_exists('days_of_week', $data['data'])) {
                $data['data']['days_of_week'] = json_decode($data['data']['days_of_week']);
            } else if (is_array($data['data'])) {
                foreach ($data['data'] as &$row) {
                    if (is_array($row) && array_key_exists('days_of_week', $row)) {
                        $row['days_of_week'] = json_decode($row['days_of_week']);
                    }
                }
            }
        }
        return $data;
    }    
    
    /**
     * Find all series and concatenate linked events data 
     *
     * @param  mixed $eventSeriesId
     * @return array|null
     */
    public function findAllSeries(?int $eventSeriesId = null) : array|null {
        return $this
            ->select('
                event_series.id,
                start_date,
                end_date,
                recurrence_frequency,
                recurrence_interval,
                days_of_week,
                GROUP_CONCAT(DISTINCT event_planning.fk_user_group_id) AS fk_user_group_id,
                GROUP_CONCAT(DISTINCT event_planning.fk_user_sync_id) AS fk_user_sync_id,
                GROUP_CONCAT(DISTINCT event_type.name) AS event_type_name,
                GROUP_CONCAT(DISTINCT user_group.name) AS user_group_name,
                GROUP_CONCAT(DISTINCT user_sync.name) AS user_lastname,
                GROUP_CONCAT(DISTINCT user_sync.surname) AS user_firstname'    
            )
            ->join('event_planning', 'fk_event_series_id = event_series.id', 'left')
            ->join('event_type', 'event_type.id = fk_event_type_id', 'left')
            ->join('user_sync', 'user_sync.id_user = fk_user_sync_id', 'left')
            ->join('user_group', 'user_group.id = fk_user_group_id', 'left')
            ->groupBy('event_series.id')
            ->find($eventSeriesId);
    }

    /**
     * Get values of enum field from the DB
     *
     * @return array|bool
     */
    public function getReccurrenceFrequencyEnumValues() : bool|array {
        $query = $this->query("SHOW COLUMNS FROM event_series WHERE Field = 'recurrence_frequency'");
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

    public function findWitPlanningData(int $id) : array {
        return $this->join('event_planning', 'fk_event_series_id = event_series.id', 'left')
            ->find($id);
    }
}
