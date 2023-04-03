<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class UserModel extends Model 
{
    protected $table = 'user_sync';
    protected $primaryKey ='id_user';
    protected $allowedFields = ['name', 'surname', 'date_delete'];

    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'date_modif';
    protected $deletedField  = 'date_delete';
    protected $dateFormat = 'datetime';

    public function get_users($userId=null)
    {
        if ($userId === null) {
            $this->orderBy('surname');
            return $this->findAll();
        } 
        return $this->find($userId);
    }

    public function is_replicate(string $name, string $surname): bool
    {
        $this->where('name =', $name);
        $this->where('surname =', $surname);
        return boolval($this->findAll());
    }

    public function get_names($userId)
    {
        return $this->select('name, surname')->find($userId);
    }

}
