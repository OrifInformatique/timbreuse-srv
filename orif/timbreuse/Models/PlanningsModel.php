<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class UserModel extends Model 
{
    protected $table = 'planning';
    protected $primaryKey ='id_planning';
    protected $allowedFields = ['due_time_monday', 'offered_time_monday',
        'due_time_tuesday', 'offered_time_tuesday', 'due_time_wednesday',
        'offered_time_wednesday', 'due_time_thursday',
        'offered_time_thursday', 'due_time_friday', 'offered_time_friday'];

    protected $useAutoIncrement = true;


    public function get_plannings_user($timUserId)
    {
        return $this->select_time()->join_tim_user_and_planning()
             ->where('user_sync.id_user = ', $timUserId)->findAll();
    }

    public function get_planning_user_hours_minutes($timUserId)
    {
    }

    public function get_planning(int $planningId)
    {
            return $this->select_time()
             -> find($planningId);
    }

    public function select_time()
    {
        return $this->select('due_time_monday, offered_time_monday, '
        . 'due_time_tuesday, offered_time_tuesday, due_time_wednesday, '
        . 'offered_time_wednesday, due_time_thursday, '
        . 'offered_time_thursday, due_time_friday, offered_time_friday');

    }

    public function join_tim_user_and_planning()
    {
        return $this->join('user_planning',
            'user_planning.id_planning = planning.id_planning')
             ->join('user_sync', 'user_sync.id_user = user_planning.id_user');
    }

    public function join_ci_user_and_tim_user()
    {
        return $this->join_tim_user_and_planning()->join('access_tim_user',
            'access_tim_user.id_user = user_planning.id_user')
            ->join('ci_user', 'ci_user.id = access_tim_user.id_ci_user');
    }

    public function get_planning_hours_minutes(int $planningId)
    {
        $planning = $this->get_planning($planningId);
        return array_map(function($time) {
            return $this->parse_hours_minutes($time);
        }, $planning);
    }

    public function get_tim_user_names(int $planningId): string
    {
        $names =  $this->select('name, surname')
            ->join_tim_user_and_planning()
            ->find($planningId);
        if (!isset($names['surname'], $names['name'])) {
            return '';
        }
        return "$names[surname] $names[name]";
    }

    public function get_begin_end_dates(int $planningId): array
    {
        $dates = $this->select('date_begin', 'date_end')
            ->join_tim_user_and_planning()
            ->find($planningId);
        var_dump($dates);
        if (!isset($dates['date_begin'])) {
            $dates['date_begin'] = null;
        }
        if (!isset($dates['date_end'])) {
            $dates['date_end'] = null;
        }
        return $dates;
    }


    public function parse_hours_minutes(string $time)
    {

        $time = Time::parse($time);
        $data['hour'] = $time->hour;
        $data['minute'] = $time->minute;
        return $data;
    }

    public function is_access_tim_user($timUserId, $planningId): bool
    {
        return !is_null($this->select('planning.id_planning')
            ->join_tim_user_and_planning()
            ->where('user_sync.id_user = ', $timUserId)->find($planningId));
    }

    public function is_access_ci_user($ciUserId, $planningId): bool
    {
        return !is_null($this->select('planning.id_planning')
            ->join_ci_user_and_tim_user()
            ->where('ci_user.id = ', $ciUserId)->find($planningId));
    }

}
