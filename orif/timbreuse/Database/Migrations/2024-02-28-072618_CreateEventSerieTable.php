<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventSerieTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'              => 'INT',
                'unsigned'          => true,
                'auto_increment'    => true
            ],
            'start_date' => [
                'type'              => 'DATE'
            ],
            'end_date' => [
                'type'              => 'DATE'
            ],
            'recurrence_frequency' => [
                'type'              => 'ENUM',
                'constraint'        => [
                    'weekly',
                    'monthly'
                ]
            ],
            'recurrence_interval' => [
                'type'              => 'INT'
            ],
            'days_of_week' => [
                'type'              => 'JSON'
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('event_series', true);
    }

    public function down()
    {
        $this->forge->dropTable('event_series', true);
    }
}
