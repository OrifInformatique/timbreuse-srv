<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddbadgeSync extends Migration
{
	public function up()
	{
		$field['id_badge']['type'] = 'BIGINT';
		$field['id_badge']['null'] = false;

		$field['id_user']['type'] = 'INT';
		$field['id_user']['null'] = true;

		$field['rowid_badge']['type'] = 'INT';
		$field['rowid_badge']['auto_increment'] = true;
		$field['rowid_badge']['null'] = false;

		$field[0] = 'date_modif DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP';

		$field['date_delete']['type'] = 'DATETIME';
		$field['date_delete']['null'] = true;

		$this->forge->addField($field);
		$this->forge->addPrimaryKey('id_badge');
		$this->forge->addUniqueKey('rowid_badge');
        $this->forge->addForeignKey('id_user', 'user_sync','id_user');
        $this->forge->createTable('badge_sync');
	}

	public function down()
	{
        $this->forge->dropTable('badge_sync');
	}
}
