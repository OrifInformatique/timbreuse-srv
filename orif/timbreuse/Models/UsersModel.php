<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class UsersModel extends Model
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

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        $this->validationRules = [
            'name' => [
                'label' => lang('tim_lang.surname'),
                'rules' => 'required|trim'
            ],
            'surname' => [
                'label' => lang('tim_lang.surname'),
                'rules' => 'required|trim'
            ],
        ];

        $this->validationMessages = [];

        parent::__construct($db, $validation);
    }

    public function get_user($userId)
    {
        return $this->select('user_sync.id_user, surname, user_sync.name, date_delete, user.id, username, email, fk_user_type, archive, user_type.name AS user_type')
                    ->join('access_tim_user', 'user_sync.id_user = access_tim_user.id_user', 'left')
                    ->join('user', 'user.id = access_tim_user.id_ci_user', 'left')
                    ->join('user_type', 'user.fk_user_type = user_type.id', 'left')
                    ->orderBy('surname')
                    ->withDeleted(true)
                    ->find($userId);
    }

    public function get_users(bool $with_deleted = false)
    {
        return $this->select('user_sync.id_user, surname, user_sync.name, date_delete, user.id, username, email, fk_user_type, archive, user_type.name AS user_type')
                    ->join('access_tim_user', 'user_sync.id_user = access_tim_user.id_user', 'left')
                    ->join('user', 'user.id = access_tim_user.id_ci_user', 'left')
                    ->join('user_type', 'user.fk_user_type = user_type.id', 'left')
                    ->orderBy('surname')
                    ->withDeleted($with_deleted)
                    ->findAll();
    }

    public function is_replicate(string $name, string $surname): bool
    {
        $this->where('name =', $name);
        $this->where('surname =', $surname);
        return boolval($this->findAll());
    }

    public function get_names(int $userId): array
    {
        return $this->select('name, surname')->find($userId);
    }

    public function get_available_users_info(): array
    {
        $data = $this->select('user_sync.id_user, name, surname')
            ->join('badge_sync', 'user_sync.id_user = badge_sync.id_user',
                'left')
            ->where('id_badge', null)
            ->orderBy('surname')
            ->findall();
        return $data ?? array();
    }

}
