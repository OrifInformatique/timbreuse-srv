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

    private function is_connected_user()
    {
        return session()->get('user_access')
            >= config('\User\Config\UserConfig')->access_lvl_registered;
    }

	public function index() {
        if ($this->is_connected_user()) {
            return redirect()->to(base_url() . 'PersoLogs/perso_time');
        }

        if (session()->get('user_access') == config('\User\Config\UserConfig')->access_lvl_guest) {
            $data['message'] = lang('tim_lang.403_error_message');

            return $this->display_view('User\Views\errors\403error', $data);
        } else {
            return redirect()->to('user/auth/login');
        }
	}
}
