<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class UserModel extends Model {
    protected $table = 'user';

    public function getUsers() {
        return $this->findAll();
    }
}