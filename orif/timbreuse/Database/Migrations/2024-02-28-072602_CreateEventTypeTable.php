<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventTypeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'name' => [
                'type'              => 'VARCHAR',
                'constraint'        => '45'
            ],
            'is_group_event' => [
                'type'              => 'BOOLEAN'
            ],
            'is_personal_event' => [
                'type'              => 'BOOLEAN'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('event_type', true);
    }

    public function down()
    {
        $this->forge->dropForeignKey('event_planning', 'event_planning_fk_user_group_id_foreign');
        $this->forge->dropForeignKey('event_planning', 'event_planning_fk_user_sync_id_foreign');

        $this->forge->dropTable('event_planning', true);
    }
}
