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

    public function get($startUserId, string $token) {
        helper('UtilityFunctions');
        if ($token == create_token($startUserId)) {
            $model = model(UsersModel::class);
            $model->where('id_user >', $startUserId);
            $model->orderBy('id_user');
            return $this->respond(json_encode($model->findAll()));
        } else {
            return $this->failUnauthorized();
        }
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