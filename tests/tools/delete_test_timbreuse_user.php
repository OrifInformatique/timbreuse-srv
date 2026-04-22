<?php

declare(strict_types=1);

/**
 * Delete the local Timbreuse test user and related links/profile.
 *
 * Usage:
 *   php tests/tools/delete_test_timbreuse_user.php
 */

const TEST_USERNAME = 'test_timbreuse';

function readEnvValue(string $key, array $lines): ?string
{
    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        if (!str_contains($trimmed, '=')) {
            continue;
        }

        [$k, $v] = explode('=', $trimmed, 2);
        if (trim($k) !== $key) {
            continue;
        }

        $value = trim($v);
        if (
            (str_starts_with($value, "'") && str_ends_with($value, "'")) ||
            (str_starts_with($value, '"') && str_ends_with($value, '"'))
        ) {
            $value = substr($value, 1, -1);
        }

        return $value;
    }

    return null;
}

function loadDbConfig(string $envPath): array
{
    if (!is_file($envPath)) {
        throw new RuntimeException(".env introuvable: {$envPath}");
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        throw new RuntimeException("Impossible de lire {$envPath}");
    }

    $host = readEnvValue('database.default.hostname', $lines) ?? '127.0.0.1';
    $port = (int) (readEnvValue('database.default.port', $lines) ?? '3306');
    $dbName = readEnvValue('database.default.database', $lines) ?? 'ci4';
    $user = readEnvValue('database.default.username', $lines) ?? 'root';
    $pass = readEnvValue('database.default.password', $lines) ?? '';

    return [
        'host' => $host,
        'port' => $port,
        'database' => $dbName,
        'username' => $user,
        'password' => $pass,
    ];
}

function connectDb(array $cfg): mysqli
{
    mysqli_report(MYSQLI_REPORT_OFF);

    $attempts = [
        [$cfg['host'], (int) $cfg['port'], 'config .env'],
    ];
    if ($cfg['host'] === 'mariadb') {
        $attempts[] = ['127.0.0.1', 3307, 'fallback docker host'];
    }

    $lastError = 'unknown';
    foreach ($attempts as [$host, $port, $label]) {
        $db = @new mysqli($host, $cfg['username'], $cfg['password'], $cfg['database'], $port);
        if (!$db->connect_error) {
            $db->set_charset('utf8mb4');
            echo "Connexion DB OK ({$label}: {$host}:{$port})" . PHP_EOL;
            return $db;
        }
        $lastError = $db->connect_error;
    }

    throw new RuntimeException('Connexion DB impossible: ' . $lastError);
}

function run(): void
{
    $cfg = loadDbConfig(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env');
    $db = connectDb($cfg);

    $stmt = $db->prepare('SELECT id FROM user WHERE username = ? LIMIT 1');
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (select user): ' . $db->error);
    }
    $username = TEST_USERNAME;
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res ? $res->fetch_assoc() : null;

    if (!$user) {
        echo 'Aucune suppression: user "' . TEST_USERNAME . '" absent.' . PHP_EOL;
        return;
    }

    $ciUserId = (int) $user['id'];
    echo 'User web trouve (id=' . $ciUserId . ').' . PHP_EOL;

    $db->begin_transaction();
    try {
        $timUserIds = [];
        $stmt = $db->prepare('SELECT id_user FROM access_tim_user WHERE id_ci_user = ?');
        if (!$stmt) {
            throw new RuntimeException('Prepare failed (select access links): ' . $db->error);
        }
        $stmt->bind_param('i', $ciUserId);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res ? $res->fetch_assoc() : null) {
            if (!$row) {
                break;
            }
            $timUserIds[] = (int) $row['id_user'];
        }

        $stmt = $db->prepare('DELETE FROM access_tim_user WHERE id_ci_user = ?');
        if (!$stmt) {
            throw new RuntimeException('Prepare failed (delete access links): ' . $db->error);
        }
        $stmt->bind_param('i', $ciUserId);
        if (!$stmt->execute()) {
            throw new RuntimeException('Delete access links failed: ' . $stmt->error);
        }

        foreach ($timUserIds as $timUserId) {
            // Avoid FK issues by unlinking badges and deleting linked records first.
            $stmt = $db->prepare('UPDATE badge_sync SET id_user = NULL WHERE id_user = ?');
            if (!$stmt) {
                throw new RuntimeException('Prepare failed (badge unlink): ' . $db->error);
            }
            $stmt->bind_param('i', $timUserId);
            if (!$stmt->execute()) {
                throw new RuntimeException('Badge unlink failed: ' . $stmt->error);
            }

            $stmt = $db->prepare('DELETE FROM log_sync WHERE id_user = ?');
            if (!$stmt) {
                throw new RuntimeException('Prepare failed (delete log_sync): ' . $db->error);
            }
            $stmt->bind_param('i', $timUserId);
            if (!$stmt->execute()) {
                throw new RuntimeException('Delete log_sync failed: ' . $stmt->error);
            }

            $stmt = $db->prepare('DELETE FROM user_planning WHERE id_user = ?');
            if (!$stmt) {
                throw new RuntimeException('Prepare failed (delete user_planning): ' . $db->error);
            }
            $stmt->bind_param('i', $timUserId);
            if (!$stmt->execute()) {
                throw new RuntimeException('Delete user_planning failed: ' . $stmt->error);
            }

            $stmt = $db->prepare('DELETE FROM user_sync WHERE id_user = ?');
            if (!$stmt) {
                throw new RuntimeException('Prepare failed (delete user_sync): ' . $db->error);
            }
            $stmt->bind_param('i', $timUserId);
            if (!$stmt->execute()) {
                throw new RuntimeException('Delete user_sync failed: ' . $stmt->error);
            }
        }

        $stmt = $db->prepare('DELETE FROM user WHERE id = ?');
        if (!$stmt) {
            throw new RuntimeException('Prepare failed (delete user): ' . $db->error);
        }
        $stmt->bind_param('i', $ciUserId);
        if (!$stmt->execute()) {
            throw new RuntimeException('Delete user failed: ' . $stmt->error);
        }

        $db->commit();
        echo 'OK - user "' . TEST_USERNAME . '" supprime.' . PHP_EOL;
    } catch (Throwable $e) {
        $db->rollback();
        fwrite(STDERR, 'Erreur: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}

run();
