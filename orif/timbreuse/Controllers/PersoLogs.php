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
        if (false) {
            return $this->perso_logs_list_old($userId, $day, $period);
        } else
        {
            return $this->perso_logs_list_new($userId, $day, $period);
        }
    }

	public function perso_logs_list_new($userId, $day=null, $period=null)
	{
        if (($day === null) or ($day == 'all')) {
            return redirect()->to($userId.'/'.Time::today()->toDateString().'/all');
        }
        if ($period === null) {
            return redirect()->to($day.'/day');
        }

        $usersModel = model(UsersModel::class);
        $user = $usersModel->get_users($userId);

		$data['title'] = "Welcome";

		/**
         * Display a test of the generic "items_list" view (defined in common module)
         */

        $data['columns'] = ['date' => 'Date',
                            'id_badge' => 'Numéro du badge',
                            'inside' => 'Entrée'];
        $day = Time::parse($day);
        $data['period'] = $period;
        $logsModel = model(LogsModel::class);
        $data['items'] = $logsModel->get_filtered_logs($userId, $day, $period);
        $sumTime = [
            'date' => 'Total temps',
            'id_badge' => $logsModel->get_hours_by_seconds($logsModel->get_time_array($data['items'])),
            'inside' => ''
        ];
        array_push($data['items'], $sumTime);

        
        $data['list_title'] = $this->create_title($user, $day, $period);
        $data['buttons'] = $this->create_buttons($period);
        if ($period != 'all') {
            $data['buttons'] = array_merge($this->create_time_links($day, $period), $data['buttons']);
            $data['date'] = $day->toDateString();
        }
        $this->display_view(['Timbreuse\Views\menu', 'Timbreuse\Views\date','Common\Views\items_list'], $data);

	}

    public function time_list($userId, $day=null, $period=null) {
        if (($day === null) or ($day == 'all')) {
            return redirect()->to($userId.'/'.Time::today()->toDateString().'/all');
        }
        if ($period === null) {
            return redirect()->to($day.'/day');
        }

        $usersModel = model(UsersModel::class);
        $user = $usersModel->get_users($userId);

        $data['title'] = "Welcome";


        $data['rows'] = [
            'morning' => lang('lang.rowMorning'),
            'afternoon' => lang('lang.rowAfternoon')
        ];


        $day = Time::parse($day);
        $data['period'] = $period;
        $logsModel = model(LogsModel::class);
        $data['items'] = $logsModel->get_week_time_table($userId, $day);
        
        $data['list_title'] = $this->create_title($user, $day, $period);
        $data['buttons'] = $this->create_buttons($period);
        if ($period != 'all') {
            $data['buttons'] = array_merge($this->create_time_links($day, $period), $data['buttons']);
            $data['date'] = $day->toDateString();
        }
        #$this->display_view(['Timbreuse\Views\menu', 'Timbreuse\Views\date','Common\Views\items_list'], $data);
        $this->display_view(['Timbreuse\Views\logs\week_time.php'], $data);

    }


    protected function create_time_links($day, $period)
    {
        switch ($period) {
            case 'day':
                $interval = 1;
                break;
            case 'month':
                $interval = 30;
                break;
            case 'week':
                $interval = 7;
                break;
        }
        $buttons = [
            ['link' => '../'.$day->subDays($interval)->toDateString().'/'.$period, 'label' => '<'],
            ['link' => '../'.$day->addDays($interval)->toDateString().'/'.$period, 'label' => '>'],
        ];
        return $buttons;
    }

    protected function create_title($user, $day, $period)
    {
        $date = $day->toDateString();
        switch ($period) {
            case 'day':
                return $user['surname'].' '.$user['name'].' '.$date;
                break;
            case 'month':
                return $user['surname'].' '.$user['name'].' mois '.$date;
                break;
            case 'week':
                return $user['surname'].' '.$user['name'].' semaine '.$date;
                break;
            case 'all':
                return 'tous les logs '.$user['surname'].' '.$user['name'];
                break;
        }
    }

	public function perso_logs_list_old($userId, $day=null, $period=null)
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

    private function test1() {
        $model = model(LogsModel::class);
        $date = Time::parse('2022-05-20');
        var_dump($model->get_filtered_logs(92, $date, 'week'));
    }

    private function test2() {
        $time = Time::parse('1970-01-01');
        $time1 = Time::parse('1970-01-13');
        $diff = $time1->difference($time);
        var_dump($diff->seconds);
    }
    protected function test3() {
        $logs = array();
        $logs[0]['date'] = '2022-01-01 12:35';
        $logs[1]['date'] = '2022-01-01 12:50';
        $logs[2]['date'] = '2022-01-01 12:57';
        $logs[3]['date'] = '2022-01-01 13:00';
        $logs[4]['date'] = '2022-01-01 13:03';
        $logs[5]['date'] = '2022-01-02 08:03';
        $logs[6]['date'] = '2022-01-02 09:03';
        $logs[0]['inside'] = 1;
        $logs[1]['inside'] = 0;
        $logs[2]['inside'] = 1;
        $logs[3]['inside'] = 0;
        $logs[4]['inside'] = 1;
        $logs[5]['inside'] = 1;
        $logs[6]['inside'] = 0;
        $time = $this->get_time_array($logs);
        var_dump($time);
        // Expecting: 4680
    }
    protected function test4() {
        $logs = array();
        $logs[0]['date'] = '2022-01-01 12:35';
        $logs[1]['date'] = '2022-01-01 12:50';
        $logs[2]['date'] = '2022-01-01 12:57';
        $logs[3]['date'] = '2022-01-01 13:00';
        $logs[4]['date'] = '2022-01-01 13:03';
        $logs[5]['date'] = '2022-01-02 08:03';
        $logs[6]['date'] = '2022-01-02 09:03';
        $logs[7]['date'] = '2022-01-03 08:03';
        $logs[8]['date'] = '2022-01-03 09:03';
        $logs[0]['inside'] = 1;
        $logs[1]['inside'] = 0;
        $logs[2]['inside'] = 1;
        $logs[3]['inside'] = 0;
        $logs[4]['inside'] = 1;
        $logs[5]['inside'] = 1;
        $logs[6]['inside'] = 0;
        $logs[7]['inside'] = 1;
        $logs[8]['inside'] = 0;
        $time = $this->get_time_array($logs);
        var_dump($time);
        // Expecting: 8280
    }

    public function test5() {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $halfDay = 'morning';
        $halfDay = 'afternoon';
        $userId = '92';
        $model = model(LogsModel::class);
        var_dump($model->get_logs_by_period($userId, $day, $halfDay));
        
    }

    public function test6() {
        $day = '2022-05-18';
        $day = Time::parse($day);
        var_dump($this->get_last_monday($day));
    }

    public function test7() {
        $days[0] = '2022-05-18';
        $days[1] = '2022-05-18';
        $days[2] = '2022-05-18';
        $days[3] = '2022-05-18';
        $days[4] = '2022-05-18';
        foreach ($days as $i => $day) {
            $day = Time::parse($day);
            var_dump($this->get_last_monday($day)->addDays($i));
        }
    }

    public function test8() {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $model = model(LogsModel::class);
        var_dump($model->get_border_log_by_period('92', $day, 'morning', true));
        /* Expecting:
            array (size=3)
                'date' => string '2022-05-18 12:00:28' (length=19)
                'id_badge' => string '589402514225' (length=12)
                'inside' => string '0' (length=1)
        */
    }

    public function test9() {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $model = model(LogsModel::class);
        var_dump($model->get_day_time_table('92', $day, 'morning'));
        /* Expecting:
            array (size=3)
                'time' => string '4:02:01' (length=7)
                'first' => string '07:58:27' (length=8)
                'last' => string '12:00:28' (length=8)
        */
    }

    public function test10() {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $model = model(LogsModel::class);
        var_dump($model->get_upper_day_time_table('92', $day));
        /* Expecting:
            array (size=3)
                'dayNb' => string '18' (length=2)
                'morning' => 
                    array (size=3)
                        'time' => string '4:02:01' (length=7)
                        'first' => string '07:58:27' (length=8)
                        'last' => string '12:00:28' (length=8)
                'afternoon' => 
                    array (size=3)
                        'time' => string '4:16:02' (length=7)
                        'first' => string '12:31:27' (length=8)
                        'last' => string '16:47:29' (length=8)
            */
    }

    public function test11() {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $model = model(LogsModel::class);
        var_dump($model->get_week_time_table('92', $day));
    }
}