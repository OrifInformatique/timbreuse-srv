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
        return $this->join_planning_and_user_planning()
             ->join('user_sync', 'user_sync.id_user = user_planning.id_user');
    }

    public function join_ci_user_and_tim_user()
    {
        return $this->join_tim_user_and_planning()->join('access_tim_user',
            'access_tim_user.id_user = user_planning.id_user')
            ->join('ci_user', 'ci_user.id = access_tim_user.id_ci_user');
    }

    public function join_planning_and_user_planning()
    {
        return $this->join('user_planning',
            'user_planning.id_planning = planning.id_planning');
    }

    public function get_planning_hours_minutes(int $planningId): array
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
        $dates = $this->select('date_begin, date_end')
            ->join_tim_user_and_planning()
            ->find($planningId);
        return $this->set_null_if_not_set_date($dates);
    }

    private function set_null_if_not_set_date(?array $dates): array
    {
        $dates['date_begin'] = $dates['date_begin'] ?? null;
        $dates['date_end'] = $dates['date_end'] ?? null;
        return $dates;
    }


    public function parse_hours_minutes(string $time)
    {

        $time = Time::parse($time);
        $data['hour'] = $time->hour;
        $data['minute'] = $time->minute;
        return $data;
    }

    public function is_access_tim_user(int $timUserId, int $planningId): bool
    {
        return !is_null($this->select('planning.id_planning')
            ->join_tim_user_and_planning()
            ->where('user_sync.id_user = ', $timUserId)->find($planningId));
    }

    public function is_access_ci_user(int $ciUserId, int $planningId): bool
    {
        return !is_null($this->select('planning.id_planning')
            ->join_ci_user_and_tim_user()
            ->where('ci_user.id = ', $ciUserId)->find($planningId));
    }

    public function get_unavailble_dates(int $timUserId): array
    {
        $dates = $this->select('date_bein', 'date_end')
            ->from('user_planning')
            ->where('user_sync.id_user = ', $timUserId)
            ->findAll();
        return array_map(array($this, 'set_null_if_not_set_date'), $dates);
    }

    //public function check_availble_date($timUserId, $date_begin_end

    public function get_user_planning_id(int $planningId): ?int
    {
        return $this->select('id_user_planning')
            ->join_planning_and_user_planning()
            ->find($planningId)['id_user_planning'] ?? null;
    }

    public function update_planning_times_and_dates(int $planningId,
        array $times, array $dates)
    {
        $this->db->transStart();
        $this->update($planningId, $times);
        $this->db->table('user_planning')
            ->where('id_user_planning = ',
                $this->get_user_planning_id($planningId))
            ->update($dates);
        $this->db->transComplete();
    }

}
