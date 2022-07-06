<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\UsersModel;
use Timbreuse\Models\AccessTimModel;
use User\Models\User_model;

class Users extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level = config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
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

        $data['columns'] = [
            'id_user' => 'id',
            'name' => 'Prénom',
            'surname' => 'Nom'
        ];
        $data['items'] = $model->get_users();


        $data['primary_key_field']  = 'id_user';
        // $data['btn_create_label']   = 'Add an item';
        #$data['url_detail'] = "PersoLogs/perso_logs_list/";
        #$data['url_detail'] = "PersoLogs/time_list/";
        $data['url_detail'] = "AdminLogs/time_list/";
        $data['url_update'] = 'Users/ci_users_list/';
        // $data['url_delete'] = "items_list/delete/";
        // $data['url_create'] = "items_list/create/";
        $this->display_view('Common\Views\items_list', $data);
    }

    public function ci_users_list($userId = 92)
    {
        $model = model(AccessTimModel::class);
        $modelCi = model(User_model::class);
        $data['title'] = "Welcome";


        $data['columns'] = [
            'id' => 'id_site',
            'username' => 'Prénom',
            'access' => 'accès',
        ];

        $data['items'] = $model->get_access_users_timb_to_ci($userId);

        #     $data['items'] = $modelCi->select('username, id, id_user')
        #         ->from('access_tim_user')->where('id=id_ci_user')
        #         ->where('id_user=', $userId)->findall();
        #     var_dump($data['items']);
        $data['items'] = $modelCi->select('id, username')->findall();
        $access = $model->select('id_ci_user')->where('id_user=', $userId)
            ->findall();

        $access = array_map(function ($access) {
            return array_pop($access);
        }, $access);



        $data['items'] = array_map(function (array $item) use ($access) {
            $item['access'] = array_search($item['id'], $access) !== false;
            return $item;
        }, $data['items']);

        $data['primary_key_field']  = 'id';
        $data['url_update'] = 'Users/add_access/' . $userId . '/';
        $data['url_delete'] = 'Users/delete_access/' . $userId . '/';
        $this->display_view('Common\Views\items_list', $data);
    }

    public function form_add_access($userId, $ciUserId)
    {
        $data = array();
        $data['userId'] = $userId;
        $data['ciUserId'] = $ciUserId;
        $this->display_view('timbreuse\Views\confirm_form', $data);
    }

    public function add_access($userId, $ciUserId)
    {
        $model = model(AccessTimModel::class);
        $data = array();
        $data['id_user'] = $userId;
        $data['id_ci_user'] = $ciUserId;
        $model->save($data);
        return redirect()->back();
    }

    public function delete_access($userId, $ciUserId)
    {
        $model = model(AccessTimModel::class);
        $data = array();
        $data['id_user'] = $userId;
        $data['id_ci_user'] = $ciUserId;
        $model->where('id_user=', $userId)->where('id_ci_user=', $ciUserId)
            ->delete();
        return redirect()->back();
    }
}
