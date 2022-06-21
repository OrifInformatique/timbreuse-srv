<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

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

    public function get_filtered_logs($userId, $date, $period) {
        $dbBadge = $this->builder('badge');
        $this->whereIn('id_badge', function () use ($dbBadge, $userId) {
            return $dbBadge->select('id_badge')->where('id_user', $userId);

        });
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

}