<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;

class AccessTimModel extends Model
{
    protected $table = 'access_tim_user';
    protected $primaryKey = 'id_access';
    protected $allowedFields = ['id_user', 'id_ci_user'];

    public function get_access_users(int $ciUserId)
    {
        return $this->select('id_user')
            ->where('id_ci_user', $ciUserId)->findall();
    }

    public function get_access_users_with_info(int $ciUserId)
    {
        return $this->select('access_tim_user.id_user, name, surname')
        ->join('user_sync', 'user_sync.id_user = access_tim_user.id_user')
        ->where('id_ci_user =', $ciUserId)
            ->findall();
    }

    #old
    public function __get_access_users_with_info(int $ciUserId)
    {
        return $this->select('access_tim_user.id_user, name, surname')
        ->from('user')
        ->where('id_ci_user=', $ciUserId)
            ->where('user.id_user=access_tim_user.id_user')
            ->findall();
    }

    public function is_access(int $ciIdUser, int $userId)
    {
        $access = $this->select('id_user')
        ->where('id_ci_user', $ciIdUser)
            ->where('id_user', $userId)
            ->findall();
        return !empty($access);
    }

    public function have_one_access(int $ciIdUser): bool
    {
        $access = $this->select('id_user')->where('id_ci_user', $ciIdUser)
            ->findall();
        return count($access) === 1;

    }

    public function get_tim_user(int $ciUserId): int
    {
        return $this->select('id_user')
            ->where('id_ci_user', $ciUserId)->first()['id_user'];
    }





}
