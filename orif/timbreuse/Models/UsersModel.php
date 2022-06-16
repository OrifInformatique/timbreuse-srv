<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'user';
    protected $primaryKey ='id_user';

    public function get_users($userId=null) {
        if ($userId === null) {
            return $this->findAll();
        } else {
            return $this->find($userId);
        }
    }
}