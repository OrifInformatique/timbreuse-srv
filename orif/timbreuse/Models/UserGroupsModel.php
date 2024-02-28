<?php

namespace Timbreuse\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

use CodeIgniter\Model;

class UserGroupsModel extends Model
{
    protected $table            = 'user_group';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['fk_user_group_id', 'name'];

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        $this->validationRules = [
            'fk_user_group_id' =>
            [
                'label' => lang('tim_lang.fieldUserGroupId'),
                'rules' => 'permit_empty|numeric'
            ],
            'name' =>
            [
                'label' => lang('tim_lang.fieldUserGroupName'),
                'rules' => 'required'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }
}
