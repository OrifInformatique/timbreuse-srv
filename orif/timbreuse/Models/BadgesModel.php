<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class BadgesModel extends Model {
    protected $table = 'badge_sync';
    protected $primaryKey = 'id_badge';

    protected $useSoftDeletes = true;

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'date_modif';
    protected $deletedField  = 'date_delete';
    protected $dateFormat = 'datetime';

    public function get_badges($idUser=null) {
        if ($idUser === null) {
            return $this->findAll();
        } else {
            return $this->where('id_user', $idUser)
                        ->findColumn('id_badge');
        }
    }

    /** 
     *  call a stored procedure, add badge and user,
     * return true if success (not tested)
     */
    public function add_badge_and_user($badgeId, $name, $surname) {
        $sql = 'CALL insert_badge_and_user(?, ?, ?)';
        $result = $this->db->query($sql, array($badgeId, $name, $surname));
        return $result !== NULL;
    }

    public function get_users_and_badges($startUserId) {
        $sql = 'CALL `select_users_and_badges`(?);';
        return $this->db->query($sql, $startUserId)->getResultArray();
    }
    
}