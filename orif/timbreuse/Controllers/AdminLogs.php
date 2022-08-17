<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Timbreuse\Models\BadgesModel;
use Timbreuse\Models\LogsModel;
use Timbreuse\Models\UsersModel;
use Timbreuse\Controllers\PersoLogs;
use CodeIgniter\I18n\Time;

class AdminLogs extends PersoLogs
{
    const RETURN_METHOD_NAME = 'time_list';

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        $this->access_level = config(
            '\User\Config\UserConfig'
        )->access_lvl_admin;
        # parent::initController($request, $response, $logger);
        # otherwise is take acces of PersoLogs
        Basecontroller::initController($request, $response, $logger);
        $this->session = \Config\Services::session();
    }

    public function perso_logs_list($userId, $day = null, $period = null)
    {
        if (($day === null) or ($day == 'all')) {
            return redirect()->to(
                $userId . '/' . Time::today()->toDateString() . '/all'
            );
        }
        if ($period === null) {
            return redirect()->to($day . '/day');
        }
        var_dump($this->access_level);
        
        $usersModel = model(UsersModel::class);
        $user = $usersModel->get_users($userId);

        $data['title'] = "Welcome";

        # Display a test of the generic "items_list" view (defined in common module)
        $data['columns'] = [
            'date' => 'Date',
            'id_badge' => 'Numéro du badge',
            'inside' => 'Entrée'
        ];
        $day = Time::parse($day);
        $data['period'] = $period;
        $logsModel = model(LogsModel::class);
        $data['items'] = $logsModel->get_filtered_logs($userId, $day, $period);
        $sumTime = [
            'date' => 'Total temps',
            'id_badge' => $this->get_hours_by_seconds(
                $this->get_time_array($data['items'])
            ),
            'inside' => ''
        ];
        array_push($data['items'], $sumTime);


        $data['list_title'] = $this->create_title($user, $day, $period);
        $data['buttons'] = $this->create_buttons($period);
        if ($period != 'all') {
            $data['buttons'] = array_merge(
                $this->create_time_links($day, $period),
                $data['buttons']
            );
            $data['date'] = $day->toDateString();
        }
        $this->display_view(
            [
                'Timbreuse\Views\period_menu',
                'Timbreuse\Views\date', 'Common\Views\items_list'
            ],
            $data
        );
    }

    public function time_list($userId, $day = null, $period = null)
    {
        if (($day === null) or ($day == 'all')) {
            return redirect()->to(
                $userId . '/' . Time::today()->toDateString() . '/month'
            );
        }
        if ($period === null) {
            return redirect()->to($day . '/day');
        }

        switch ($period) {
            case 'week':
                return $this->time_list_week($userId, $day, $period);
                break;
            case 'month':
                return $this->time_list_month($userId, $day, $period);
                break;
            case 'day':
                return $this->time_list_day($userId, $day, $period);
                break;
            default:
                return $this->time_list_week($userId, $day, $period);
                break;
        }
    }

    protected function get_url_for_get_day_view_day_array($fakeLogId){
            return isset($fakeLogId) ?  '../../../detail_modify/' .
                $fakeLogId : null;
    }

    protected function redirect_log(array $log) {
        $link = explode(' ', $log['date'])[0];
        $link .= '/day';
        $link = self::RETURN_METHOD_NAME . '/' . $log['id_user'] . '/' . $link;
        return $link;
    }
}
