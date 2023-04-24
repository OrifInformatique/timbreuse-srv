<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddlogSync extends Migration
{
	public function up()
	{
		$field['date']['type'] = 'DATETIME';
		$field['date']['null'] = false;

		$field['id_badge']['type'] = 'BIGINT';
		$field['id_badge']['null'] = true;

		$field['inside']['type'] = 'TINYINT';
		$field['inside']['null'] = false;

		$field['id_log']['type'] = 'INT';
		$field['id_log']['auto_increment'] = true;
		$field['id_log']['null'] = false;

		$field['id_user']['type'] = 'INT';
		$field['id_user']['null'] = false;

		$field['date_badge']['type'] = 'DATETIME';
		$field['id_user']['null'] = true;

		$field[0] = 'date_modif DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';

		$field['date_delete']['type'] = 'DATETIME';
		$field['date_delete']['null'] = true;

		$field['id_ci_user']['type'] = 'INT';
		$field['id_ci_user']['null'] = true;

		$this->forge->addField($field);
		$this->forge->addPrimaryKey('id_log');
		$this->forge->addForeignKey('id_badge', 'badge_sync', 'id_badge');
		$this->forge->addForeignKey('id_user', 'user_sync', 'id_user');
        $this->forge->createTable('log_sync');
	}

	public function down()
	{
		$this->forge->dropTable('log_sync');
	}
}
