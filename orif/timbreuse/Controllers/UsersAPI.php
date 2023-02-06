<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Timbreuse\Models\UsersModel;

class UsersAPI extends BaseController
{
    use ResponseTrait; # API Response Trait
    /**
     * api
     */
     public function put($name, $surname, $token) {
        $model = model(UsersModel::class);
        helper('UtilityFunctions');
        if ($token == create_token($name, $surname)) {
            if (($model->is_replicate($name, $surname)) or boolval($model->
            insert($name, $surname))) {
                return $this->respondCreated();
            } else {
                return $this->failServerError('database error');
            }
        } else {
            return $this->failUnauthorized();
        }
    }

    public function get($startDate, string $token)
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

    # private function create_token($name, $surname)
    # {
    #     $text = $name.$surname;
    #     helper('UtilityFunctions');
    #     $key = load_key();
    #     $token_text = hash_hmac('sha256', $text, $key);
    #     return $token_text;
    # }
     
}
