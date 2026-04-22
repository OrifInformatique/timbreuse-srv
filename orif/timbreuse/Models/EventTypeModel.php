<?php

namespace Timbreuse\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Validation\ValidationInterface;

class EventTypeModel extends Model
{
    protected $table = 'event_type';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'type',
        'user_sync_id',
        'badge_number',
        'created_at',
    ];

    protected $useAutoIncrement = true;
    protected $useSoftDeletes = false;

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $dateFormat = 'datetime';

    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    public function log_hard_delete(string $entityType, int|string $entityId, array $payload = []): bool
    {
        $userSyncId = null;
        $badgeNumber = null;
        if ($entityType === 'user') {
            $userSyncId = (int) $entityId;
        } elseif ($entityType === 'badge') {
            $badgeNumber = $entityId;
        }

        $data = [
            'type' => 'hard_delete',
            'user_sync_id' => $userSyncId,
            'badge_number' => $badgeNumber,
        ];
        return boolval($this->insert($data));
    }
}

