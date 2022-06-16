<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\UsersModel;

class Users extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level=config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session=\Config\Services::session();
    }

    public function index()
    {
        return $this->users_list();
    }

	public function users_list()
	{
        $model = model(UsersModel::class);
		$data['title'] = "Welcome";

		/**
         * Display a test of the generic "items_list" view (defined in common module)
         */
		$data['list_title'] = "Test tout les utilisateurs de la timbeurse";

        $data['columns'] = ['id_user' => 'id',
                            'name' => 'Prénom',
                            'surname' => 'Nom'];
        $data['items'] = $model->get_users();


        $data['primary_key_field']  = 'id_user';
        // $data['btn_create_label']   = 'Add an item';
        $data['url_detail'] = "PersoLogs/perso_logs_list/";
        // $data['url_update'] = "items_list/update/";
        // $data['url_delete'] = "items_list/delete/";
        // $data['url_create'] = "items_list/create/";
        $this->display_view('Common\Views\items_list', $data);

	}
}