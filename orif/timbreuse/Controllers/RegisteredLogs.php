<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\AccessTimModel;

class RegisteredLogs extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level=config('\User\Config\UserConfig')->access_lvl_registered;
        parent::initController($request, $response, $logger);
        $this->session=\Config\Services::session();
    }

    public function index()
    {
        return $this->time_list();
    }

    public function time_list($day = null, $period = null)
    {
        var_dump($this->session->get());
        $a = array();
        $a['b'] = 3;
        $this->session->set($a);
        var_dump($this->session->get());
        $this->session->remove('b');
        var_dump($this->session->get());
        $model = model(AccessTimModel::class);
        $id = $model->get_access_users($this->session->get('user_id'));
        var_dump($id);

    }



}
