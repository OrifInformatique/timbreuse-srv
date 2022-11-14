<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'user_sync';
    protected $primaryKey ='id_user';
    protected $allowedFields = ['name', 'surname'];

    public function get_users($userId=null) {
        if ($userId === null) {
            return $this->findAll();
        } else {
            return $this->find($userId);
        }
    }

    public function is_replicate(string $name, string $surname): bool
    {
        $this->where('name =', $name);
        $this->where('surname =', $surname);
        return boolval($this->findAll());
    }

}