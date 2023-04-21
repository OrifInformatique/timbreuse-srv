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

        $data['title'] = ucfirst(lang('tim_lang.titlePlanning'));
        $data['h3title'] = ucfirst(lang('tim_lang.titlePlanning'));
        $data['labels']['monday'] = ucfirst(lang('tim_lang.monday'));
        $data['labels']['tuesday'] = ucfirst(lang('tim_lang.tuesday'));
        $data['labels']['wednesday'] = ucfirst(lang('tim_lang.wednesday'));
        $data['labels']['thursday'] = ucfirst(lang('tim_lang.thursday'));
        $data['labels']['friday'] = ucfirst(lang('tim_lang.friday'));
        $data['labels']['dueTime'] = ucfirst(lang('tim_lang.dueTime'));
        $data['labels']['offeredTime'] = ucfirst(lang('tim_lang.offeredTime'));
        $data['labels']['cancel'] = ucfirst(lang('tim_lang.cancel'));
        $data['labels']['save'] = ucfirst(lang('common_lang.btn_save'));

        $this->display_view(
            [
                'Timbreuse\Views\planning\edit_planning.php'
            ],
            $data
        );
    }

}

