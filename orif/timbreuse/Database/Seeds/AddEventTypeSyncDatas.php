<?php

namespace Timbreuse\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddEventTypeSyncDatas extends Seeder
{
    public function run()
    {
        $builder = $this->db->table('event_type');
        $exists = $builder->where('type', 'hard_delete')->countAllResults();
        if ($exists > 0) {
            return;
        }

        $builder->insert([
            'type' => 'hard_delete',
            'user_sync_id' => null,
            'badge_number' => null,
        ]);
    }
}

