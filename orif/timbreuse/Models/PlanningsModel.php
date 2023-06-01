<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class PlanningModel extends Model 
{
    protected $table = 'planning';
    protected $primaryKey ='id_planning';
    protected $allowedFields = ['due_time_monday', 'offered_time_monday',
        'due_time_tuesday', 'offered_time_tuesday', 'due_time_wednesday',
        'offered_time_wednesday', 'due_time_thursday',
        'offered_time_thursday', 'due_time_friday', 'offered_time_friday',
        'date_delete'];

    protected $useAutoIncrement = true;
    protected $useSoftDeletes = true;
    protected $deletedField  = 'date_delete';
    protected $dateFormat = 'datetime';


    public function get_date_and_id(int $timUserId, bool $withDeleted): array
    {
        $this->select_date_and_id_planning()
                ->join_tim_user_and_planning()
                ->where('user_sync.id_user = ', $timUserId)
                ->orderBy('date_begin', 'DESC');
        if ($withDeleted) {
            $this->withDeleted();
        }
        return $this->findAll();
    }

    public function get_due_plannings_user(int $timUserId): array
    {
        return $this->select_due_time()->join_tim_user_and_planning()
             ->where('user_sync.id_user = ', $timUserId)->findAll();
    }

    public function get_due_plannings(int $planningId): array
    {
        return $this->select_due_time()
                    ->withDeleted()
                    ->find($planningId);
    }

    public function get_plannings_user(int $timUserId): array
    {
        return $this->select_time()->join_tim_user_and_planning()
             ->where('user_sync.id_user = ', $timUserId)->findAll();
    }

    public function get_planning(int $planningId): array
    {
        return $this->select_time()
                ->withDeleted()
                ->find($planningId);
    }

    public function select_date_and_id_planning(): PlanningModel 
    {
        return $this->select(
                'date_begin, date_end, user_planning.id_planning');
    }

    public function select_due_time(): PlanningModel 
    {
        return $this->select('due_time_monday, due_time_tuesday, '
        . 'due_time_wednesday, due_time_thursday, due_time_friday');

    }

    public function select_time(): PlanningModel 
    {
        return $this->select('due_time_monday, offered_time_monday, '
        . 'due_time_tuesday, offered_time_tuesday, due_time_wednesday, '
        . 'offered_time_wednesday, due_time_thursday, '
        . 'offered_time_thursday, due_time_friday, offered_time_friday');

    }

    public function join_tim_user_and_planning(): PlanningModel 
    {
        return $this->join_planning_and_user_planning()
             ->join('user_sync', 'user_sync.id_user = user_planning.id_user');
    }

    public function join_ci_user_and_tim_user(): PlanningModel 
    {
        return $this->join_tim_user_and_planning()->join('access_tim_user',
            'access_tim_user.id_user = user_planning.id_user')
            ->join('ci_user', 'ci_user.id = access_tim_user.id_ci_user');
    }

    public function join_planning_and_user_planning(): PlanningModel 
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


    public function get_begin_end_dates(int $planningId,
            bool $withDeleted=false): array
    {
        $this->select('date_begin, date_end')
            ->join_tim_user_and_planning();
        if ($withDeleted) {
            $this->withDeleted();
        }
        $dates = $this->find($planningId);
        return $this->set_null_if_not_set_date($dates);
    }

    private function set_null_if_not_set_date(?array $dates): array
    {
        $dates['date_begin'] = $dates['date_begin'] ?? null;
        $dates['date_end'] = $dates['date_end'] ?? null;
        return $dates;
    }


    public function parse_hours_minutes(string $time): array
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
            ->withDeleted()
            ->where('user_sync.id_user = ', $timUserId)->find($planningId));
    }

    public function is_access_ci_user(int $ciUserId, int $planningId): bool
    {
        return !is_null($this->select('planning.id_planning')
            ->join_ci_user_and_tim_user()
            ->where('ci_user.id = ', $ciUserId)->find($planningId));
    }

    public function get_unavailable_period(int $timUserId,
        ?int $planningId=null): array
    {
        $this->select('date_begin, date_end')
            ->join_planning_and_user_planning()
            ->where('id_user = ', $timUserId);
        if ($planningId) {
            $this->where('planning.id_planning <> ', $planningId);
        }
        $dates = $this->findAll();
        return array_map(array($this, 'set_null_if_not_set_date'), $dates);
    }

    public function is_date_in_period_list(string $date,
        array $periods): bool
    {
        foreach ($periods as $dates) {
            $dateBegin = time::parse($dates['date_begin']);
            $dateEnd = time::parse($dates['date_end'] ?? '9999-12-31');
            $date = time::parse($date);
            if (!($date->isBefore($dateBegin) or $date->isAfter($dateEnd))) {
                return true;
            }
        }
        return false;
    }

    public function is_period_colide_with_periods(array $period,
        array $unavailablePeriods): bool
    {
        foreach ($unavailablePeriods as $unavailablePeriod) {
            $unavailablePeriod['date_end'] = $unavailablePeriod['date_end']
                ?? '9999-12-31';
            if ($this->is_colide_period($period, $unavailablePeriod)) {
                return true;
            }
        }
        return false;
    }

    public function is_colide_period(array $period, array $otherPeriod): bool
    {
        if (!isset($period['date_begin'], $otherPeriod['date_begin'])) {
            return false;
        }
        $p1 = time::parse($period['date_begin']);
        $p2 = time::parse($period['date_end']);
        $o1 = time::parse($otherPeriod['date_begin']);
        $o2 = time::parse($otherPeriod['date_end']);
        $periodIdBeforeOther = ($p1->isBefore($o1) and $p2->isBefore($o1));
        $periodIdAfterOther = ($p1->isAfter($o2) and $p2->isAfter($o2));
        $isNotColide = ($periodIdBeforeOther or $periodIdAfterOther);
        return !$isNotColide;
    }

    public function is_available_period(int $timUserId, array $period,
            ?int $planningId=null): bool
    {
        if (strlen($period['date_end']) == 0) {
            $period['date_end'] = '9999-12-31';
        }
        $unavailableDates = $this->get_unavailable_period($timUserId,
            $planningId);
        return !$this->is_period_colide_with_periods($period,
            $unavailableDates);
    }

    public function is_available_date(int $timUserId, String $date,
            ?int $planningId=null): bool
    {
        if (strlen($date) == 0) {
            $date = '9999-12-31';
        }
        $unavailableDates = $this->get_unavailable_period($timUserId,
            $planningId);
        return !$this->is_date_in_period_list($date, $unavailableDates);
    }

    public function get_user_planning_id(int $planningId): ?int
    {
        return $this->select('id_user_planning')
            ->join_planning_and_user_planning()
            ->find($planningId)['id_user_planning'] ?? null;
    }

    public function get_tim_user_id(int $planningId): ?int
    {
        return $this->select('id_user')
            ->join_planning_and_user_planning()
            ->withDeleted()
            ->find($planningId)['id_user'] ?? null;
    }

    public function insert_planning_times_and_dates(int $timUserId,
        array $times, array $dates): void
    {
        $this->db->transStart();
        $this->insert($times);
        $values['id_planning'] = $this->selectMax('id_planning')
            ->first();
        $values['id_user'] = $timUserId;
        $values = array_merge($values, $dates);
        $this->db->table('user_planning')
            ->insert($values);
        $this->db->transComplete();
    }

    public function update_planning_times_and_dates(int $planningId,
        array $times, array $dates): void
    {
        $this->db->transStart();
        $this->update($planningId, $times);
        $this->db->table('user_planning')
            ->where('id_user_planning = ',
                $this->get_user_planning_id($planningId))
            ->update($dates);
        $this->db->transComplete();
    }

    public function get_day_string(?string $carry, string $time): string
    {
        return $carry . '%s ' . substr($time, 0, 5) . ' ';
    }

    public function get_name_day_string(): array
    {
        $nameDay[0] = ucfirst(lang('tim_lang.monday'));
        $nameDay[1] = ucfirst(lang('tim_lang.tuesday'));
        $nameDay[2] = ucfirst(lang('tim_lang.wednesday'));
        $nameDay[3] = ucfirst(lang('tim_lang.thursday'));
        $nameDay[4] = ucfirst(lang('tim_lang.friday'));
        return $nameDay;
    }



    public function format_due_planning(array $duePlanning): string
    {
        $txt = array_reduce($duePlanning, array($this, 'get_day_string'));
        $dayLabel = array_map(function($string) {
            return substr($string, 0, 2);
        }, $this->get_name_day_string());
        return vsprintf($txt, $dayLabel);
    }

    public function add_due_string(array $idAndOther): array
    {
        $idAndOther['due_time'] = $this->get_string(
                $idAndOther['id_planning']);
        return $idAndOther;
    }

    public function get_data_list_user_planning(int $timUserId,
            bool $withDeleted): array
    {
        $idsAndDates = $this->get_date_and_id($timUserId, $withDeleted);
        return array_map(array($this, 'add_due_string'), $idsAndDates);
    }

    public function get_string(int $planningId): string
    {
        $duePlanning = $this->get_due_plannings($planningId);
        return $this->format_due_planning($duePlanning);
    }

    public function is_deleted(?int $planningId): bool
    {
        if (is_null($planningId)) {
            return false;
        }
        $data = $this->select('date_delete')
                ->withDeleted()
                ->find($planningId);
        return isset($data['date_delete']);

    }

    public function get_planning_time_day(string $date, int $timUserId): array
    {
        $columns = $this->get_columns_day_name($date);
        $date = $this->db->escape($date);
        $timUserId = $this->db->escape($timUserId);
        $where = "(date_begin <= $date) AND ((date_end >= $date) OR "
                . "(date_end IS NULL)) AND (id_user = $timUserId)";
        $this->join_planning_and_user_planning();
        $columnsData = $this->select("$columns[0],  $columns[1]")
                ->where($where)
                ->first();
        $keys = array_keys($columnsData);
        return array($columnsData[$keys[0]], $columnsData[$keys[1]]);
    }

    public function get_balance(string $date, int $timUserId)
    {
        $planning_time = $this->get_planning_time_day($date, $timUserId);
    }

    # to edit
    public function convert_second(array $columnsData): array
    {
        $keys = array_keys($columnsData);
        $dueTime = Time::parse($columnsData[$keys[0]]);
        $offeredTime = Time::parse($columnsData[$keys[1]]);
        return array($dueTime, $offeredTime);
    }

    public function get_columns_day_name(string $date): array
    {
        $dayOfWeek = Time::parse($date)->dayOfWeek;
        $label[1][0] = 'due_time_monday';
        $label[1][1] = 'offered_time_monday';
        $label[2][0] = 'due_time_tuesday';
        $label[2][1] = 'offered_time_tuesday';
        $label[3][0] = 'due_time_wednesday';
        $label[3][1] = 'offered_time_wednesday';
        $label[4][0] = 'due_time_thursday';
        $label[4][1] = 'offered_time_thursday';
        $label[5][0] = 'due_time_friday';
        $label[5][1] = 'offered_time_friday';
        return $label[$dayOfWeek];


    }

   


}
