<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;

class AccessTimModel extends Model
{
    protected $table = 'access_tim_user';
    protected $primaryKey = 'id_access';
    protected $allowedFields = ['id_user', 'id_ci_user'];

    public function get_access_users($ciUserId)
    {
        try {

            return $this->select('id_user')
                ->where('id_ci_user', $ciUserId)
                ->findall();
        } catch (\Exception $e) {
            #    echo $e->getMessage();
            return null;
        }
    }

    /**
     * @deprecated
     */
    public function get_access_users_timb_to_ci($userId)
    {
        trigger_error('Deprecated function called.', E_USER_DEPRECATED);
        try {

            return $this->select('id_ci_user')
                ->where('id_user', $userId)
                ->findall()[0]['id_ci_user'];
        } catch (\Exception $e) {
            #    echo $e->getMessage();
            return null;
        }
    }

    public function get_access_users_with_info($ciUserId)
    {
        return $this->select('access_tim_user.id_user, name, surname')
        ->from('user')
        ->where('id_ci_user=', $ciUserId)
            ->where('user.id_user=access_tim_user.id_user')
            ->findall();
    }

    public function is_access($ciIdUser, $userId){
        $access = $this->select('id_user')
        ->where('id_ci_user', $ciIdUser)
            ->where('id_user', $userId)
            ->findall();
        return !empty($access);
    }





}
