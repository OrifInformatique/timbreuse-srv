<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class FakeLogsModel extends Model {
    protected $table = 'fake_log';
    protected $primaryKey ='id_fake_log';

    public function get_info($fakeLogId=null)
    {
        if ($fakeLogId === null) {
            return $this->select('id_ci_user', 'date', 'date_site', 'inside')
                ->findAll();
        } else {
            return $this->find($fakeLogId);
        }
    }
}