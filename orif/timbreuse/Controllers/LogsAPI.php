<?php


namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\LogsModel;
use CodeIgniter\API\ResponseTrait;
use Timbreuse\Models\BadgesModel;

use CodeIgniter\I18n\Time;

class LogsAPI extends BaseController
{
    use ResponseTrait;

    public function put($date, $badgeId, $inside, $token)
    {
        $logModel = model(LogsModel::class);
        $badgeModel = model(badgesModel::class);
        $data = array();
        $data['date'] = $date;
        $data['id_badge'] = $badgeId;
        $data['inside'] = $inside;
        helper('UtilityFunctions');
        if ($token == create_token($date, $badgeId, $inside)) {
            # direct reponse created if already in the db withtout insert 
            # again
            $userId = $badgeModel->select('id_user')->find($badgeId);
            $data += $userId;
            $data['date_badge'] = $date;
            if ($logModel->is_replicate($date, $badgeId, $inside) or 
            ($logModel->insert($data))) {
                return $this->respondCreated();
            } else {
                return $this->failServerError('database error');
            }
        } else {
            # return $this->failUnauthorized();
        }
    }

    /**
     * @deprecated
     */
    private function create_token($date, $badgeId, $inside)
    {
        trigger_error('Deprecated function called.', E_USER_DEPRECATED);
        $text = $date.$badgeId.$inside;
        # $key = 'a'; # to put a truth in a file not in git
        helper('UtilityFunctions');
        $key = load_key();
        $token_text = hash_hmac('sha256', $text, $key);
        var_dump($text);
        var_dump($token_text);
        return $token_text;
    }


    private function get_logs_test() {
        $data = array();
        $data['a'] = 0;
        $data['b'] = 'c';
        $data['c'] = 73.23;
        $data['d'] = Time::now()->toDateTimeString();
        $data['e'] = True;
        $data['f'] = False;
        #return $this->setResponseFormat('json')->respondCreated($data);
        return $this->respondCreated(json_encode($data));
    }

    /**
     * @deprecated
     */
    public function _get($startLogId) {
        trigger_error('Deprecated function called.', E_USER_DEPRECATED);
        $model = model(LogsModel::class);
        $model->where('id_log >', $startLogId);
        $model->orderBy('id_log');
        return $this->respond(json_encode($model->findAll()));
    }

    /**
     * this is like of http method get for API
     *  maybe rename this in get
     */
    public function get($startDate) {
        $model = model(LogsModel::class);
        $model->select(
            'date, id_badge, inside, id_log, id_user, date_badge, date_modif, '
            .'date_delete'
        );
        $model->where('date_modif >=', $startDate);
        $model->orderBy('date_modif');
        return $this->respond(json_encode($model->findAll()));
    }

    private function test() {
        $model = model(LogsModel::class);
        $date = '2022-10-10 16:33:32';
        $badgeId = '42';
        $inside = true;
        var_dump($model->is_replicate($date, $badgeId, $inside));
    }

    /**
     * http://localhost:8080/logs/test2
     */
    private function test2() {
        $date = '2022-11-01 14:35:45';
        $id_badge = 42;
        $inside = true;
        var_dump($this->put($date, $id_badge, $inside, $this->create_token(
            $date, $id_badge, $inside)));
    }

}
