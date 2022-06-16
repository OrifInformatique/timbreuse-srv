<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class BadgesModel extends Model {
    protected $table = 'badge';

    public function get_badges($idUser=null) {
        if ($idUser === null) {
            return $this->findAll();
        } else {
            return $this->where('id_user', $idUser)
                        ->findColumn('id_badge');
        }
    }
    
}