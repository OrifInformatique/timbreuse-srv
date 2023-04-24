<?php


namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\UsersModel;

use CodeIgniter\I18n\Time;

class Planning extends BaseController
{
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level = config('\User\Config\UserConfig')
             ->access_lvl_registered;
        # $this->access_level = config('\User\Config\UserConfig')
        #      ->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
    }


    public function index()
    {
        return redirect()->to(current_url() . '/' . 'edit_planning/');
    }

    private function get_label_for_edit_planning() {

        $data['monday'] = ucfirst(lang('tim_lang.monday'));
        $data['tuesday'] = ucfirst(lang('tim_lang.tuesday'));
        $data['wednesday'] = ucfirst(lang('tim_lang.wednesday'));
        $data['thursday'] = ucfirst(lang('tim_lang.thursday'));
        $data['friday'] = ucfirst(lang('tim_lang.friday'));
        $data['dueTime'] = ucfirst(lang('tim_lang.dueTime'));
        $data['offeredTime'] = ucfirst(lang('tim_lang.offeredTime'));
        $data['cancel'] = ucfirst(lang('tim_lang.cancel'));
        $data['save'] = ucfirst(lang('common_lang.btn_save'));
        return $data;

    }

    public function edit_planning() {
        $data['title'] = ucfirst(lang('tim_lang.titlePlanning'));
        $data['h3title'] = ucfirst(lang('tim_lang.titlePlanning'));
        $data['labels'] = $this->get_label_for_edit_planning();
        $data['dueTime'] = '';
        $data['offeredTime'] = '';

        $this->display_view(
            [
                'Timbreuse\Views\planning\edit_planning.php'
            ],
            $data
        );
   }

}

