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
                'label' => lang('tim_lang.field_user_group_id'),
                'rules' => 'permit_empty|numeric|differs[id]'
            ],
            'name' =>
            [
                'label' => lang('tim_lang.field_user_group_name'),
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
    
    /**
     * Get all user groups' ids linked to a user
     *
     * @param  mixed $timUserId
     * @return array
     */
    private function getAllByTimUserId(int $timUserId): array {
        $result = $this
            ->join('user_sync_group', 'user_sync_group.fk_user_group_id = user_group.id', 'inner')
            ->where('fk_user_sync_id', $timUserId)
            ->findColumn('fk_user_group_id');
        
        if (!empty($result)) {
            // Return an array containing the user's groups ids
            return $result;
        } else {
            // Return an empty array
            return [];
        }
    }
    
    /**
     * Recursively get parent group's ids
     *
     * @param  mixed $id
     * @return array
     */
    private function getParentGroupIdsRecusively(?int $id = null): array {
        $ids = [];

        $userGroup = $this->find($id ?? 0);

        if (!is_null($userGroup) && !is_null($userGroup['fk_parent_user_group_id'])) {
            $parentUserGroupId = $userGroup['fk_parent_user_group_id'];
            $parentUserGroupIds = $this->getParentGroupIdsRecusively($parentUserGroupId);

            $ids[] = $parentUserGroupId;
            $ids = array_merge($ids, $parentUserGroupIds);
        }

        return $ids;
    }
    
    /**
     * Get all user groups' ids linked to a user, including parent groups' ids
     *
     * @param  mixed $timUserId
     * @return array
     */
    public function getAllLinkedUserGroupIds(int $timUserId) : array {
        $linkedUserGroupsIds = $this->getAllByTimUserId($timUserId);
        $allLinkedUserGroups = $linkedUserGroupsIds;

        foreach($linkedUserGroupsIds as $userGroupId) {
            $parentGroupIds = $this->getParentGroupIdsRecusively($userGroupId);

            $allLinkedUserGroups = array_unique(array_merge($allLinkedUserGroups, $parentGroupIds));
        }

        return $allLinkedUserGroups;
    }
}
