<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class BadgesModel extends Model 
{
    protected $table = 'badge_sync';
    protected $primaryKey = 'id_badge';

    protected $useSoftDeletes = true;

    protected $allowedFields = ['id_user'];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'date_modif';
    protected $deletedField  = 'date_delete';
    protected $dateFormat = 'datetime';

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        $this->validationRules = [
            'id_user' => [
                'label' => lang('tim_lang.userId'),
                'rules' => 'permit_empty|integer'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }

    public function get_badges($userId=null)
    {
        if ($userId === null) {
            # $this->orderBy('id_badge');
            return $this->findAll();
        } else {
            return $this->where('id_user', $userId)
                        ->findColumn('id_badge');
        }
    }

    public function get_available_badges(): array
    {
        #$this->orderBy('id_badge');
        $data = $this->where('id_user', null)
                ->findColumn('id_badge');
        return $data ?? array();
        
    }

    /** 
     *  call a stored procedure, add badge and user,
     * return true if success (not tested)
     */
    public function add_badge_and_user($badgeId, $name, $surname)
    {
        $sql = 'CALL insert_badge_and_user(?, ?, ?)';
        $result = $this->db->query($sql, array($badgeId, $name, $surname));
        return $result !== NULL;
    }

    public function get_users_and_badges($startUserId)
    {
        $sql = 'CALL `select_users_and_badges`(?);';
        return $this->db->query($sql, $startUserId)->getResultArray();
    }
    
    /**
     * Set each id_user from one user to null
     *
     * @param  int $timUserId
     * @return void
     */
    public function set_user_id_to_null(int $timUserId)
    {
        $badgeIds = $this->get_badges($timUserId);
        $data['id_user'] = null;

        if (isset($badgeIds) && count($badgeIds) > 0) {
            $this->withDeleted(true)->update($badgeIds, $data);
        }
    }

    public function is_set_badge($badgeId)
    {
        return !empty($this->find($badgeId));
    }

    public function deallocate_and_reallocate_badge($timUserId, $newBadge)
    {
        $badgeData['id_user'] = $timUserId;
        $this->transBegin();
        $this->set_user_id_to_null($timUserId);
        if (($newBadge !== '') and ($newBadge !== null)) {
            $this->update($newBadge, $badgeData);
        }
        if ($this->is_set_badge($newBadge)) {
            $this->db->transCommit();
            return true;
        } else {
            $this->db->transRollback();
            return false;
        }
    }


    public function get_badges_and_user_info()
    {
        return $this->select('id_badge, name, surname')
            ->join('user_sync', 'user_sync.id_user = badge_sync.id_user',
                'left')
            ->findAll();
    }

    public function get_user_id($badgeId)
    {
        return $this->select('id_user')
            ->find($badgeId);
    }

    public function get_user_info($badgeId)
    {
        return $this->select('user_sync.id_user, name, surname')
            ->join('user_sync', 'user_sync.id_user = badge_sync.id_user')
            ->find($badgeId);
    }

    /**
     * @deprecated 
     * it is moved to userModel
    */
    private function get_available_users_info(): array
    {
        $data = $this->select('user_sync.id_user, name, surname')
            ->join('user_sync', 'user_sync.id_user = badge_sync.id_user',
                'right')
            ->where('id_badge', null)
            ->orderBy('name')
            ->findall();
        return $data ?? array();
    }

    public function is_allocate_user(string $badgeId): bool
    {
        $sql = "SELECT coalesce((id_user=0) or (id_user or null), false) b " . 
                'FROM badge_sync WHERE id_badge=?';
        $result =  $this->db->query($sql, $badgeId)->getResultArray()[0]['b'];
        return boolval($result);
    }

}
