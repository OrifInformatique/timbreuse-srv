<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class Home extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $this->session=\Config\Services::session();
    }

	public function index() {
        // Check user access level
        if (session()->get('user_access') >= config('\User\Config\UserConfig')->access_lvl_registered) {
            return redirect()->to(base_url() . 'PersoLogs/perso_time');
        } elseif (session()->get('user_access') == config('\User\Config\UserConfig')->access_lvl_guest) {
            // Guest users are not managed in this application
            $data['message'] = lang('tim_lang.403_error_message');
            return $this->display_view('User\Views\errors\403error', $data);
        } else {
            return redirect()->to('user/auth/login');
        }
	}
}
