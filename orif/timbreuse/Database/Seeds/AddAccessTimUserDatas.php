<?php

namespace Timbreuse\Database\Seeds;
use CodeIgniter\Database\Seeder;

class AddAccessTimUserDatas extends Seeder
{
    public function run()
    {
        $data = [
            ['id_user'=>1,'id_ci_user'=>1],
            ['id_user'=>2,'id_ci_user'=>2],
        ];
        
        foreach($data as $row)
        $this->db->table('access_tim_user')->insert($row);
    }
}