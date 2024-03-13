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
        if (isset($data['data']['days_of_week'])) {
            $data['data']['days_of_week'] = json_decode($data['data']['days_of_week']);
        }
        return $data;
    }
}
