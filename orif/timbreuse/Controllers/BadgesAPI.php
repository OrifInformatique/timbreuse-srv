<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Timbreuse\Models\BadgesModel;

class BadgesAPI extends BaseController
{
    use ResponseTrait; # API Response Trait
    /**
     * api
     */
    protected function is_available($badgeId): bool
    {
        echo 'is_available';
        $model = model(BadgesModel::class);
        $data = $model->find($badgeId);
        var_dump($data);
        var_dump(!boolval($data) or is_null($data['id_user']));
        return !boolval($data) or is_null($data['id_user']);
    }

    public function put($badgeId, $name, $surname, $token) {
        $model = model(BadgesModel::class);
        # when is not a test ; 
        # $token == $this->create_token($badgeId, $name, $surname)
        helper('UtilityFunctions');
        if ($token == create_token($badgeId, $name, $surname)) {
            if (($this->is_available($badgeId)) and ($model->
            add_badge_and_user($badgeId, $name, $surname))) {
                return $this->respondCreated();
            } else {
                return $this->failServerError('database error');
            }
        } else {
            return $this->failUnauthorized();
        }
    }

    /**
     * @deprecated
     */
    private function create_token($badgeId, $name, $surname)
    {
        trigger_error('Deprecated function called.', E_USER_DEPRECATED);
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
    public function get($startDate, string $token)
    {
        helper('UtilityFunctions');
        if ($token != create_token($startDate)) {
            return $this->failUnauthorized();
        }
        $model = model(BadgesModel::class);
        $model->select(
            'id_badge, id_user, rowid_badge, date_modif, date_delete');
        # $model->where('rowid_badge >', $startBadgeRowID);
        # $model->orderBy('rowid_badge');
        $model->where('date_modif >=', $startDate);
        $model->orderBy('date_modif');
        $model->withDeleted();
        return $this->respond(json_encode($model->findAll()));
    }

    /**
     * @deprecated
     */
    public function get_old($startUserId)
    {
        trigger_error('Deprecated function called.', E_USER_DEPRECATED);
        $model = model(BadgesModel::class);
        $data = $model->get_users_and_badges($startUserId);
        return $this->respond(json_encode($data));
    }

}
