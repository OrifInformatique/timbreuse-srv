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

class Logs extends BaseController
{
    use ResponseTrait;
    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    )
    {
        $this->access_level = config(
            '\User\Config\UserConfig'
        )->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        return $this->logs_list();
    }

	public function logs_list()
	{
        $model = model(LogsModel::class);
		$data['title'] = "Welcome";

		/**
         * Display a test of the generic "items_list" view (defined in common module)
         */
		$data['list_title'] = "Test tout les logs";

        $data['columns'] = ['date' => 'Date',
                            'id_badge' => 'Numéro du badge',
                            'inside' => 'Entrée'];
        $data['items'] = $model->get_logs();


        // $data['primary_key_field']  = 'date';
        // $data['btn_create_label']   = 'Add an item';
        // $data['url_detail'] = "items_list/detail/";
        // $data['url_update'] = "items_list/update/";
        // $data['url_delete'] = "items_list/delete/";
        // $data['url_create'] = "items_list/create/";
        $this->display_view('Common\Views\items_list', $data);

	}

    /**
     * maybe rename this in put or post to looks like http method
     */
    public function add($date, $badgeId, $inside, $token)
    {
        $logModel = model(LogsModel::class);
        $badgeModel = model(badgesModel::class);
        $data = array();
        $data['date'] = $date;
        $data['id_badge'] = $badgeId;
        $data['inside'] = $inside;
        if ($token == $this->create_token($date, $badgeId, $inside)) {
            # direct reponse created if already in the db withtout insert 
            # again
            $userId = $badgeModel->select('id_user')->find($badgeId);
            $data += $userId;
            if ($logModel->is_replicate($date, $badgeId, $inside) or 
            ($logModel->insert($data))) {
                return $this->respondCreated();
            } else {
                return $this->failServerError('database error');
            }
        } else {
            return $this->failUnauthorized();
        }
    }

    private function create_token($date, $badgeId, $inside)
    {
        $text = $date.$badgeId.$inside;
        # $key = 'a'; # to put a truth in a file not in git
        helper('UtilityFunctions');
        $key = load_key();
        $token_text = hash_hmac('sha256', $text, $key);
        return $token_text;
    }

    # move in helper
    # private function load_key() {
    #     $fileText = file_get_contents('../.key.json');
    #     return json_decode($fileText, true)['key'];
    # }

    public function get_logs_test() {
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
     * this is like of http method get for API
     *  maybe rename this in get
     */
    public function get_logs($StartLogId) {
        $model = model(LogsModel::class);
        $model->where('id_log >', $StartLogId);
        $model->orderBy('id_log');
        # to fix with respond() but necessary change access level of the 
        # controller
        return $this->respondCreated(json_encode($model->findAll()));
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
    public function test2() {
        $date = '2022-11-01 14:35:45';
        $id_badge = 42;
        $inside = true;
        var_dump($this->add($date, $id_badge, $inside, $this->create_token($date,
                $id_badge, $inside)));
    }

}
