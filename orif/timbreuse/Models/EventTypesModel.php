<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class EventTypesModel extends Model
{
    protected $table            = 'event_type';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'is_group_event_type', 'is_personal_event_type'];

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
            'name' =>
            [
                'label' => lang('tim_lang.fieldName'),
                'rules' => 'required|trim|'
                . 'min_length['.config('\Timbreuse\Config\TimbreuseConfig')->eventTypeNameMinLength.']|'
                . 'max_length['.config('\Timbreuse\Config\TimbreuseConfig')->eventTypeNameMaxLength.']'
            ],
            'is_group_event_type' =>
            [
                'label' => lang('tim_lang.fieldIsGroupEventType'),
                'rules' => 'required|is_bool'
            ],
            'is_personal_event_type' =>
            [
                'label' => lang('tim_lang.fieldIsPersonalEventType'),
                'rules' => 'required|is_bool'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }
}
