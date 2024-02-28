<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserGroupTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'fk_user_group_id' => [
                'type'              => 'INT',
                'unsigned'          => true
            ],
            'name' => [
                'type'              => 'VARCHAR',
                'constraint'        => '45'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fk_user_group_id', 'user_group', 'id');
        $this->forge->createTable('user_group', true);
    }

    public function down()
    {
        $this->forge->dropForeignKey('user_group', 'user_group_fk_user_group_id_foreign');

        $this->forge->dropTable('user_group', true);
    }
}
