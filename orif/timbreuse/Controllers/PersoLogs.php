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
use Timbreuse\Models\AccessTimModel;
use Timbreuse\Models\LogsFakeLogsModel;
use Timbreuse\Models\FakeLogsModel;
use Timbreuse\Models\PlanningsModel;

class PersoLogs extends BaseController
{
    const RETURN_METHOD_NAME = 'perso_time';
    
    public function initController(RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level = config('\User\Config\UserConfig')
             ->access_lvl_registered;
        parent::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        return redirect()->to(current_url() . '/perso_time');
    }

    protected function get_last_monday(Time $day): Time
    {
        helper('UtilityFunctions');
        return get_last_monday($day);
    }

    protected function get_month_week_array(int $timUserId, Time $date): array
    {
        $weeks = array();
        $firstDay = Time::create($date->year, $date->month, 1);
        $monday = $this->get_last_monday($firstDay);

        do {
            array_push($weeks, $this->get_day_week_array($timUserId, $monday));
            $monday = $monday->addDays(7);
        } while ($monday->month == $date->month);

        return $weeks;
    }

    protected function get_workdays_text_with_number(time $monday,
        time $lastDay, bool $withYear): string
    {
        $mondayText = $monday->toLocalizedString('dd.MM – ');
        if ($withYear) {
            $lastDayText = $lastDay->toLocalizedString('dd.MM.yyyy');
        } else {
            $lastDayText = $lastDay->toLocalizedString('dd.MM');
        }
        return $mondayText . $lastDayText;
    }

    protected function get_workdays_text_with_month_name(time $monday,
        time $lastDay, bool $withYear): string
    {
        $mondayText = $monday->toLocalizedString('d MMMM – ');
        if ($withYear) {
            $lastDayText = $lastDay->toLocalizedString('d MMMM yyyy');
        } else {
            $lastDayText = $lastDay->toLocalizedString('d MMMM');
        }
        return $mondayText . $lastDayText;
    }

    protected function get_workdays_text(Time $date, ?int $dayNumber=6,
        ?bool $withYear=false, ?bool $withNumber=true): string
    {
        $monday = $this->get_last_monday($date);
        $lastDay = $monday->addDays($dayNumber);
        if ($withNumber) {
            return $this->get_workdays_text_with_number($monday, $lastDay,
                $withYear);
        }
        return $this->get_workdays_text_with_month_name($monday, $lastDay,
            $withYear);
    }

    protected function get_hours_by_seconds(int $seconds): string
    {
        helper('UtilityFunctions');
        return get_hours_by_seconds($seconds);
    }


    /**
     * calculate the time
     * to retest
     */
    protected function get_time_array(array $logs): int
    {
        $dateIn = null;
        $invokeCalcule = function (?int $carry, array $log) use (&$dateIn)
        {
            return $this->reduce_time_array($carry, $log, $dateIn);
        };
        $seconds = array_reduce($logs, $invokeCalcule);
        if ($seconds === null) {
            $seconds = 0;
        }
        return $seconds;
    }

    protected function reduce_time_array(?int $carry, array $log,
        ?string &$dateIn): int
    {
        $isGoInside = boolval($log['inside']);
        $isOutside = $dateIn === null;
        $isSameDay = !$isOutside ? $this->is_same_day(Time::parse($dateIn),
            Time::parse($log['date'])): null;
        $isStartPeriod = (($isGoInside and $isOutside) or !$isSameDay);
        $isEndPeriod = (!$isGoInside and !$isOutside and $isSameDay);
        if ($isStartPeriod)
        {
            $dateIn = $log['date'];
        } elseif ($isEndPeriod)
        {
            $carry += abs(Time::parse($log['date'])->difference($dateIn)
                ->seconds);
            $dateIn = null;
        }
        return $carry ?? 0;
    }

    /* for month view */
    protected function get_day_week_array(int $timUserId, Time $date): array
    {
        $model = model(LogsModel::class);
        $logs = $model->get_filtered_logs($timUserId, $date, 'week');
        $data['label_week'] = $this->get_workdays_text($date);
        $data['time'] = $this->get_hours_by_seconds(
                $this->get_time_array($logs));
        $data['url'] = '../' . $date->toDateString() . '/week';
        return $data;
    }


    protected function get_texts_for_day_view()
    {
        $data['title'] = lang('tim_lang.day');
        $data['columns'] = array();
        $data['columns'][0] = lang('tim_lang.hour');
        $data['columns'][1] = lang('tim_lang.enter/exit');
        return $data;
    }

    protected function get_page_title_for_log_views($timUserId, $day, $period)
    {
        $usersModel = model(UsersModel::class);
        $user = $usersModel->get_users($timUserId);
        $data['list_title'] = $this->create_title($user, $day, $period);
        return $data;
    }

    protected function get_sum_time_for_day_view($timUserId, $day, $period)
    {
        $sumTime = array();
        $sumTime['time'] = $this->get_time_day_by_period($timUserId, $day,
            $period);
        $sumTime['date'] = ucfirst(lang('tim_lang.timeTotal'));
        return $sumTime;
    }

    protected function create_planning_link(?int $timUserId=null): array
    {
        helper('UtilityFunctions');
        if ($timUserId === get_tim_user_id()) {
            $button['link'] = base_url('/Plannings/get_plannings_list/');
        } else {
            $button['link'] = base_url(
                "/Plannings/get_plannings_list/$timUserId");
        }
        $button['label'] = ucfirst(lang('tim_lang.planning'));
        return $button;
    }
    
    protected function create_event_planning_link(?int $timUserId=null): array
    {
        helper('UtilityFunctions');
        if ($timUserId === get_tim_user_id()) {
            $button['link'] = base_url('event-plannings');
        } else {
            $button['link'] = base_url("event-plannings/$timUserId");
        }
        $button['label'] = ucfirst(lang('tim_lang.event_plannings_list'));
        return $button;
    }

    protected function get_buttons_for_log_views($day, $period,
            ?int $timUserId=null): array
    {
        $data['buttons'] = $this->create_buttons($period);
        $data['buttons'] = array_merge(
            $this->create_time_links($day, $period),
            $data['buttons']
        );
        array_push($data['buttons'], $this->create_planning_link($timUserId));
        array_push ($data['buttons'], $this->create_event_planning_link($timUserId));
        return $data;
    }

    /**
     * add period, date, userId(for day viw) to data array
     */
    protected function put_args_in_array_for_log_views($timUserId, $day, $period)
    {
        $data['period'] = $period;
        $data['date'] = $day->toDateString();
        if ($period == 'day') {
            $data['userId'] = $timUserId;
        }
        return $data;
    }

    protected function time_list_day($timUserId, $day = null, $period = null)
    {
        $day = Time::parse($day);
        $data = $this->put_args_in_array_for_log_views($timUserId, $day,
            $period);
        $data['items'] = $this->get_day_view_day_array($timUserId, $day);
        $data += $this->get_texts_for_day_view();
        $data += $this->get_page_title_for_log_views($timUserId, $day,
            $period);
        $data += $this->get_buttons_for_log_views($day, $period, $timUserId);
        $data['sumWorkTime'] = $this->get_time_day_by_period($timUserId, $day,
            $period);
        $data = array_merge($data, $this->get_detail_time_array($timUserId,
            $day, $period));
        return $this->display_view(['Timbreuse\Views\period_menu',
                'Timbreuse\Views\date', 'Timbreuse\Views\logs\day_time.php'],
                $data);
    }


    protected function get_detail_time_array_null() {
        $date_null = lang('tim_lang.unDefineDate');
        $data['offeredTime'] = $date_null;
        $data['sumTime'] = $date_null;
        $data['balance'] = $date_null;
        $data['dueTime'] = $date_null;
        return $data;
    }

    protected function get_detail_time_array(int $timUserId, string $day,
        string $period, bool $asterisk=false): array
    {
        $planningModel = model(PlanningsModel::class);
        if (!$planningModel->has_planning_by_period($timUserId, $day,
            $period))
        {
            return $this->get_detail_time_array_null();
        }
        $time = Time::parse($day);
        $data['offeredTime'] = $this->get_offered_time_show_by_period(
            $timUserId, $day, $period);
        $data['dueTime'] = $planningModel->get_due_time_by_period($timUserId,
            $day, $period);
        $data['sumTime'] = $this->get_total_time_by_period($timUserId, $day,
            $period, $asterisk);
        if ($asterisk) {
            $data['balance'] = $this->get_balance_by_period_asterisk(
                $timUserId, $day, $period);
        } else {
            $data['balance'] = $this->get_balance_by_period($timUserId, $day,
                $period);
        }
        return $data;
    }

    protected function get_offered_time_show_day(int $timUserId,
        string $date): ?string
    {
        $planningModel = model(PlanningsModel::class);
        $planningTime = $planningModel
                ->get_planning_time_day($date, $timUserId);
        if (is_null($planningTime)) {
            return null;
        }
        $date = Time::parse($date);
        $logsTime = $this->get_time_day_by_period($timUserId, $date, 'day');
        $times = $this->to_seconds_for_planning_day($planningTime[0],
                $planningTime[1], $logsTime);
        $offeredTimeSeconds = $this->get_offered_time_seconds(
            $times['dueTime'], $times['offeredTime'], $times['logsTime']);
        $offeredTime = $this->get_hours_by_seconds($offeredTimeSeconds);
        return $offeredTime;
    }

    protected function get_offered_time_show_period(string $firstDay,
            int $numberOfDay, int $timUserId)
    {
        return $this->get_time_period($firstDay, $numberOfDay, $timUserId,
            'get_offered_time_show_day');
    }

    /* is duplicate from planningmodel */
    public function get_time_period(string $firstDay, int $numberOfDay,
        int $timUserId, string $methodName): string
    {
        $days = range(0, $numberOfDay - 1);
        $firstDay = Time::parse($firstDay);
        $daysDate = array_map(array($firstDay, 'addDays'), $days);
        $daysDateText = array_map(fn($day) => $day->toDateString(), $daysDate);
        $daysText = array_map(fn($day) => call_user_func_array(
            array($this, $methodName), array($timUserId, $day)),
            $daysDateText);
        $daysTextFiltered = array_filter($daysText,
            fn($text) => !is_null($text));
        if (is_null($daysTextFiltered)) {
            return null;
        }
        $daysSeconds = array_map(array($this, 'toSeconds'),
                $daysTextFiltered); 
        $seconds = array_reduce($daysSeconds,
                fn($carry, $day) => $carry + $day);
        $text = $this->get_hours_by_seconds($seconds);
        return $text;
    }


    protected function get_offered_time_seconds(int $dueTime, int $offeredTime,
        int $logsTime): int
    {
        if ($dueTime === $offeredTime) {
            return $offeredTime;
        }
        if ($logsTime === 0) {
            $nullTime = 0;
            return $nullTime;
        }
        return $offeredTime;
    }

    protected function get_total_time_by_period(int $timUserId, string $day,
        string $period, bool $asterisk=false): string
    {
        $time = Time::parse($day);
        $sumWorkTime = $this->get_time_day_by_period($timUserId, $time,
            $period);
        $planningModel = model(PlanningsModel::class);
        $offeredTime = $planningModel->get_offered_time_by_period($timUserId,
            $day, $period);
        $dueTime = $planningModel->get_due_time_by_period($timUserId, $day,
            $period);
        $offeredTimeSeconds = $this->toSeconds($offeredTime);
        $dueTimeSeconds = $this->toSeconds($dueTime);
        if ($offeredTimeSeconds === $dueTimeSeconds) {
            return $this->get_hours_by_seconds($dueTimeSeconds);
        }
        $sumWorkTimeSeconds = $this->toSeconds($sumWorkTime);
        if ($sumWorkTimeSeconds == 0) {
            return $this->get_hours_by_seconds(0);
        }
        $totalTimeSecond = $sumWorkTimeSeconds + $offeredTimeSeconds;
        $totalTime =  $this->get_hours_by_seconds($totalTimeSecond);
        $model = model(LogsModel::class);
        $logs = $model->get_filtered_logs($timUserId, $time, $period);
        return ($this->is_not_tim_logs($logs) and $asterisk) ?
            $totalTime . '✱' : $totalTime;
    }

    protected function time_list_month($timUserId, $day = null, $period = null)
    {
        $data['title'] = lang('tim_lang.month');
        $data['columns'] = array();
        $data['columns'][0] = lang('tim_lang.week');
        $data['columns'][1] = lang('tim_lang.time');
        $day = Time::parse($day);
        $data += $this->put_args_in_array_for_log_views($timUserId, $day,
                $period);
        $data['items'] = $this->get_month_week_array($timUserId, $day);
        $data += $this->get_page_title_for_log_views($timUserId, $day,
            $period);
        $data += $this->get_buttons_for_log_views($day, $period, $timUserId);
        $data['sumWorkTime'] = $this->get_time_day_by_period($timUserId, $day,
            $period);
        $data = array_merge($data, $this->get_detail_time_array($timUserId,
            $day, $period));
        //$data['balance'] = $this->get_balance_month($timUserId, $day);
        return $this->display_view(['Timbreuse\Views\period_menu',
                'Timbreuse\Views\date', 'Timbreuse\Views\logs\month_time.php'],
                $data);
    }

    /**
     * use for week view with time
     */
    protected function get_day_time_table($timUserId, $date, $halfDay): array
    {
        $model = model(LogsModel::class);
        $logs = $model->get_logs_by_period($timUserId, $date, $halfDay);
        $data['time'] = $this->get_time_array($logs);
        $data['time'] = $this->get_hours_by_seconds($data['time']);
        if ($this->is_not_tim_logs($logs)) {
            $data['time'] = $data['time'] . '✱';
        }
        $data['firstEntry'] = $this->get_string_time_for_day_time_table(
            $timUserId, $date, $halfDay, false);
        $data['lastOuting'] = $this->get_string_time_for_day_time_table(
            $timUserId, $date, $halfDay, true);
        return $data;
    }


    protected function get_string_time_for_day_time_table(int $timUserId, $date,
        $halfDay, bool $isLast): string
    {
        $model = model(LogsModel::class);
        $entry = $model->get_border_log_by_period($timUserId, $date, $halfDay,
                                                    $isLast);
        # add because bug when production on site infomaniak
        if (isset($entry['date'])){
            $entryStr = Time::parse($entry['date'])->toTimeString();
        } else {
            return '';
        }
        if ($this->is_not_tim_log($entry)) {
            $entryStr .= '✱';
        }
        return $entryStr;
    }

    protected function is_site_log(array $log): bool
    {
        return isset($log['date_badge']); 
    }

    protected function is_site_logs(array $logs): bool
    {
        foreach ($logs as $log) {
            if (isset($log['date_badge'])) {
                return true;
            }
        }
        return false;
    }

    protected function is_not_tim_logs(array $logs):bool
    {
        foreach ($logs as $log) {
            if ($this->is_not_tim_log($log)) {
                return true;
            }
        }
        return false;
    }

    /**
     * log is created on site or modifyed
     */
    protected function is_not_tim_log(array $log):bool
    {
        if (isset($log['date'])) {
            return $log['date'] != $log['date_badge'];
        }
        return false;
    }

    /**
     * use for week view with time
     */
    protected function get_upper_day_time_table($timUserId, $date,
        $fakeLog = true): array
    {
        $data['dayNb'] = $date->day;
        $data['url'] = '../' . $date->toDateString() . '/day';
        $data['morning'] = $this->get_day_time_table($timUserId, $date,
                'morning', $fakeLog);
        $data['afternoon'] = $this->get_day_time_table($timUserId, $date,
                'afternoon', $fakeLog);
        $data['time'] = $this->get_time_day_by_period_with_asterisk($timUserId,
            $date, 'day');
        $data = array_merge($data, $this
            ->get_detail_time_array($timUserId, $date, 'day', true));
        return $data;
    }

    /**
     * use for week view with time
     */
    protected function get_week_time_table($timUserId, $date): array
    {
        $monday = $this->get_last_monday($date);
        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        $data = array();
        foreach ($weekdays as $i => $weekday) {
            $data[$weekday] = $this->get_upper_day_time_table($timUserId,
                    $monday->addDays($i));
        }
        return $data;
    }

    protected function get_texts_for_week_view()
    {
        $data['title'] = lang('tim_lang.week');
        $data['rows'] = [
            'morning' => lang('tim_lang.rowMorning'),
            'afternoon' => lang('tim_lang.rowAfternoon'),
            'time' => ucfirst(lang('tim_lang.workTime')),
            'offeredTime' => ucfirst(lang('tim_lang.offeredTime')),
            'sumTime' => ucfirst(lang('tim_lang.timeTotal')),
            'dueTime' => ucfirst(lang('tim_lang.dueTime')),
            'balance' => ucfirst(lang('tim_lang.balance')),
        ];
        $data['rows2'] = [
            'time' => lang('tim_lang.time'),
            'firstEntry' => lang('tim_lang.firstEntry'),
            'lastOuting' => lang('tim_lang.lastOuting'),
        ];
        return $data;
    }

    protected function time_list_week($timUserId, $day = null, $period = null)
    {
        $day = Time::parse($day);
        $data = $this->put_args_in_array_for_log_views($timUserId, $day,
                $period);
        $data += $this->get_texts_for_week_view();
        $data['items'] = $this->get_week_time_table($timUserId, $day,);
        $data['sumWorkTime'] = $this->get_time_day_by_period_with_asterisk(
                $timUserId, $day, $period);
        $data = array_merge($data, $this->get_detail_time_array($timUserId,
            $day, $period, true));
        $data += $this->get_page_title_for_log_views($timUserId, $day,
            $period);
        $data += $this->get_buttons_for_log_views($day, $period, $timUserId);
        $data['oneRowKey'] = $this->get_key_one_row_for_week();
        return $this->display_view(['Timbreuse\Views\period_menu',
                'Timbreuse\Views\date', 'Timbreuse\Views\logs\week_time.php'],
                $data);
    }

    protected function get_key_one_row_for_week()
    {
        $data[0] = 'time';
        $data[1] = 'offeredTime';
        $data[2] = 'sumTime';
        $data[3] = 'dueTime';
        $data[4] = 'balance';
        return $data;
    }

    /**
     * @deprecated
     */
    public function turnSiteData()
    {
        trigger_error('Deprecated function called.', E_USER_DEPRECATED);
        session()->set('isFakeLog', !session()->get('isFakeLog'));
        return redirect()->back();
    }


    protected function redirect_admin()
    {
        $ciUserId = $this->get_ci_user_id();
        $accessModel = model(AccessTimModel::class);
        if ($accessModel->have_one_access($ciUserId)) {
            $timUserId = $accessModel->get_tim_user_id($ciUserId);
            return redirect()->to(current_url() . '/../../AdminLogs/time_list/'
                .$timUserId);
        } else {
            return redirect()->to(current_url() . '/../../Users');
        }
    }

    protected function invoke_registered_user(array $timUserIds)
    {
            if (count($timUserIds) === 0) {
                $output['message'] = lang('tim_lang.errorNoTimAccess');
                return $this->display_view('User\Views\errors\403error', $output);
            }
            
            return $this->access_user_list();
    }

    protected function is_registered_user()
    {
        return session()->get('user_access')
            === config('\User\Config\UserConfig')->access_lvl_registered;
    }
    public function perso_time(?string $day = null, ?string $period = null)
    {
        if ($this->is_admin()) {
            return $this->redirect_admin();
        }
        if (!$this->is_registered_user()) {
            return;
        }
        if (($day === null) and ($period === null)){
            session()->remove('userIdAccess');
        }
        $model = model(AccessTimModel::class);
        $timUserIds = $model->get_access_users($this->get_ci_user_id());
        if ((!session()->has('userIdAccess')) and (count($timUserIds) !== 1)) {
            return $this->invoke_registered_user($timUserIds);
        } else {
            $timUserId = session()->get('userIdAccess')
                ?? $timUserIds[0]['id_user'];
            $this->check_and_block_user($timUserId);
        }
        return $this->redirect_for_perso_time($timUserId, $day, $period);
    }

    protected function redirect_for_perso_time(int $timUserId,
        ?string $day = null, ?string $period = null)
    {
        if (($day === null)) {
            return redirect()->to(current_url() . '/../perso_time/'
                . Time::today()->toDateString() . '/day');
        }
        if ($period === null) {
            return redirect()->to(current_url() . '/../' . $day . '/day');
        }
        return $this->perso_time_period($timUserId, $day, $period);

    }

    protected function perso_time_period($timUserId, $day, $period)
    {
        switch ($period) {
            case 'week':
                return $this->time_list_week($timUserId, $day, $period);
                break;
            case 'month':
                return $this->time_list_month($timUserId, $day, $period);
                break;
            case 'day':
                return $this->time_list_day($timUserId, $day, $period);
                break;
            default:
                return $this->time_list_week($timUserId, $day, $period);
                break;
        }

    }

    protected function is_session_access($id = null)
    {
        if (session()->has('userIdAccess')) {
            $model = model(AccessTimModel::class);
            $ciUserId = $this->get_ci_user_id();
            $timUserId = session()->get('userIdAccess');
            return $model->is_access($ciUserId, $timUserId) or $this->is_admin();
        } else if (isset($id)) {
            $model = model(AccessTimModel::class);
            $ciUserId = $this->get_ci_user_id();
            return $model->is_access($ciUserId, $id) or $this->is_admin();
        } else {
            return false or $this->is_admin();
        }
    }

    protected function is_admin(): bool
    {
        helper('UtilityFunctions');
        return is_admin();
    }

    protected function get_ci_user_id(): ?int
    {
        helper('UtilityFunctions');
        return get_ci_user_id();
    }

    protected function block_user()
    {
        session()->remove('userIdAccess');
        echo $this->display_view('\User\errors\403error');
        exit();
        return $this->display_view('\User\errors\403error');
    }

    protected function check_and_block_user(?int $id = null)
    {
        if (!$this->is_session_access($id)) {
            return $this->block_user();
        }
    }

    protected function create_time_links(Time $day, string $period): array
    {
        switch ($period) {
            case 'day':
                $pastDayStr = $day->subDays(1)->toDateString();
                $afterDayStr = $day->addDays(1)->toDateString();
                break;
            case 'month':
                if ($day->day > 28) {
                    # avoid skip a all month
                    $day = $day->setDay(28);
                }
                $pastDayStr = $day->subMonths(1)->toDateString();
                $afterDayStr = $day->addMonths(1)->toDateString();
                break;
            case 'week':
                $pastDayStr = $day->subDays(7)->toDateString();
                $afterDayStr = $day->addDays(7)->toDateString();
                break;
        }
        $pastLink = '../' . $pastDayStr .  '/' . $period;
        $afterLink = '../' . $afterDayStr .  '/' . $period;
        $buttons[0]['link'] = $pastLink;
        $buttons[0]['label'] =  '<';
        $buttons[1]['link'] =  $afterLink;
        $buttons[1]['label'] =   '>';
        return $buttons;
    }

    protected function create_title(array $user, Time $day,
        string $period): string
    {
        switch ($period) {
            case 'day':
                return $user['surname'] . ' ' . $user['name'] . '   '

                    . $day->toLocalizedString('eeee d MMMM yyyy');
                break;
            case 'month':
                return $user['surname'] . ' ' . $user['name'] . '   '
                    . $day->toLocalizedString('MMMM yyyy');
                break;
            case 'week':
                $weekText = $this->get_workdays_text($day, 4, true, false);
                return $user['surname'] . ' ' . $user['name'] . '   '
                    . $weekText;
                break;
        }
    }

    protected function create_buttons(string $period): array
    {
        $data = array();
        array_push($data,
            [
                'link' => '../' . Time::today()->toDateString() . '/' .
                    $period,
                'label' => ucfirst(lang('tim_lang.today')),
            ]
        );
        array_push($data, [
            'link' => 'day',
            'label' => ucfirst(lang('tim_lang.day'))
        ]);
        array_push($data, [
            'link' => 'week',
            'label' => ucfirst(lang('tim_lang.week'))
        ]);
        array_push($data, [
            'link' => 'month',
            'label' => ucfirst(lang('tim_lang.month'))
        ]);
        return $data;
    }

    protected function get_user_data(int $timUserId): array
    {
        $badgesModel = model(BadgesModel::class);
        $logsModel = model(LogsModel::class);
        $usersModel = model(UsersModel::class);
        $badgeId = $badgesModel->get_badges($timUserId);
        $data['logs'] = $logsModel->get_logs($badgeId);
        $data['user'] = $usersModel->get_users($timUserId);
        return $data;
    }

    protected function filter_log_month(array $log, Time $day)
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

    protected function is_same_month(Time $day1, Time $day2): bool
    {
        $bMonths = $day1->month === $day2->month;
        $bYears = $day1->year === $day2->year;
        return $bMonths and $bYears;
    }

    protected function is_same_week(Time $day1, Time $day2)
    {
        $bWeek = $day1->getWeekOfYear() === $day2->getWeekOfYear();
        $bYears = $day1->year === $day2->year;
        return $bWeek and $bYears;
    }

    protected function is_same_day(Time $day1, Time $day2): bool
    {
        $bDay = $day1->day === $day2->day;
        $bMonths = $day1->month === $day2->month;
        $bYears = $day1->year === $day2->year;
        return $bDay and $bMonths and $bYears;
    }

    protected function get_log_status(array $log): string
    {
        if (isset($log['date_delete'])){
            return 'deleted';
        } elseif (!isset($log['date_badge'])) {
            return 'site';
        } elseif ($log['date_badge'] != $log['date']) {
            return 'modified';
        }
        return '';
    }

    /**
     * add (return) items to array data
     */
    protected function get_day_view_day_array($timUserId, Time $date)
    {
        $model = model(LogsModel::class);
        $logs = $model->get_day_userlogs_with_deleted($timUserId, $date);
        return array_map(function ($log) {
            $data = array();
            $data['date'] = Time::parse($log['date']);
            $data['date'] = sprintf('%02d:%02d:%02d', $data['date']->hour,
                $data['date']->minute, $data['date']->second);
            $data['time'] = $log['inside'] ? lang('tim_lang.enter') :
            lang('tim_lang.exit');
            $data['url'] = $this->get_url_for_get_day_view_day_array($log);
            $data['edit_url'] = $this->get_edit_url_for_day_view($log);
            $data['status'] = $this->get_log_status($log);
            return $data;
        }, $logs);
    }

    protected function get_edit_url_for_day_view(array $log)
    {
        return '../../edit_log/' .  $log['id_log'];
    }

    protected function get_url_for_get_day_view_day_array(array $log)
    {
        return $this->is_not_tim_log($log) ?  '../../detail_modify/' .
            $log['id_log'] : null;
    }

    protected function get_time_day_by_period($timUserId, Time $day,
        string $period): string
    {
        $model = model(LogsModel::class);
        $logs = $model->get_filtered_logs($timUserId, $day, $period);
        $time = $this->get_time_array($logs);
        $time = $this->get_hours_by_seconds($time);
        return $time;
    }

    protected function get_time_day_by_period_with_asterisk($timUserId,
        Time $day, string $period): string
    {
        $model = model(LogsModel::class);
        $logs = $model->get_filtered_logs($timUserId, $day, $period);
        $timeSeconds = $this->get_time_array($logs);
        $time = $this->get_hours_by_seconds($timeSeconds);
        return $this->is_not_tim_logs($logs) ? $time . '✱' : $time;
    }

    public function access_user_list()
    {
        $ciUserId = $this->get_ci_user_id();
        $model = model(AccessTimModel::class);
        $data['items'] = $model->get_access_users_with_info($ciUserId);
        $data['columns'] = [
            'name' => ucfirst(lang('tim_lang.name')),
            'surname' => ucfirst(lang('tim_lang.surname')),
        ];
        $data['primary_key_field']  = 'id_user';
        $data['url_detail'] = 'PersoLogs/access_user/';
        return $this->display_view('Common\Views\items_list', $data);
        
    }

    public function access_user($timUserId)
    {
        $model = model(AccessTimModel::class);
        $ciUserId = $this->get_ci_user_id();
        if ($model->is_access($ciUserId, $timUserId)) {
            session()->set('userIdAccess', $timUserId);
            $today = Time::today()->toDateString();
            return redirect()->to( current_url() .  '/../../perso_time/'
                . $today . '/month');
        }
    }

    public function get_labels_for_array_detail(): array
    {
        $labels = array();
        $labels['date'] = ucfirst(lang('tim_lang.hour'));
        $labels['id_user'] = ucfirst(lang('tim_lang.username'));
        $labels['inside'] = ucfirst(lang('tim_lang.enter'));
        $labels['id_ci_user'] = ucfirst(lang('tim_lang.ciUsername'));
        $labels['date_modif'] = ucfirst(lang('tim_lang.modifyDate'));
        $labels['id_badge'] = ucfirst(lang('tim_lang.badgeId'));
        $labels['id_log'] = ucfirst(lang('tim_lang.idLog'));
        $labels['date_badge'] = ucfirst(lang('tim_lang.badgeDate'));
        $labels['date_delete'] = ucfirst(lang('tim_lang.deleteDate'));
        return $labels;
    }
    
    public function detail_modify($logId)
    {
        $data['items'] = $this->get_items_array_detail_modify($logId);
        $data['labels'] = $this->get_labels_for_array_detail();
        $data['buttons'] = array();
        $button = array();

        #$agent = $this->request->getUserAgent();
        #$button['link'] = $agent->getReferrer();
        $model = model(LogsModel::class);
        $log = $model->find($logId);
        $button['link'] = '../' . $this->redirect_log($log);

        #$button['link'] = $this->session->get('_ci_previous_url');
        $button['label'] = ucfirst(lang('tim_lang.back'));
        array_push($data['buttons'], $button);
        $data['title'] = lang('tim_lang.details');
        return $this->display_view(['Timbreuse\Views\menu',
                'Timbreuse\Views\logs\detail_modify_log',], $data);
    }

    protected function get_items_array_detail_modify($logId)
    {
        # to do rename fakeLogFakeId
        $model = model(LogsModel::class);
        $items = $model->find($logId);
        $this->check_and_block_user($items['id_user']);
        $items['inside'] = boolval($items['inside']) ? lang('tim_lang.yes') :
            lang('tim_lang.no');
        unset($items['id_fake_log']);
        $items['id_user'] = $this->get_username($items['id_user']);
        $items['id_ci_user'] = $this->get_site_username($items['id_ci_user']);
        return $items;
    }
    
    /**
     * duplicate from Users.php
     */
    protected function get_username($timUserId)
    {
        $model = model(UsersModel::class);
        $userName = $model->select('name, surname')->find($timUserId);
        $userName = $userName['name'].' '.$userName['surname'];
        return $userName;
    }

    protected function get_site_username($ciUserId)
    {
        $model = model(UsersModel::class);
        $userName = $model->select('user.username')->from('user')
            ->where('user.id', $ciUserId)->findAll();
        if (isset($userName[0]['username'])) {
            return $userName[0]['username'];
        }
            return '';
    }


    public function create_modify_log()
    {
        $this->check_and_block_user($this->request->getPost('userId'));
        $model = model(LogsModel::class);
        if ($this->request->getMethod() === 'post' && $this->validate([
            'time' => 'required|regex_match[^([0-1][0-9]|2[0-3]):[0-5][0-9]:'.
                '[0-5][0-9]$]',
            'inside'  => 'required|regex_match[^(true|false)$]',
            'date' => 'required|valid_date',
            'userId' => 'required|integer',
        ])) {
            $request_array = array();
            $request_array['id_ci_user'] = $this->get_ci_user_id();
            $request_array['id_user'] = $this->request->getPost('userId');
            $request_array['date'] = $this->request->getPost('date') . ' ' . 
                $this->request->getPost('time');
            $request_array['inside'] = $this->request->getPost('inside') === 
                'true';
            $model->save($request_array);
            return redirect()->back();
        } else {
            return redirect()->back()->withInput();
        }
    }

    public function restore_log($logId)
    {
        $data['id'] = $logId;
        $data['text'] = lang('tim_lang.confirmRestore');

        # do not put a "/" in the end because the remote serveur change 
        # post in get if put "/"
        $data['link'] = '../approve_restore_log';

        $data['cancel_link'] = '../edit_log/' . $logId;
        $data['label_button'] = ucfirst(lang('tim_lang.restore')); 
        return $this->display_view('Timbreuse\Views\logs\approve_restore_log',
            $data);
    }

    public function approve_restore_log()
    {
        if ($this->request->getMethod() !== 'post') {
            return $this->display_view('\User\errors\403error');
        } 
        $logId = $this->request->getPost('id');
        $model = model(LogsModel::class);
        $log = $model->withDeleted()->find($logId);
        $this->check_and_block_user($log['id_user']);
        $model->onlyDeleted()->update($logId, ['date_delete' => null]);
        return redirect()->to(current_url() . '/../' . 
            $this->redirect_log($log));
    }

    protected function replace_time_in_date($date, $time)
    {
        $oldDate = Time::parse($date);
        $time = Time::parse($time);
        return Time::create($oldDate->year, $oldDate->month,
            $oldDate->day, $time->hour, $time->minute, $time->second);
    }


    public function delete_modify_log($logId)
    {
        # maybe rename  the methode name
        $model = model(LogsModel::class);
        $data['userId'] = $model->find($logId)['id_user'];
        $data['id'] = $logId;
        $data['text'] = lang('tim_lang.confirmDelete');
        $data['link'] = '../confirm_delete_modify_log';
        $data['cancel_link'] = '../edit_log/' . $logId;
        $data['label_button'] = ucfirst(lang('tim_lang.delete')); 
        $data['ciUserId'] = $this->get_ci_user_id();
        $data['title'] = lang('tim_lang.delete');
        return $this->display_view('Timbreuse\Views\logs\confirm_delete',
            $data);
    }

    public function confirm_delete_modify_log()
    {
        # to do rename fakeLog
        # to do rename the method name, now it is all log can be (soft) delete
        if ($this->request->getMethod() !== 'post') {
            return $this->display_view('\User\errors\403error');
        }     
        $id = $this->request->getPost('id');
        $model = model(LogsModel::class);
        $fakeLog = $model->find($id);
        $this->check_and_block_user($fakeLog['id_user']);
        $model->delete($id);
        $this->redirect_log($fakeLog);
        return redirect()->to(current_url() . '/../' . 
            $this->redirect_log($fakeLog));
    }

    protected function redirect_log(array $log) : string
    {
        $link = explode(' ', $log['date'])[0];
        $link .= '/day';
        $link = self::RETURN_METHOD_NAME . '/' . $link;
        return $link;
    }

    public function edit_log(int $logId)
    {
        if ($this->request->getMethod() === 'post' and $this->validate([
            'time' => 'required|regex_match[^([0-1][0-9]|2[0-3]):[0-5][0-9]:'.
                '[0-5][0-9]$]',
            'inside'  => 'required|regex_match[^(true|false)$]',
            'logId' => 'required|integer',
        ])) {
            return $this->post_edit_log();
        }
        $model = model(LogsModel::class);
        $data = $model->withDeleted()->find($logId);
        $this->check_and_block_user($data['id_user']);
        $datetime = Time::parse($data['date']);
        $data['time'] = $datetime->format('H:i:s');
        $data['cancel_link'] = '../' . $this->redirect_log($data);
        $data['delete_link'] = '../delete_modify_log/'.$logId;
        $data['restore_link'] = '../restore_log/' .$logId;
        $data['update_link'] = "./$logId";
        $data['title'] = lang('tim_lang.recordModification');
        return $this->display_view('Timbreuse\Views\logs\edit_log', $data);
    }

    protected function post_edit_log()
    {
        $model = model(LogsModel::class);
        $logId = $this->request->getPost('logId');
        $log = $model->find($logId);
        $this->check_and_block_user($log['id_user']);
        $newDate = $this->replace_time_in_date($log['date'],
            $this->request->getPost('time'));
        $inside = $this->request->getPost('inside');
        $inside = $inside === 'true';
        $userCiId = $this->get_ci_user_id();
        $model->update($logId, [ 'date' => $newDate, 'inside' => $inside,
                'id_ci_user' => $userCiId, ]);
        return redirect()->to(current_url() . '/../../' . 
            $this->redirect_log($log));
    }

    protected function get_balance_day(int $timUserId, string $date): ?string
    {
        $planningModel = model(PlanningsModel::class);
        $planningTime = $planningModel
                ->get_planning_time_day($date, $timUserId);
        if (is_null($planningTime)) {
            return null;
        }
        $date = Time::parse($date);
        $logsTime = $this->get_time_day_by_period($timUserId, $date, 'day');
        $times = $this->to_seconds_for_planning_day($planningTime[0],
                $planningTime[1], $logsTime);
        $balanceSeconds = $this->get_balance_seconds($times['dueTime'],
                $times['offeredTime'], $times['logsTime']);
        $balance = $this->get_hours_by_seconds($balanceSeconds);
        $balance = $this->get_string_with_plus($balance);
        return $balance;
    }

    protected function get_balance_week(int $timUserId, string $date): string
    {
        $date = Time::parse($date);
        $monday = $this->get_last_monday($date);
        return $this->get_balance_period($monday, 5, $timUserId);
    }

    protected function get_offered_time_show_week(int $timUserId,
        string $date): string
    {
        $date = Time::parse($date);
        $monday = $this->get_last_monday($date);
        return $this->get_offered_time_show_period($monday, 5, $timUserId);
    }

    /**
        * period is day, week, month
     */
    protected function get_offered_time_show_by_period(int $timUserId,
        string $date, string $period): string
    {
        switch ($period) {
        case 'day':
            return $this->get_offered_time_show_day($timUserId, $date);
            break;
        case 'week':
            return $this->get_offered_time_show_week($timUserId, $date);
            break;
        case 'month':
            return $this->get_offered_time_show_month($timUserId, $date);
            break;
        }
    }

    /**
        * period is day, week, month
     */
    protected function get_balance_by_period(int $timUserId, string $date,
        string $period): string
    {
        switch ($period) {
        case 'day':
            return $this->get_balance_day($timUserId, $date);
            break;
        case 'week':
            return $this->get_balance_week($timUserId, $date);
            break;
        case 'month':
            return $this->get_balance_month($timUserId, $date);
            break;
        }
    }

    protected function get_balance_by_period_asterisk(int $timUserId,
        string $date, string $period): string
    {
        $model = model(LogsModel::class);
        $balance = $this->get_balance_by_period($timUserId, $date, $period);
        $time = Time::parse($date);
        $logs = $model->get_filtered_logs($timUserId, $time, $period);
        return $this->is_not_tim_logs($logs) ?  $balance . '✱' : $balance;
    }

    /**
        * period is time beeteen two fixed points in time
    */
    protected function get_balance_period(string $firstDay,
            int $numberOfDay, int $timUserId): string
    {
        $balanceText = $this->get_time_period($firstDay, $numberOfDay,
            $timUserId, 'get_balance_day');
        return $this->get_string_with_plus($balanceText);
    }


    # not use
    protected function get_balance_period_from_monday(string $firstDay,
        string $lastDay, int $timUserId): string
    {
        $firstDay = Time::parse($firstDay);
        $lastDay = Time::parse($lastDay);
        $numberOfDay = $lastDay->difference($firstDay)->days;
        $weeks = range(0, $numberOfDay, 7);
        $weeksDate = array_map(array($firstDay, 'addDays'), $weeks);
        $daysDateText = array_map(function($OneDayOfWeek) {
            return $OneDayOfWeek->toDateString();
        }, $weeksDate);
        $balanceWeek = array_map(function($week) use ($timUserId) {
            return $this->get_balance_week($timUserId, $week);
        }, $daysDateText);
        $balanceWeekSeconds = array_map(array($this, 'toSeconds'),
                $balanceWeek); 
        $balanceSeconds = array_reduce($balanceWeekSeconds,
                function($carry, $day) {
                    return $carry + $day;
                });
        $balanceText = $this->get_hours_by_seconds($balanceSeconds);
        return $balanceText;
    }


    protected function get_balance_month(int $timUserId, string $date): string
    {
        $date = Time::parse($date);
        $firstDay = Time::create($date->year, $date->month, 1);
        $lastDay = $firstDay->addMonths(1)->subDays(1);
        $numberOfDay = $lastDay->day;
        return $this->get_balance_period($firstDay, $numberOfDay, $timUserId);
    }

    protected function get_offered_time_show_month(int $timUserId,
        string $date): string
    {
        $date = Time::parse($date);
        $firstDay = Time::create($date->year, $date->month, 1);
        $lastDay = $firstDay->addMonths(1)->subDays(1);
        $numberOfDay = $lastDay->day;
        return $this->get_offered_time_show_period($firstDay, $numberOfDay,
            $timUserId);
    }


    protected function get_string_with_plus($text): string
    {
        if ($text[0] === '-') {
            return $text;
        }
        return "+$text";
    }

    protected function get_balance_seconds(int $dueTime, int $offeredTime,
            int $logsTime): int
    {
        if ($dueTime === $offeredTime) {
            return 0;
        }
        if ($logsTime === 0) {
            return -$dueTime;
        }
        return -$dueTime + $logsTime + $offeredTime;
    }

    protected function to_seconds_for_planning_day(string $dueTime,
            string $offeredTime, string $logsTime): array
    {
        $data['dueTime'] = $this->toSeconds($dueTime);
        $data['offeredTime'] = $this->toSeconds($offeredTime);
        $data['logsTime'] = $this->toSeconds($logsTime);
        return $data;
    }

    protected function toSeconds(string $time): int
    {
        helper('UtilityFunctions');
        return toSeconds($time);
    }

    protected function parseDuration(string $duration): array
    {
        helper('UtilityFunctions');
        return parseDuration($duration);
    }


    private function test1()
    {
        $model = model(LogsModel::class);
        $date = Time::parse('2022-05-20');
        var_dump($model->get_filtered_logs(92, $date, 'week'));
    }

    private function test2()
    {
        $time = Time::parse('1970-01-01');
        $time1 = Time::parse('1970-01-13');
        $diff = $time1->difference($time);
        var_dump($diff->seconds);
    }
    protected function test3()
    {
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
    private function test4()
    {
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

    private function test5()
    {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $halfDay = 'morning';
        $halfDay = 'afternoon';
        $userId = '92';
        $model = model(LogsModel::class);
        var_dump($model->get_logs_by_period($userId, $day, $halfDay));
    }

    private function test6()
    {
        $day = '2022-05-18';
        $day = Time::parse($day);
        var_dump($this->get_last_monday($day));
    }

    private function test7()
    {
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

    private function test8()
    {
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

    private function test9()
    {
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

    private function test10()
    {
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

    private function test11()
    {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $model = model(LogsModel::class);
        var_dump($model->get_week_time_table('92', $day));
    }

    private function test12()
    {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $model = model(LogsModel::class);
        var_dump($model->get_day_week_array('92', $day));
    }

    private function test13()
    {
        $day = '2022-05-18';
        $day = Time::parse($day);
        $model = model(LogsModel::class);
        var_dump($model->get_month_week_array('92', $day));
    }

    private function test14()
    {
        $day = '2022-05-18 12:11:22';
        $day = Time::parse($day);
        var_dump($this->get_day_view_day_array('92', $day));
    }

    private function test15()
    {
        $day = '2022-05-18 12:11:22';
        $day = Time::parse($day);
        var_dump($this->get_time_day_by_period('92', $day, 'week'));
    }

    private function test16()
    {
        $model = model(AccessTimModel::class);
        var_dump($model->is_access(8, 92));
    }

    private function test17()
    {
        $model = model(LogsFakeLogsModel::class);
        $day = Time::parse('2022-05-31');
        var_dump($model->get_border_log_by_period(92, $day, 'morning', true));
    }

    private function test18()
    {
        $data['items'] = array();
        $data['items'][0]['label'] = 'test';
        $data['items'][0]['data'] = 'data';
        $data['items'][1]['label'] = 'test2';
        $data['items'][1]['data'] = 'data2';
        $data['items'][2]['label'] = 'test3';
        $data['items'][2]['data'] = 'data3';
        $data['list_title'] = 'test';
        return $this->display_view('Timbreuse\Views\logs\modify_log', $data);
    }

    private function test19()
    {
        $day = Time::parse('2022-05-30');
        var_dump($this->get_day_view_day_array(92, $day));
        var_dump($this->get_day_view_day_array(92, $day, true));
    }

    private function test20()
    {
        $model = model(LogsModel::class);
        $model->delete(362);
    }

    private function test21()
    {
        $model = model(LogsModel::class);
        var_dump($model->select('id_log')->findAll());
    }

    private function test22()
    {
        $data['date'] = Time::parse('2022-05-30');
        $data['time'] = '18:33';
        $data['userId'] = 33;
        $data['inside'] = 'false';

        return $this->display_view('Timbreuse\Views\logs\edit_log', $data);
    }

    private function test23()
    {
        $model = model(AccessTimModel::class);
        return $model->have_one_access(10);
    }

    private function test24()
    {
        $model = model(AccessTimModel::class);
        var_dump($model->get_tim_user_id(10));
        return $model->get_tim_user_id(10);
    }

    public function test25()
    {
        $model = model(PlanningsModel::class);
        return $model->get_due_time_period('2023-05-29', 7, 92);
    }

    public function test26()
    {
        $model = model(PlanningsModel::class);
        return $model->get_due_time_week(92, '2023-05-29');
    }

    public function test27()
    {
        $model = model(PlanningsModel::class);
        $date = '2023-04-12';
        $timUserId = 92;
        var_dump(array($model->get_due_time_month($timUserId, $date),
            $model->get_offered_time_month($timUserId, $date)));
    }

    

}
