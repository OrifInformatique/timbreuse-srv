<?php

namespace Timbreuse\Database\Seeds;
use CodeIgniter\Database\Seeder;

class AddUserSyncDatas extends Seeder
{
    public function run()
    {
        $data = [
            ['id_user'=>1,'name'=>'Admin','surname'=>'Admin'],
            ['id_user'=>2,'name'=>'User','surname'=>'User'],
        ];
        
        foreach($data as $row)
        $this->db->table('user_sync')->insert($row);
    }
}