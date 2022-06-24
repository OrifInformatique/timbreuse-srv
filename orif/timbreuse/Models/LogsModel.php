<?php
namespace Timbreuse\Models;

use CodeIgniter\BaseModel;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class LogsModel extends Model {
    protected $table = 'log';

    public function get_logs($idBadge=null) {
        if ($idBadge === null) {
            return $this->findAll();
        } else {
            return $this->where('id_badge', $idBadge)
                        ->findAll();
        }
    }

    public function get_id_badge($userId) {
        $modelBadge = model(BadgesModel::class);
        $this->whereIn('id_badge', function () use ($modelBadge, $userId) {
            return $modelBadge->select('id_badge')->where('id_user', $userId);
        });
    }

    public function get_filtered_logs($userId, $date, $period): array {
        $this->get_id_badge($userId);
        if ($period == 'day') {
            $this->where('DAY(date)', $date->getDay());
        }
        if (($period == 'day') or ($period =='month')) {
            $this->where('MONTH(date)', $date->getMonth());
        }
        if ($period == 'week') {
            $this->where('WEEKOFYEAR(date)', $date->getWeekOfYear());
        }
        if ($period != 'all') {
            $this->where('YEAR(date)', $date->getYear());
        }
        return $this->get()->getResultArray();
    }
    public function get_border_interval($date, $halfDay): array {
        switch ($halfDay) {
            case 'morning':
                $border['startTime'] = $date->setHour(7)->setMinute(0)->setSecond(0);
                $border['endTime'] = $date->setHour(12)->setMinute(30)->setSecond(0);
                break;
            case 'afternoon':
                $border['startTime'] = $date->setHour(12)->setMinute(30)->setSecond(0);
                $border['endTime'] = $date->setHour(17)->setMinute(45)->setSecond(0);
                break;
        }
        return $border;
    }

    public function get_logs_by_period($userId, $date, $halfDay): array {
        $this->get_id_badge($userId);
        $border = $this->get_border_interval($date, $halfDay);
        $this->where('date >', $border['startTime']);
        $this->where('date <', $border['endTime']);
        return $this->get()->getResultArray();
    }

    /**
     * return work time for a array of logs
     */
    public function get_time_array($logs): int
    {
        $date_in = null;
        $seconds = array_reduce($logs, function ($carry, $log) use (&$date_in) {
            if (boolval($log['inside'])) {
                if (($date_in === null) or !($this->is_same_day(Time::parse($date_in), Time::parse($log['date'])))) {
                    $date_in = $log['date'];
                }
            } elseif ($date_in !== null) {
                if ($this->is_same_day(Time::parse($date_in), Time::parse($log['date']))) {
                    $carry += Time::parse($log['date'])->difference($date_in)->seconds;
                    $date_in = null;
                } 
            }
            return $carry;
        });
        if ($seconds === null) {
            $seconds = 0;
        }
        return $seconds;
    }

    public function get_border_log_by_period($userId, $date, $halfDay, $last = false): array
    {
        $this->get_id_badge($userId);
        $border = $this->get_border_interval($date, $halfDay);
        $this->where('date >', $border['startTime']);
        $this->where('date <', $border['endTime']);
        if ($last) {
            $this->where('inside =', 0);
            $this->orderBy('date', 'DESC');
        } else {
            $this->where('inside =', 1);
        }
        $this->limit(1);
        try {
            return $this->get()->getResultArray()[0];
        } catch (\Exception $e) {
            return array();
        }

    }

    public function get_day_time_table($userId, $date, $halfDay): array {
        $logs = $this->get_logs_by_period($userId, $date, $halfDay);
        $data['time'] = $this->get_time_array($logs);
        $data['time'] = $this->get_hours_by_seconds($data['time']);
        try {
            $data['first'] = $this->get_border_log_by_period($userId, $date, $halfDay)['date'];
            $data['first'] = Time::parse($data['first'])->toTimeString();
        } catch (\Exception $e) {
            $data['first'] = '';
        }
        try {
            $data['last'] = $this->get_border_log_by_period($userId, $date, $halfDay, true)['date'];
            $data['last'] = Time::parse($data['last'])->toTimeString();
        } catch (\Exception $e) {
            $data['last'] = '';
        }
        return $data;
    }

    public function get_upper_day_time_table($userId, $date): array {
        $data['dayNb'] = $date->day;
        $data['morning'] = $this->get_day_time_table($userId, $date, 'morning');
        $data['afternoon'] = $this->get_day_time_table($userId, $date, 'afternoon');
        return $data;
    }

    public function get_week_time_table($userId, $date): array {
        $monday = $this->get_last_monday($date);
        $weekdays = [
            'monday',
            'tuesday',
            'wednesday',
            'thurday',
            'friday',
        ];
        $data = array();
        foreach ($weekdays as $i => $weekday) {
            $data[$weekday] = $this->get_upper_day_time_table($userId, $monday->addDays($i));
        }
        return $data;
        
        /*
        $i = 0;
        array_map(function($day) use ($userId, $monday, &$i) {
            return $this->get_upper_day_time_table($userId, $monday->addDays($i));
        },
        $weekdays);
        */
    }

    protected function get_last_monday(Time $day) {
        return $day->subDays($day->dayOfWeek - 1);
    }

    public function get_hours_by_seconds($seconds) {
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;

        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;
        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);

    }

    /**
     * ! method duplicate in persologs controller 
     */
    public function is_same_day(Time $day1, Time $day2): bool
    {
        $bDay = $day1->getDay() === $day2->getDay();
        $bMonths = $day1->getMonth() === $day2->getMonth();
        $bYears = $day1->getYear() === $day2->getYear();
        return $bDay and $bMonths and $bYears;
    }


}