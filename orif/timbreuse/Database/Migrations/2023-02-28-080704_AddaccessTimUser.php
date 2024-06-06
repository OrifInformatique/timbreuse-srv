<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddaccessTimUser extends Migration
{
	public function up()
	{
		$field['id_access']['type'] = 'INT';
		$field['id_access']['auto_increment'] = true;
		$field['id_access']['null'] = false;

		$field['id_user']['type'] = 'INT';
		$field['id_user']['null'] = false;

		$field['id_ci_user']['type'] = 'INT';
		$field['id_ci_user']['unsigned'] = true;
		$field['id_ci_user']['null'] = false;

		$this->forge->addField($field);
		$this->forge->addPrimaryKey('id_access');
		$this->forge->addForeignKey('id_user', 'user_sync', 'id_user');
		$this->forge->addForeignKey('id_ci_user', 'user', 'id');
        $this->forge->createTable('access_tim_user');

		$seeder=\Config\Database::seeder();
        $seeder->call('\Timbreuse\Database\Seeds\AddAccessTimUserDatas');
	}

	public function down()
	{
		$this->forge->dropTable('access_tim_user');
	}
}
