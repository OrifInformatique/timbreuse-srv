<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class LogsFakeLogsModel extends LogsModel {
    protected $table = 'log_fake_log';
    protected $primaryKey ='id_fake_log';

    /**
     * @deprecated 
     */
    public function __construct()
    {
        trigger_error(
            'Method ' . __METHOD__ . ' is deprecated',
            E_USER_DEPRECATED
        ); 
    }

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