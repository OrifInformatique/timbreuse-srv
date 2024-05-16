<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserSyncGroupTable extends Migration
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
            'fk_user_sync_id' => [
                'type'              => 'INT'
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fk_user_group_id', 'user_group', 'id');
        $this->forge->addForeignKey('fk_user_sync_id', 'user_sync', 'id_user');
        $this->forge->createTable('user_sync_group', true);
    }

    public function down()
    {
        $this->forge->dropForeignKey('user_sync_group', 'user_sync_group_fk_user_group_id_foreign');
        $this->forge->dropForeignKey('user_sync_group', 'user_sync_group_fk_user_sync_id_foreign');

        $this->forge->dropTable('user_sync_group', true);
    }
}
