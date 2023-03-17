<?php


namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\UsersModel;

use CodeIgniter\I18n\Time;

class Badges extends BaseController
{
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level = config('\User\Config\UserConfig')
             ->access_lvl_admin;
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

        # Display a test of the generic "items_list" view (defined in common 
        # module)

        $data['list_title'] = ucfirst(lang('tim_lang.badges'));

        $data['columns'] = [
            'id_badge' =>ucfirst(lang('tim_lang.badgeId')),
            'name' =>ucfirst(lang('tim_lang.name')),
            'surname' =>ucfirst(lang('tim_lang.surname'))
        ];
        $data['items'] = $model->get_badges_and_user_info();


        $data['primary_key_field']  = 'id_badge';
        # $data['btn_create_label']   = 'Add an item';
        # $data['url_detail'] = "AdminLogs/time_list/";
        $data['url_update'] = 'Badges/edit_badge_relation/';
        # $data['url_delete'] = "items_list/delete/";
        # $data['url_create'] = "items_list/create/";
        $this->display_view('Common\Views\items_list', $data);
    }

    public function edit_badge_relation($badgeId)
    {
        if (($this->request->getMethod() === 'post') and $this->validate([
            'timUserId' => 'required|integer',
            'badgeId' => 'required|integer'
        ])) {
            return $this->post_edit_badge_relation();
        }
        $data = $this->get_data_for_edit_badge_relation($badgeId);
        $this->display_view('Timbreuse\Views\Badges\edit_badges', $data);
    }

    public function get_data_for_edit_badge_relation($badgeId)
    {
        $data['badgeId'] = $badgeId;
        $data['postUrl'] = '../post_edit_badge_relation/' . $badgeId;
        $data['returnUrl'] = '..' . $badgeId;
        $model = model(badgesModel::class);
        $data['availableUsers'] = $model->get_badge_and_user_info($badgeId);
        # ici reprendre lundi !!!!!!!!!!!!!!!!!!!!!!

        # $model = model(usersModel::class);
        # $data['availableUsers'] = $model->get_badges_and_user_info($badgeId);
        $data['labels']['user'] = ucfirst(lang('tim_lang.timUsers'));
        $data['labels']['back'] = ucfirst(lang('tim_lang.back'));
        $data['labels']['modify'] = ucfirst(lang('tim_lang.modify'));
        $data['labels']['dealloc'] = ucfirst(lang('tim_lang.dealloc'));
        $data['h3title'] = ucfirst(sprintf(lang('tim_lang.edit_badge'),
            $badgeId));
        $data['deallocUrl'] = '#';
        return $data;
    }
}

