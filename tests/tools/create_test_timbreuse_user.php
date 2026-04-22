<?php

declare(strict_types=1);

/**
 * Create (or update) the local Timbreuse test user and ensure it is linked
 * to a Timbreuse profile visible in /Users.
 *
 * Usage:
 *   php tests/tools/create_test_timbreuse_user.php
 */

const TEST_USER_TYPE = 2; // registered user
const TEST_USERS = [
    [
        'username' => 'e2e_badge_deleted_u1',
        'password' => 'TimbreuseE2E123!',
        'name' => 'Integration',
        'surname' => 'E2E_User1',
    ],
    [
        'username' => 'e2e_badge_deleted_u2',
        'password' => 'TimbreuseE2E123!',
        'name' => 'Integration',
        'surname' => 'E2E_User2',
    ],
    [
        'username' => 'e2e_badge_deleted_u3',
        'password' => 'TimbreuseE2E123!',
        'name' => 'Integration',
        'surname' => 'E2E_User3',
    ],
];

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

    // Useful fallback when running from host OS with dockerized MariaDB.
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

function getUserIdByUsername(mysqli $db, string $username): ?int
{
    $stmt = $db->prepare('SELECT id FROM user WHERE username = ? LIMIT 1');
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (getUserIdByUsername): ' . $db->error);
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    return $row ? (int) $row['id'] : null;
}

function createOrUpdateCiUser(mysqli $db, array $testUser): int
{
    $existingId = getUserIdByUsername($db, $testUser['username']);
    $hash = password_hash($testUser['password'], PASSWORD_BCRYPT);
    if ($hash === false) {
        throw new RuntimeException('Impossible de hasher le mot de passe.');
    }

    if ($existingId !== null) {
        $stmt = $db->prepare(
            'UPDATE user SET fk_user_type = ?, password = ?, archive = NULL WHERE id = ?'
        );
        if (!$stmt) {
            throw new RuntimeException('Prepare failed (update user): ' . $db->error);
        }
        $type = TEST_USER_TYPE;
        $stmt->bind_param('isi', $type, $hash, $existingId);
        if (!$stmt->execute()) {
            throw new RuntimeException('Update user failed: ' . $stmt->error);
        }
        echo 'User web mis a jour (id=' . $existingId . ').' . PHP_EOL;
        return $existingId;
    }

    $stmt = $db->prepare(
        'INSERT INTO user (fk_user_type, username, password, email, archive) VALUES (?, ?, ?, NULL, NULL)'
    );
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (insert user): ' . $db->error);
    }
    $type = TEST_USER_TYPE;
    $username = $testUser['username'];
    $stmt->bind_param('iss', $type, $username, $hash);
    if (!$stmt->execute()) {
        throw new RuntimeException('Insert user failed: ' . $stmt->error);
    }

    $newId = (int) $db->insert_id;
    echo 'User web cree (id=' . $newId . ').' . PHP_EOL;
    return $newId;
}

function ensureLinkedTimProfile(mysqli $db, int $ciUserId, array $testUser): int
{
    $stmt = $db->prepare('SELECT id_user FROM access_tim_user WHERE id_ci_user = ? LIMIT 1');
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (check link): ' . $db->error);
    }
    $stmt->bind_param('i', $ciUserId);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    if ($row && !empty($row['id_user'])) {
        $timId = (int) $row['id_user'];
        echo 'Lien Timbreuse deja present (id_user=' . $timId . ').' . PHP_EOL;
        return $timId;
    }

    $stmt = $db->prepare('INSERT INTO user_sync (name, surname) VALUES (?, ?)');
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (insert user_sync): ' . $db->error);
    }
    $name = $testUser['name'];
    $surname = $testUser['surname'];
    $stmt->bind_param('ss', $name, $surname);
    if (!$stmt->execute()) {
        throw new RuntimeException('Insert user_sync failed: ' . $stmt->error);
    }
    $timUserId = (int) $db->insert_id;
    echo 'Profil Timbreuse cree (id_user=' . $timUserId . ').' . PHP_EOL;

    $stmt = $db->prepare('INSERT INTO access_tim_user (id_user, id_ci_user) VALUES (?, ?)');
    if (!$stmt) {
        throw new RuntimeException('Prepare failed (insert access_tim_user): ' . $db->error);
    }
    $stmt->bind_param('ii', $timUserId, $ciUserId);
    if (!$stmt->execute()) {
        throw new RuntimeException('Insert access_tim_user failed: ' . $stmt->error);
    }
    echo 'Lien access_tim_user cree.' . PHP_EOL;

    return $timUserId;
}

function run(): void
{
    $cfg = loadDbConfig(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env');
    $db = connectDb($cfg);

    $db->begin_transaction();
    try {
        $results = [];
        foreach (TEST_USERS as $testUser) {
            $ciUserId = createOrUpdateCiUser($db, $testUser);
            $timUserId = ensureLinkedTimProfile($db, $ciUserId, $testUser);
            $results[] = [
                'username' => $testUser['username'],
                'password' => $testUser['password'],
                'ciUserId' => $ciUserId,
                'timUserId' => $timUserId,
            ];
        }
        $db->commit();

        echo PHP_EOL;
        echo 'OK - comptes test prets:' . PHP_EOL;
        foreach ($results as $result) {
            echo '- username: ' . $result['username'] . PHP_EOL;
            echo '  password: ' . $result['password'] . PHP_EOL;
            echo '  user.id: ' . $result['ciUserId'] . PHP_EOL;
            echo '  user_sync.id_user: ' . $result['timUserId'] . PHP_EOL;
        }
    } catch (Throwable $e) {
        $db->rollback();
        fwrite(STDERR, 'Erreur: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}

run();
