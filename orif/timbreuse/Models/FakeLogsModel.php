<?php
namespace Timbreuse\Models;

use CodeIgniter\Model;

class FakeLogsModel extends Model {
    protected $table = 'fake_log';
    protected $primaryKey ='id_fake_log';
    protected $allowedFields = ['id_user', 'id_ci_user', 'date', 'inside'];
    protected $validationRules = [
            'id_user' => 'required|integer',
            'id_ci_user' => 'required|integer',
            'date' => 'required|valid_date',
    ];

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