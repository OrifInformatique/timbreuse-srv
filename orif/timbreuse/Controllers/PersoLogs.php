<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\LogsModel;
use Timbreuse\Models\UsersModel;

class PersoLogs extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level=config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session=\Config\Services::session();
    }

    public function index($arg)
    {
        return $this->perso_logs_list($arg);
    }

	public function perso_logs_list($userId=92)
	{
        $badgesModel = model(BadgesModel::class);
        $logsModel = model(LogsModel::class);
        $usersModel = model(UsersModel::class);
		$data['title'] = "Welcome";
        $badgeId = $badgesModel->get_badges($userId);
        $logs = $logsModel->get_logs($badgeId);

		/**
         * Display a test of the generic "items_list" view (defined in common module)
         */
		$data['list_title'] = "Test tout les logs d'un utilisateur de la timbeurse";

        $data['columns'] = ['date' => 'Date',
                            'id_badge' => 'Numéro du badge',
                            'inside' => 'Entrée'];

        $data['items'] = $logs;


        // $data['primary_key_field']  = 'date';
        // $data['btn_create_label']   = 'Add an item';
        // $data['url_detail'] = "items_list/detail/";
        // $data['url_update'] = "items_list/update/";
        // $data['url_delete'] = "items_list/delete/";
        // $data['url_create'] = "items_list/create/";
        $this->display_view('Common\Views\items_list', $data);

	}
}