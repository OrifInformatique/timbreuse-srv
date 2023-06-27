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

	public function index()
	{
        return redirect()->to(current_url() . '/PersoLogs/perso_time');
		$data['title'] = "Welcome";

		/**
         * Display a test of the generic "items_list" view 
         * (defined in common module)
         */

        $data['buttons'] = [
            ['link' => 'logs', 'label' => 'Logs'],
            ['link' => 'users', 'label' => 'Users'],
            ['link' => 'persoLogs\perso_time', 'label' => 'perso_time'],
        ];

		return $this->display_view(['Timbreuse\Views\menu'], $data);
	}

}
