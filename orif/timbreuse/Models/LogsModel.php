<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class LogsModel extends Model {
    protected $table = 'log';

    public function get_logs($idBadge=null) {
        if ($idBadge === null) {
            return $this->findAll();
        } else {
            return $this->where('id_badge', $idBadge)
                        ->findAll();
        }
    }
}