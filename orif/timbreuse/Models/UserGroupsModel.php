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
    protected $allowedFields    = ['fk_parent_user_group_id', 'name'];

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
            'fk_parent_user_group_id' =>
            [
                'label' => lang('tim_lang.fieldUserGroupId'),
                'rules' => 'permit_empty|numeric|differs[id]'
            ],
            'name' =>
            [
                'label' => lang('tim_lang.fieldUserGroupName'),
                'rules' => 'required|trim|'
                . 'min_length['.config('\Timbreuse\Config\TimbreuseConfig')->userGroupNameMinLength.']|'
                . 'max_length['.config('\Timbreuse\Config\TimbreuseConfig')->userGroupNameMaxLength.']'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }

    public function getUserGroups(?int $id = null): array {
        return $this->select('child.id, child.name AS userGroupName, parent.name AS parentUserGroupName')
            ->distinct()
            ->from('user_group child')
            ->join('user_group parent', 'parent.id = child.fk_parent_user_group_id', 'left')
            ->find($id);
    }
}
