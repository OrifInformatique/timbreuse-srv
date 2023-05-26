<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class Adduserplanning extends Migration
{
	public function up()
	{
		//

		$field['id_user_planning']['type'] = 'INT';
		$field['id_user_planning']['auto_increment'] = true;
		$field['id_user_planning']['null'] = false;

		$field['id_user']['type'] = 'INT';
		$field['id_user']['null'] = false;

		$field['id_planning']['type'] = 'INT';
		$field['id_planning']['null'] = false;

        $field[0] = 
            '`date_begin` DATE DEFAULT NULL CHECK (`date_begin` < `date_end`)';
        $field[1] = 
            '`date_end` DATE DEFAULT NULL CHECK (`date_end` > `date_begin`)';

		# $field['date_begin']['type'] = 'DATE';
		# $field['date_begin']['null'] = true;

		# $field['date_end']['type'] = 'DATE';
		# $field['date_end']['null'] = true;

		# $field['date_delete']['type'] = 'DATETIME';
		# $field['date_delete']['null'] = true;

		$this->forge->addField($field);
		$this->forge->addPrimaryKey('id_user_planning');
        $this->forge->addForeignKey('id_user', 'user_sync','id_user');
        $this->forge->addForeignKey('id_planning', 'planning','id_planning');
        $this->forge->createTable('user_planning');
	}

	public function down()
	{
		//
        $this->forge->dropTable('user_planning');
	}
}
