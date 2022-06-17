<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\LogsModel;
use Timbreuse\Models\UsersModel;
use CodeIgniter\I18n\Time;

class PersoLogs extends BaseController
{
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level=config('\User\Config\UserConfig')->access_lvl_admin;
        parent::initController($request, $response, $logger);
        $this->session=\Config\Services::session();
    }


	public function perso_logs_list($userId, $day=null)
	{
        $badgesModel = model(BadgesModel::class);
        $logsModel = model(LogsModel::class);
        $usersModel = model(UsersModel::class);
		$data['title'] = "Welcome";
        $badgeId = $badgesModel->get_badges($userId);
        $logs = $logsModel->get_logs($badgeId);
        $user = $usersModel->get_users($userId);

		/**
         * Display a test of the generic "items_list" view (defined in common module)
         */

        $data['columns'] = ['date' => 'Date',
                            'id_badge' => 'Numéro du badge',
                            'inside' => 'Entrée'];

        if ($day === null) {
            $data['items'] = $logs;
            $data['list_title'] = "Tout les logs de".' '.$user['surname'].' '.$user['name'];
            $data['buttons'] = [
                ['link' => $userId.'/'.Time::today()->toDateString(), 'label' => 'Aujourd’hui'],
            ];
        } else {
            $day = Time::parse($day);
            $data['date'] = $day->toDateString();
            $data['list_title'] = $user['surname'].' '.$user['name'].' '.$data['date'];
            $filter = function($log) use($day) {
                return $this->filter_log_day($log, $day);
            };
            $data['items'] = array_filter($logs, $filter);
            $data['buttons'] = [
                ['link' => $day->subDays(1)->toDateString(), 'label' => '<'],
                ['link' => $day->addDays(1)->toDateString(), 'label' => '>'],
            ];
        }



        // $data['primary_key_field']  = 'date';
        // $data['btn_create_label']   = 'Add an item';
        // $data['url_detail'] = "items_list/detail/";
        // $data['url_update'] = "items_list/update/";
        // $data['url_delete'] = "items_list/delete/";
        // $data['url_create'] = "items_list/create/";
        $this->display_view(['Timbreuse\Views\menu', 'Timbreuse\Views\date','Common\Views\items_list'], $data);

	}

    public function filter_log_day($log, Time $day)
    {
        $logDay = Time::parse($log['date']);
        return $this->is_same_day($day, $logDay);
    }

    public function is_same_day(Time $day1, Time $day2) 
    {
        $bDay = $day1->getDay() == $day2->getDay();
        $bMonths = $day1->getMonth() == $day2->getMonth();
        $bYears = $day1->getYear() == $day2->getYear();
        return $bDay and $bMonths and $bYears;
    }

}