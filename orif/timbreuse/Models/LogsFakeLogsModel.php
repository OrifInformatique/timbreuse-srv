<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class LogsFakeLogsModel extends LogsModel {
    protected $table = 'log_fake_log';
    protected $primaryKey ='id_fake_log';

    public function get_logs($userId = null)
    {
        if ($userId === null) {
            return $this->findAll();
        } else {
            return $this->where('id_user', $userId)
                ->findAll();
        }
    }

    public function where_id_badge($userId)
    {
        $this->where('id_user', $userId);
    }


}