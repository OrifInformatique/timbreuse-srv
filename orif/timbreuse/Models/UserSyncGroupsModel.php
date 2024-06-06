<?php

namespace Timbreuse\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

use CodeIgniter\Model;

class UserSyncGroupsModel extends Model
{
    protected $table            = 'user_sync_group';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['fk_user_group_id', 'fk_user_sync_id'];

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
            'fk_user_group_id' =>
            [
                'label' => lang('tim_lang.field_user_group_id'),
                'rules' => 'required|numeric'
            ],
            'fk_user_sync_id' =>
            [
                'label' => lang('tim_lang.field_user_sync_id'),
                'rules' => 'required|numeric'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }
}
