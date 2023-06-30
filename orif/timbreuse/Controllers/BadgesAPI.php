<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use Timbreuse\Models\BadgesModel;
use CodeIgniter\HTTP\Response;

class BadgesAPI extends BaseController
{
    use ResponseTrait; # API Response Trait

    protected function is_available(int $badgeId): bool
    {
        $model = model(BadgesModel::class);
        $data = $model->find($badgeId);
        return !boolval($data) or is_null($data['id_user']);
    }

    public function put(int $badgeId, string $name, string $surname,
        string $token): Response
    {
        $model = model(BadgesModel::class);
        helper('UtilityFunctions');
        if ($token !== create_token($badgeId, $name, $surname)) {
            return $this->failUnauthorized();
        }
        if (($this->is_available($badgeId)) and ($model->
            add_badge_and_user($badgeId, $name, $surname)))
        {
            return $this->respondCreated();
        }
        return $this->failServerError('database error');
    }     
    
    /**
     * get data badges
     */
    public function get(string $startDate, string $token): string|Response
    {
        helper('UtilityFunctions');
        if ($token !== create_token($startDate)) {
            return $this->failUnauthorized();
        }
        $model = model(BadgesModel::class);
        $model->select(
            'id_badge, id_user, rowid_badge, date_modif, date_delete');
        $model->where('date_modif >=', $startDate);
        $model->orderBy('date_modif');
        $model->withDeleted();
        return $this->respond(json_encode($model->findAll()));
    }

}
