<?php


namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\BadgesModel;

use CodeIgniter\I18n\Time;

class Badges extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level = config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        return $this->badges_list();
    }

    public function badges_list()
    {
        $model = model(badgesModel::class);
        $data['title'] = lang('tim_lang.badges');

        /**
         * Display a test of the generic "items_list" view (defined in common module)
         */
        $data['list_title'] = ucfirst(lang('tim_lang.badges'));

        $data['columns'] = [
            'id_badge' =>ucfirst(lang('tim_lang.badgeId')),
            'name' =>ucfirst(lang('tim_lang.name')),
            'surname' =>ucfirst(lang('tim_lang.surname'))
        ];
        $data['items'] = $model->get_badges();


        $data['primary_key_field']  = 'id_badge';
        # $data['btn_create_label']   = 'Add an item';
        # $data['url_detail'] = "PersoLogs/perso_logs_list/";
        # $data['url_detail'] = "PersoLogs/time_list/";
        # $data['url_detail'] = "AdminLogs/time_list/";
        # $data['url_update'] = 'Users/ci_users_list/';
        # $data['url_update'] = 'Users/edit_tim_user/';
        # $data['url_delete'] = "items_list/delete/";
        # $data['url_create'] = "items_list/create/";
        $this->display_view('Common\Views\items_list', $data);
    }
}
