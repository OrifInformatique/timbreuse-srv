<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;

class AccessTimModel extends Model
{
    protected $table = 'access_tim_user';
    protected $primaryKey = 'id_access';
    protected $allowedFields = ['id_user', 'id_ci_user'];

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

    public function get_access_users_timb_to_ci($userId)
    {
        try {

            return $this->select('id_ci_user')
                ->where('id_user', $userId)
                ->findall()[0]['id_ci_user'];
        } catch (\Exception $e) {
            #    echo $e->getMessage();
            return null;
        }
    }

}
