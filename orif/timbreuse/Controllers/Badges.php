<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Timbreuse\Models\BadgesModel;

class Badges extends BaseController
{
    use ResponseTrait; # API Response Trait
    /**
     * api
     */
    public function add($badgeId, $name, $surname, $token) {
        $model = model(BadgesModel::class);
        # when is not a test ; 
        # $token == $this->create_token($badgeId, $name, $surname)
        if ($token == $this->create_token($badgeId, $name, $surname)) {
            if ((boolval($model->find($badgeId))) or ($model->
            add_badge_and_user($badgeId, $name, $surname))) {
                return $this->respondCreated();
            } else {
                return $this->failServerError('database error');
            }
        } else {
            return $this->failUnauthorized();
        }
    }

    private function create_token($badgeId, $name, $surname)
    {
        $text = $badgeId.$name.$surname;
        helper('UtilityFunctions');
        $key = load_key();
        $token_text = hash_hmac('sha256', $text, $key);
        return $token_text;
    }

    public function test1() {
        #helper('Timbreuse\Helpers\UtilityFunctions');
        helper('UtilityFunctions');
        var_dump(testhelper());
    }

    
    /**
     * get data badges
     */
    public function get($startBadgeRowID)
    {
        $model = model(BadgesModel::class);
        $model->where('rowid_badge >', $startBadgeRowID);
        $model->orderBy('rowid_badge');
        return $this->respond(json_encode($model->findAll()));
    }

    public function get2($startUserId)
    {
        $model = model(BadgesModel::class);
        $data = $model->get_users_and_badges($startUserId);
        return $this->respond(json_encode($data));
    }

}