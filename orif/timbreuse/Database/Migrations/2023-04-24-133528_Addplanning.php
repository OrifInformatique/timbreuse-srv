<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class Addplanning extends Migration
{
	public function up()
	{
		//

		$field['id_planning']['type'] = 'INT';
		$field['id_planning']['auto_increment'] = true;
		$field['id_planning']['null'] = false;

		$field['due_time_monday']['type'] = 'TIME';
		$field['due_time_monday']['default'] = '00:00:00';
		$field['due_time_monday']['null'] = false;

		$field['offered_time_monday']['type'] = 'TIME';
		$field['offered_time_monday']['default'] = '00:00:00';
		$field['offered_time_monday']['null'] = false;

		$field['due_time_tuesday']['type'] = 'TIME';
		$field['due_time_tuesday']['default'] = '00:00:00';
		$field['due_time_tuesday']['null'] = false;

		$field['offered_time_tuesday']['type'] = 'TIME';
		$field['offered_time_tuesday']['default'] = '00:00:00';
		$field['offered_time_tuesday']['null'] = false;

		$field['due_time_wednesday']['type'] = 'TIME';
		$field['due_time_wednesday']['default'] = '00:00:00';
		$field['due_time_wednesday']['null'] = false;

		$field['offered_time_wednesday']['type'] = 'TIME';
		$field['offered_time_wednesday']['default'] = '00:00:00';
		$field['offered_time_wednesday']['null'] = false;

		$field['due_time_thursday']['type'] = 'TIME';
		$field['due_time_thursday']['default'] = '00:00:00';
		$field['due_time_thursday']['null'] = false;

		$field['offered_time_thursday']['type'] = 'TIME';
		$field['offered_time_thursday']['default'] = '00:00:00';
		$field['offered_time_thursday']['null'] = false;

		$field['due_time_friday']['type'] = 'TIME';
		$field['due_time_friday']['default'] = '00:00:00';
		$field['due_time_friday']['null'] = false;

		$field['offered_time_friday']['type'] = 'TIME';
		$field['offered_time_friday']['default'] = '00:00:00';
		$field['offered_time_friday']['null'] = false;

		$this->forge->addField($field);
		$this->forge->addPrimaryKey('id_planning');
        $this->forge->createTable('planning');
        $this->call_seeds();
	}

    public function call_seeds() {
        $seeder = \Config\Database::seeder();
        $seeder->call('\Timbreuse\Database\Seeds\Addplanningdatas');
    }

	public function down()
	{
		//
		$this->forge->dropTable('planning');
	}
}
