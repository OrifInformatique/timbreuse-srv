<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\LogsModel;
use CodeIgniter\API\ResponseTrait;


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

    public function add($date, $badgeId, $inside, $token)
    {
        $model = model(LogsModel::class);
        $data = array();
        $data['date'] = $date;
        $data['id_badge'] = $badgeId;
        $data['inside'] = $inside;
        if ($token == $this->create_token($date, $badgeId, $inside)) {
            if ($model->insert($data)) {
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
        $key = 'a'; # to put a truth in a file not in git
        $token_text = hash_hmac('sha256', $text, $key);
        return $token_text;
    }
}
