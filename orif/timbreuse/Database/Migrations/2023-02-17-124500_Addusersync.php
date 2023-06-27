<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class Addusersync extends Migration
{
	public function up()
	{
		$field['id_user']['type'] = 'INT';
		$field['id_user']['auto_increment'] = true;
		$field['id_user']['null'] = false;

		$field['name']['type'] = 'TEXT';
		$field['name']['null'] = false;

		$field['surname']['type'] = 'TEXT';
		$field['surname']['null'] = false;

		$field[0] = 'date_modif DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';

		$field['date_delete']['type'] = 'DATETIME';
		$field['date_delete']['null'] = true;

		$this->forge->addField($field);
		$this->forge->addPrimaryKey('id_user');
        $this->forge->createTable('user_sync');
	}

	public function down()
	{
        $this->forge->dropTable('user_sync');
	}
}
