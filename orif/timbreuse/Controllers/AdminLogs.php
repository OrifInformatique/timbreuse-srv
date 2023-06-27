<?php

namespace Timbreuse\Controllers;

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

    public function initController( RequestInterface $request,
        ResponseInterface $response, LoggerInterface $logger)
    {
        $this->access_level = config( '\User\Config\UserConfig'
        )->access_lvl_admin;
        get_parent_class(parent::class)::initController($request, $response,
                $logger);
        $this->session = \Config\Services::session();
    }


    public function time_list($userId, $day = null, $period = null)
    {
        if (($day === null) or ($day == 'all')) {
            return redirect()->to(
                current_url() . '/../' . 
                $userId . '/' . Time::today()->toDateString() . '/day'
            );
        }
        if ($period === null) {
            return redirect()->to(current_url() . '/../' . $day . '/day');
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

    protected function get_edit_url_for_day_view(array $log): string
    {
            return '../../../edit_log/' .  $log['id_log'];
    }

    protected function get_url_for_get_day_view_day_array(array $log)
    {
            return $this->is_not_tim_log($log) ?  '../../../detail_modify/' .
                $log['id_log'] : null;
    }

    protected function redirect_log(array $log) : string
    {
        $link = explode(' ', $log['date'])[0];
        $link .= '/day';
        $link = self::RETURN_METHOD_NAME . '/' . $log['id_user'] . '/' . $link;
        return $link;
    }
}
