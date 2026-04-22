<?php

namespace Timbreuse\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class AddEventType extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => false,
            ],
            'user_sync_id' => [
                'type' => 'INT',
                'null' => true,
            ],
            'badge_number' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('created_at');
        $this->forge->addKey('type');
        $this->forge->addKey('user_sync_id');
        $this->forge->addKey('badge_number');
        $this->forge->createTable('event_type', true);
    }

    public function down()
    {
        $this->forge->dropTable('event_type', true);
    }
}

