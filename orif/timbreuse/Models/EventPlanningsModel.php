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
}
