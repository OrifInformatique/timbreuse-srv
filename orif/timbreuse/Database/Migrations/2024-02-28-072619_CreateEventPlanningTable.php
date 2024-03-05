<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventPlanningTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'fk_event_series_id' => [
                'type'              => 'INT',
                'unsigned'          => true,
                'null'              => true
            ],
            'fk_user_group_id' => [
                'type'              => 'INT',
                'unsigned'          => true,
                'null'              => true
            ],
            'fk_user_sync_id' => [
                'type'              => 'INT',
                'null'              => true
            ],
            'fk_event_type_id' => [
                'type'              => 'INT',
                'unsigned'          => true
            ],
            'event_date' => [
                'type'              => 'DATE'
            ],
            'start_time' => [
                'type'              => 'TIME'
            ],
            'end_time' => [
                'type'              => 'TIME'
            ],
            'is_work_time' => [
                'type'              => 'BOOLEAN'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('fk_user_group_id', 'user_group', 'id');
        $this->forge->addForeignKey('fk_user_sync_id', 'user_sync', 'id_user');
        $this->forge->addForeignKey('fk_event_series_id', 'event_series', 'id');
        $this->forge->addForeignKey('fk_event_type_id', 'event_type', 'id');
        $this->forge->createTable('event_planning', true);
    }

    public function down()
    {
        $this->forge->dropForeignKey('event_planning', 'event_planning_fk_event_type_id_foreign');
        $this->forge->dropForeignKey('event_planning', 'event_planning_fk_event_series_id_foreign');
        $this->forge->dropForeignKey('event_planning', 'event_planning_fk_user_group_id_foreign');
        $this->forge->dropForeignKey('event_planning', 'event_planning_fk_user_sync_id_foreign');
        
        $this->forge->dropTable('event_planning', true);
    }
}
