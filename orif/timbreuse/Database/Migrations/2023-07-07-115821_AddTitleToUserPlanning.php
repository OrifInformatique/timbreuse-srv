<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTitleToUserPlanning extends Migration
{
    public function up()
    {
		$field['title']['type'] = 'VARCHAR';
		$field['title']['null'] = true;
		$field['title']['constraint'] = 255;

        $this->forge->addColumn('user_planning', $field);
    }

    public function down()
    {
        $this->forge->dropColumn('user_planning', 'title');
    }
}
