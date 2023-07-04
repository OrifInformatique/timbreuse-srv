<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Timbreuse\Models\UsersModel;
use CodeIgniter\HTTP\Response;

class UsersAPI extends BaseController
{
    use ResponseTrait; # API Response Trait

    public function put(string $name, string $surname, string $token): Response
    {
        $model = model(UsersModel::class);
        helper('UtilityFunctions');
        if ($token !== create_token($name, $surname)) {
            return $this->failUnauthorized();
        }
        if (($model->is_replicate($name, $surname)) or boolval($model
                ->insert($name, $surname)))
        {
            return $this->respondCreated();
        }
        return $this->failServerError('database error');
    }

    public function get(string $startDate, string $token): Response|string
    {
        helper('UtilityFunctions');
        if ($token != create_token($startDate)) {
            return $this->failUnauthorized();
        }
        $model = model(UsersModel::class);
        $model->select('id_user, name, surname, date_modif, date_delete');
        $model->where('date_modif >=', $startDate);
        $model->orderBy('date_modif');
        $model->withDeleted();
        return $this->respond(json_encode($model->findAll()));
    }

}
