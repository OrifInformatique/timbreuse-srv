<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

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

    public function get_available_badges()
    {
        #$this->orderBy('id_badge');
        $this->where('id_user', null);
         return $this->findColumn('id_badge');
        
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

    public function set_user_id_to_null($timUserId)
    {
        $badgeIds = $this->get_badges($timUserId);
        $data['id_user'] = null;
        if (isset($badgeIds[0]) and is_array($badgeIds)) {
            foreach ($badgeIds as $badgeId) {
                $this->update($badgeId, $data);
            }
        } elseif (isset($badgeIds)) {
            $this->update($badgeIds, $data);
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

    public function get_user_info($badgeId)
    {
        return $this->select('user_sync.id_user, name, surname')
            ->join('user_sync', 'user_sync.id_user = badge_sync.id_user')
            ->find($badgeId);
    }

    public function get_available_users_info()
    {
        return $this->select('user_sync.id_user, name, surname')
            ->join('user_sync', 'user_sync.id_user = badge_sync.id_user',
                'right')
            ->where('id_badge', null)
            ->findall();
    }

}
