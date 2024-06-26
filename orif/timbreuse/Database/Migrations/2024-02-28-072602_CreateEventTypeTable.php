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
            'is_group_event_type' => [
                'type'              => 'BOOLEAN'
            ],
            'is_personal_event_type' => [
                'type'              => 'BOOLEAN'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('event_type', true);
    }

    public function down()
    {
        $this->forge->dropTable('event_type', true);
    }
}
