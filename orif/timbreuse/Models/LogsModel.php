<?php

namespace Timbreuse\Models;

use CodeIgniter\BaseModel;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class LogsModel extends Model
{
    protected $table = 'log_sync';
    protected $primaryKey = 'id_log';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'date', 'id_badge', 'inside', 'id_user',
        'date_badge', 'id_ci_user', 'date_delete',
    ];
    protected $useSoftDeletes = true;

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = 'date_modif';
    protected $deletedField  = 'date_delete';
    protected $dateFormat = 'datetime';



    # the switching check mode in check with badge id is not safe, since 
    # adding user id in log table.
    # this is to comeback in the old check with badge id in log table
    protected $checkWithBadgeId = false; #  must be false


    public function get_logs($idBadge = null)
    {
        if ($idBadge === null) {
            return $this->findAll();
        } else {
            return $this->where('id_badge', $idBadge)
                ->findAll();
        }
    }

    /**
     * prepare query to filtered with current user’s badge id 
     */
    public function where_id_badge(int $userId)
    {
        $modelBadge = model(BadgesModel::class);
        $this->whereIn('id_badge', function () use ($modelBadge, $userId) {
            return $modelBadge->select('id_badge')->where('id_user', $userId);
        });
    }

    public function limit_period(Time $date, string $period)
    {
        if ($period == 'day') {
            $this->where('DAY(date)', $date->getDay());
        }
        if (($period == 'day') or ($period == 'month')) {
            $this->where('MONTH(date)', $date->getMonth());
        }
        if ($period == 'week') {
            $this->where('WEEKOFYEAR(date)', $date->getWeekOfYear());
        }
        $this->where('YEAR(date)', $date->getYear());
    }

    public function limit_user(int $timUserId)
    {
        if ($this->checkWithBadgeId) {
            $this->where_id_badge($timUserId);
        } else {
            $this->where('id_user', $timUserId);
        }
    }

    public function get_day_userlogs_with_deleted($userId, $date): array
    {
        $this->limit_user($userId);
        $this->limit_period($date, 'day');
        $this->orderBy('date');
        $this->withDeleted();
        return $this->findAll();
    }

    public function get_filtered_logs(int $timUserId, time $date,
        string $period): array
    {
        $this->limit_user($timUserId);
        $this->limit_period($date, $period);
        $this->orderBy('date');
        return $this->findAll();
    }

    public function get_border_interval($date, $halfDay): array
    {
        switch ($halfDay) {
            case 'morning':
                $border['startTime'] = $date->setHour(7)->setMinute(0)
                    ->setSecond(0);
                $border['endTime'] = $date->setHour(12)->setMinute(30)
                    ->setSecond(0);
                break;
            case 'afternoon':
                $border['startTime'] = $date->setHour(12)->setMinute(30)
                    ->setSecond(0);
                $border['endTime'] = $date->setHour(17)->setMinute(45)
                    ->setSecond(0);
                break;
        }
        return $border;
    }

    /**
     * period is morning and afternoon of a day
     */
    public function get_logs_by_period($userId, $date, $halfDay): array
    {
        $this->limit_user($userId);
        $border = $this->get_border_interval($date, $halfDay);
        $this->where('date >=', $border['startTime']);
        $this->where('date <', $border['endTime']);
        $this->orderBy('date');
        return $this->findAll();
    }

    public function get_border_log_by_period($userId, $date, $halfDay,
        $last = false): array 
    {
        $this->limit_user($userId);
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
        $items = $this->findAll();
        if (isset($items[0])) {
            return $items[0];
        } 
        return array();
    }

    public function is_replicate($date, $badgeId, $inside)
    {
        $this->where('date =', $date);
        $this->where('id_badge =', $badgeId);
        $this->where('inside =', $inside);
        return boolval($this->findAll());
    }

    
}
