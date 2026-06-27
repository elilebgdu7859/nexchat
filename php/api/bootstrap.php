<?php
declare(strict_types=1);

$config = require __DIR__ . '/../config/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

function db(): PDO {
    static $pdo;
    global $config;
    if (!$pdo) {
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function input(): array {
    $raw = file_get_contents('php://input') ?: '{}';
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function json_out(array $payload, int $status = 200): void {
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function token_for(array $user): string {
    global $config;
    $payload = base64_encode(json_encode(['uid' => (int)$user['id'], 'exp' => time() + 60 * 60 * 24 * 30]));
    $sig = hash_hmac('sha256', $payload, $config['jwt_secret']);
    return $payload . '.' . $sig;
}

function current_user(): array {
    global $config;
    $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (!preg_match('/Bearer\s+(.+)/', $auth, $m)) json_out(['error' => 'unauthorized'], 401);
    [$payload, $sig] = array_pad(explode('.', $m[1], 2), 2, '');
    if (!hash_equals(hash_hmac('sha256', $payload, $config['jwt_secret']), $sig)) json_out(['error' => 'bad_token'], 401);
    $data = json_decode(base64_decode($payload), true) ?: [];
    if (($data['exp'] ?? 0) < time()) json_out(['error' => 'expired'], 401);
    $stmt = db()->prepare('SELECT id, username, display_name, email, avatar_color, ai_persona, peer_id FROM users WHERE id = ?');
    $stmt->execute([(int)$data['uid']]);
    $user = $stmt->fetch();
    if (!$user) json_out(['error' => 'user_not_found'], 401);
    return $user;
}
