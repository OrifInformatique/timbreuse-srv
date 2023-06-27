<?php

namespace Timbreuse\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Addplanningdatas extends Seeder
{
	public function run()
	{
		//
        $data['id_planning'] = 1;
        $data['due_time_monday'] = '08:12:00';
        $data['offered_time_monday'] = '00:30:00';
        $data['due_time_tuesday'] = '08:12:00';
        $data['offered_time_tuesday'] = '00:30:00';
        $data['due_time_wednesday'] = '08:12:00';
        $data['offered_time_wednesday'] = '00:30:00';
        $data['due_time_thursday'] = '08:12:00';
        $data['offered_time_thursday'] = '00:30:00';
        $data['due_time_friday'] = '08:12:00';
        $data['offered_time_friday'] = '00:30:00';
        $this->db->table('planning')->insert($data);
	}
}
