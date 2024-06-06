<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class UserPlanningsModel extends Model
{
    protected $table = 'user_planning';
    protected $primaryKey ='id_user_planning';
    protected $allowedFields = ['id_user', 'id_planning', 'date_begin', 'date_end', 'title'];

    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;

    protected $useTimestamps = false;
}
