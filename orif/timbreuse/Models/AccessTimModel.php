<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;

class AccessTimModel extends Model
{
    protected $table = 'access_tim_user';
    protected $primaryKey = 'id_access';

    public function get_access_users($userCiId)
    {
        try {

            return $this->select('id_user')
                ->where('id_ci_user', $userCiId)
                ->findall()[0]['id_user'];
        } catch (\Exception $e) {
            #    echo $e->getMessage();
            return null;
        }
    }
}
