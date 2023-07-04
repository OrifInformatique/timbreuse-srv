<?php


namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\LogsModel;
use CodeIgniter\API\ResponseTrait;
use Timbreuse\Models\BadgesModel;
use CodeIgniter\HTTP\Response;

use CodeIgniter\I18n\Time;

class LogsAPI extends BaseController
{
    use ResponseTrait; # API Response Trait

    public function put(string $date, int $badgeId, string $inside,
        string $token): Response
    {
        $logModel = model(LogsModel::class);
        $badgeModel = model(badgesModel::class);
        $data = array();
        $data['date'] = $date;
        $data['id_badge'] = $badgeId;
        $data['inside'] = $inside;
        helper('UtilityFunctions');
        if ($token !== create_token($date, $badgeId, $inside)) {
            return $this->failUnauthorized();
        }
        $userId = $badgeModel->select('id_user')->find($badgeId);
        $data += $userId;
        $data['date_badge'] = $date;
        if ($logModel->is_replicate($date, $badgeId, $inside) or 
            $logModel->insert($data))
        {
            return $this->respondCreated();
        }
        return $this->failServerError('database error');
    }

    /**
     * this is like of http method get for API
     *  maybe rename this in get
     */
    public function get(string $startDate, string $token): string|Response
    {
        helper('UtilityFunctions');
        if ($token != create_token($startDate)) {
            return $this->failUnauthorized();
        }
        $model = model(LogsModel::class);
        $model->select(
            'date, id_badge, inside, id_log, id_user, date_badge, date_modif, '
            .'date_delete'
        );
        $model->where('date_modif >=', $startDate);
        $model->orderBy('date_modif');
        $model->withDeleted();
        return $this->respond(json_encode($model->findAll()));
    }



}
