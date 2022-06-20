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


	public function perso_logs_list($userId, $day=null, $period=null)
	{
        if (($day === null) or ($day == 'all')) {
            return redirect()->to($userId.'/'.Time::today()->toDateString().'/all');
        }
        if ($period === null) {
            return redirect()->to($day.'/day');
        }

        $user_data = $this->get_user_data($userId);
        $logs = $user_data['logs'];
        $user = $user_data['user'];

		$data['title'] = "Welcome";

		/**
         * Display a test of the generic "items_list" view (defined in common module)
         */

        $data['columns'] = ['date' => 'Date',
                            'id_badge' => 'Numéro du badge',
                            'inside' => 'Entrée'];
        $day = Time::parse($day);
        $data['period'] = $period;

        if ($period == 'all') {
            $data += $this->all_view($logs, $user);
        } elseif ($period == 'day') {
            $data += $this->day_view($logs, $user, $day);
        } elseif ($period == 'week') {
            $data += $this->week_view($logs, $user, $day);
        } elseif ($period == 'month') {
            $data += $this->month_view($logs, $user, $day);
        }
        $data['buttons'] += $this->create_buttons($period);


        $this->display_view(['Timbreuse\Views\menu', 'Timbreuse\Views\date','Common\Views\items_list'], $data);

	}

    protected function create_buttons($period) {
        $data = array();
        array_push($data, ['link' => '../', 'label' => 'Tout']);
        if ($period != 'all') {
            array_push($data, ['link' => '../'.Time::today()->toDateString().'/'.$period, 'label' => 'Aujourd’hui']);
        } else {
            array_push($data, ['link' => '../'.Time::today()->toDateString(), 'label' => 'Aujourd’hui']);
        }
        array_push($data, ['link' => 'day', 'label' => 'Jour']);
        array_push($data, ['link' => 'week', 'label' => 'Semaine']);
        array_push($data, ['link' => 'month', 'label' => 'Mois']);
        return $data;
    }
    
    protected function get_user_data($userId) {
        $badgesModel = model(BadgesModel::class);
        $logsModel = model(LogsModel::class);
        $usersModel = model(UsersModel::class);
        $badgeId = $badgesModel->get_badges($userId);
        $data['logs'] = $logsModel->get_logs($badgeId);
        $data['user'] = $usersModel->get_users($userId);
        return $data;
    }

    

    protected function month_view($logs, $user, $day) {
        $data['date'] = $day->toDateString();
        $data['list_title'] = $user['surname'].' '.$user['name'].' mois '.$data['date'];
        $filter = function($log) use($day) {
            return $this->filter_log_month($log, $day);
        };
        $data['items'] = array_filter($logs, $filter);
        $data['buttons'] = [
            ['link' => '../'.$day->subDays(30)->toDateString().'/month', 'label' => '<'],
            ['link' => '../'.$day->addDays(30)->toDateString().'/month', 'label' => '>'],
        ];
        return $data;
    }

    protected function week_view($logs, $user, $day) {
        $data['date'] = $day->toDateString();
        $data['list_title'] = $user['surname'].' '.$user['name'].' semaine '.$data['date'];
        $filter = function($log) use($day) {
            return $this->filter_log_week($log, $day);
        };
        $data['items'] = array_filter($logs, $filter);
        $data['buttons'] = [
            ['link' => '../'.$day->subDays(7)->toDateString().'/week', 'label' => '<'],
            ['link' => '../'.$day->addDays(7)->toDateString().'/week', 'label' => '>'],
        ];
        return $data;
    }

    protected function day_view($logs, $user, $day) {
        $data['date'] = $day->toDateString();
        $data['list_title'] = $user['surname'].' '.$user['name'].' '.$data['date'];
        $filter = function($log) use($day) {
            return $this->filter_log_day($log, $day);
        };
        $data['items'] = array_filter($logs, $filter);
        $data['buttons'] = [
            ['link' => '../'.$day->subDays(1)->toDateString(), 'label' => '<'],
            ['link' => '../'.$day->addDays(1)->toDateString(), 'label' => '>'],
        ];
        return $data;
    }

    protected function all_view($logs, $user) {
        $data['items'] = $logs;
        $data['list_title'] = "Tout les logs de".' '.$user['surname'].' '.$user['name'];
        $data['buttons'] = array();
        return $data;
    }

    protected function filter_log_month($log, Time $day)
    {
        $logDay = Time::parse($log['date']);
        return $this->is_same_month($day, $logDay);
    }

    protected function filter_log_week($log, Time $day)
    {
        $logDay = Time::parse($log['date']);
        return $this->is_same_week($day, $logDay);
    }
    
    protected function filter_log_day($log, Time $day)
    {
        $logDay = Time::parse($log['date']);
        return $this->is_same_day($day, $logDay);
    }

    protected function is_same_month(Time $day1, Time $day2) 
    {
        $bMonths = $day1->getMonth() === $day2->getMonth();
        $bYears = $day1->getYear() === $day2->getYear();
        return $bMonths and $bYears;
    }

    protected function is_same_week(Time $day1, Time $day2) 
    {
        $bWeek = $day1->getWeekOfYear() === $day2->getWeekOfYear();
        $bYears = $day1->getYear() === $day2->getYear();
        return $bWeek and $bYears;
    }
    protected function is_same_day(Time $day1, Time $day2) 
    {
        $bDay = $day1->getDay() === $day2->getDay();
        $bMonths = $day1->getMonth() === $day2->getMonth();
        $bYears = $day1->getYear() === $day2->getYear();
        return $bDay and $bMonths and $bYears;
    }

    public function test1() {
        $model = model(LogsModel::class);
        $date = Time::parse('2022-05-17');
        var_dump($model->get_filtered_logs('92', $date));
    }

}