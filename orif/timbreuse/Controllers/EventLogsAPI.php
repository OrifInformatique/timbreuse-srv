<?php

namespace Timbreuse\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\Response;
use Timbreuse\Models\EventTypeModel;

class EventLogsAPI extends BaseController
{
    use ResponseTrait;

    public function get(string $startDate, string $token): Response|string
    {
        helper('UtilityFunctions');
        if ($token != create_token($startDate)) {
            return $this->failUnauthorized();
        }

        $model = model(EventTypeModel::class);
        $model->select(
            'event_type.id, event_type.type, event_type.user_sync_id, '
            . 'user_sync.name AS user_sync_name, user_sync.surname AS user_sync_surname, '
            . 'event_type.badge_number, event_type.created_at'
        );
        $model->join('user_sync', 'user_sync.id_user = event_type.user_sync_id', 'left');
        $model->where('created_at >=', $startDate);
        $model->orderBy('created_at');

        // Contrat API aligne sur le client Timbreuse (`event_log_sync`):
        // id_event, event_type, entity_type, entity_id, payload, date_event.
        $rows = $model->findAll();
        $events = [];
        foreach ($rows as $row) {
            $badgeNumber = $row['badge_number'] ?? null;
            $userSyncId = $row['user_sync_id'] ?? null;

            if ($badgeNumber !== null && $badgeNumber !== '') {
                $entityType = 'badge';
                $entityId = (int) $badgeNumber;
            } elseif ($userSyncId !== null && $userSyncId !== '') {
                $entityType = 'user';
                $entityId = (int) $userSyncId;
            } else {
                $entityType = 'unknown';
                $entityId = 0;
            }

            $payload = [
                'badge_number' => $badgeNumber,
                'user_sync_id' => $userSyncId,
                'user_sync_name' => $row['user_sync_name'] ?? null,
                'user_sync_surname' => $row['user_sync_surname'] ?? null,
            ];

            $events[] = [
                'id_event' => (int) $row['id'],
                'event_type' => $row['type'],
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'date_event' => $row['created_at'],
            ];
        }

        return $this->respond($events);
    }
}

